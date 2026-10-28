#!/usr/bin/env bash
set -Eeuo pipefail
cd "$(dirname "$0")/.."
dc() { docker compose --env-file "${APP_ENV_FILE:-.env}" "$@"; }
case ${1:-} in
    backup)
        umask 0077
        backup_dir=${BACKUP_DIR:-backups}
        mkdir -p "$backup_dir"
        backup_file="$backup_dir/dryer-$(date -u +%Y%m%dT%H%M%SZ)-$$.sql.gz"
        # Password stays in the container environment, never in command arguments.
        dc exec -T db sh -c 'MYSQL_PWD="$MYSQL_PASSWORD" exec mysqldump --user="$MYSQL_USER" --single-transaction --quick --no-tablespaces --set-gtid-purged=OFF --skip-add-locks "$MYSQL_DATABASE"' | gzip > "$backup_file.partial"
        gzip -t "$backup_file.partial"
        mv "$backup_file.partial" "$backup_file"
        echo "Database backup: $backup_file"
        ;;
    restore)
        backup_file=${2:?Usage: bash docker/database.sh restore BACKUP.sql.gz}
        [[ ${CONFIRM_DATABASE_RESTORE:-} == dryer ]] || { echo 'Stop writers, verify target and backup, then set CONFIRM_DATABASE_RESTORE=dryer.'; exit 1; }
        gzip -t "$backup_file"
        gzip -dc "$backup_file" | dc exec -T db sh -c 'MYSQL_PWD="$MYSQL_PASSWORD" exec mysql --user="$MYSQL_USER" "$MYSQL_DATABASE"'
        echo 'Restore complete; verify migration compatibility before restarting writers.'
        ;;
    *) echo 'Usage: bash docker/database.sh backup | restore BACKUP.sql.gz'; exit 1 ;;
esac
