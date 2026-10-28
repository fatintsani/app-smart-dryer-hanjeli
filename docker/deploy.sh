#!/usr/bin/env bash
# Ubuntu: one release at a time, one migration runner, fail closed on health.
set -Eeuo pipefail
cd "$(dirname "$0")/.."
release_tag=${1:?Usage: bash docker/deploy.sh RELEASE_TAG [--existing]}
[[ "$release_tag" =~ ^[a-zA-Z0-9_][a-zA-Z0-9_.-]{0,127}$ ]] || { echo 'Invalid release tag'; exit 1; }
[[ -f .env ]] || { echo 'Existing production .env is required; it will not be created.'; exit 1; }
[[ ${2:-} == '' || ${2:-} == --existing ]] || { echo 'Unknown option'; exit 1; }
exec 9>.docker-deploy.lock
flock -n 9 || { echo 'Another deployment is running.'; exit 1; }
export IMAGE_TAG="$release_tag"
export APP_ENV_FILE="$PWD/.env"
dc() { docker compose --env-file .env "$@"; }
dc config --quiet
if [[ ${2:-} != --existing ]]; then
    dc build --pull app web
fi
# Check environment and built frontend settings before stopping the current app.
dc run --rm --no-deps --pull never app true
dc run --rm --no-deps --pull never web -t
if [[ -f .docker-release ]]; then
    cp .docker-release .docker-previous-release
fi
dc stop web queue mqtt scheduler app reverb
failed_release() {
    trap - ERR
    dc stop web queue mqtt scheduler || true
    echo 'Deployment failed; web and processing workers stopped. Inspect compose ps/logs and recover explicitly. No schema rollback was attempted.' >&2
    exit 1
}
trap failed_release ERR
dc up -d --no-deps --no-build --pull never --wait --wait-timeout 180 db
# Back up the quiescent database, even for first deployment. Never seed accounts.
bash docker/database.sh backup
# Always recreate this one-shot service for each deployment.
dc up -d --no-deps --no-build --pull never --force-recreate migrate
migration_id=$(dc ps -a -q migrate)
migration_status=$(docker wait "$migration_id")
if [[ "$migration_status" != 0 ]]; then
    echo 'Migration failed; see compose logs migrate.'
    failed_release
fi
dc up -d --no-build --pull never --force-recreate --wait --wait-timeout 240 app reverb web queue mqtt scheduler
dc exec -T app php docker/smoke.php
printf '%s\n' "$release_tag" > .docker-release
echo "Release healthy: $release_tag"
