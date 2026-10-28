#!/usr/bin/env bash
# Does not read/write the production .env. Uses a unique Compose project.
set -Eeuo pipefail
cd "$(dirname "$0")/.."
export COMPOSE_PROJECT_NAME="hanjeli-validation-$(date +%s)-$$"
export APP_IMAGE="$COMPOSE_PROJECT_NAME-app" WEB_IMAGE="$COMPOSE_PROJECT_NAME-web" IMAGE_TAG=test
export APP_ENV_FILE
APP_ENV_FILE=$(mktemp /tmp/hanjeli-validation.XXXXXX)
export WEB_PORT=${VALIDATION_WEB_PORT:-18088}
umask 0077
python3 - "$APP_ENV_FILE" <<'PY'
from pathlib import Path
import base64, secrets, sys
values = {
    'APP_KEY': 'base64:' + base64.b64encode(secrets.token_bytes(32)).decode(),
    'DB_PASSWORD': secrets.token_hex(24), 'MYSQL_ROOT_PASSWORD': secrets.token_hex(24),
    'REVERB_APP_ID': 'validation', 'REVERB_APP_KEY': 'validation-public-key',
    'REVERB_APP_SECRET': secrets.token_hex(24), 'MQTT_HOST': 'mqtt-broker',
    'MQTT_PORT': '1883', 'MQTT_USE_TLS': 'false',
}
lines = []
for line in Path('.env.docker.example').read_text().splitlines():
    key = line.split('=', 1)[0]
    lines.append(key + '=' + values[key] if key in values else line)
Path(sys.argv[1]).write_text('\n'.join(lines) + '\nHANJELI_VALIDATION=true\n')
PY
dc() { docker compose --env-file "$APP_ENV_FILE" -f compose.yaml -f docker/compose.validation.yaml "$@"; }
cleanup() {
    result=$?
    trap - EXIT
    dc ps -a || true
    if [[ "$result" != 0 ]]; then
        dc logs --no-color --tail=60 web migrate app mqtt || true
    fi
    # Remove only this script's isolated containers/networks; retain all volumes.
    dc down --timeout 20 || true
    rm -f "$APP_ENV_FILE"
    echo "Retained validation volumes belong to $COMPOSE_PROJECT_NAME"
    exit "$result"
}
trap cleanup EXIT
dc config --quiet
dc build app web
dc up -d --no-build --pull missing --wait --wait-timeout 240
dc exec -T web nginx -t
dc exec -T app sh -c 'test "$(id -u)" -ne 0 && test ! -f .env && test ! -d node_modules && test ! -d vendor/phpunit && test ! -f /usr/local/bin/composer'
echo 'PASS: non-root runtime without production dotenv or development dependencies'
if dc exec -T -e APP_DEBUG=true app php docker/check-environment.php >/dev/null 2>&1; then
    echo 'FAIL: unsafe production configuration was accepted'
    exit 1
fi
echo 'PASS: unsafe production configuration rejected'
dc exec -T app php docker/smoke.php --persist-write
dc exec -T scheduler php artisan schedule:list
if ! dc logs --no-color mqtt 2>&1 | grep -F 'Connected to MQTT Broker successfully' > /dev/null; then
    echo 'FAIL: MQTT worker did not report successful broker connection' >&2
    dc logs --no-color --tail=100 mqtt >&2 || true
    exit 1
fi

echo 'PASS: MQTT worker connected and subscribed to isolated broker'
dc stop web app queue mqtt reverb scheduler
validation_backup_dir=$(mktemp -d /tmp/hanjeli-backup-validation.XXXXXX)
BACKUP_DIR="$validation_backup_dir" bash docker/database.sh backup
validation_backup=$(find "$validation_backup_dir" -name '*.sql.gz' -type f -print -quit)
CONFIRM_DATABASE_RESTORE=dryer bash docker/database.sh restore "$validation_backup"
echo 'PASS: database backup and restore with dedicated application user'
dc up -d --no-deps --no-build --force-recreate --wait --wait-timeout 180 db
dc up -d --no-build --force-recreate --wait --wait-timeout 240 app reverb web queue mqtt scheduler
dc exec -T app php docker/smoke.php --persist-check
docker build --target test --build-arg VITE_REVERB_APP_KEY=validation-public-key -t "$COMPOSE_PROJECT_NAME-tests" .
docker run --rm -e APP_KEY="$(openssl rand -base64 32 | sed 's/^/base64:/')" "$COMPOSE_PROJECT_NAME-tests"
