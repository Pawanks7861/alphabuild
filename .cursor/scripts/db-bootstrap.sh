#!/usr/bin/env bash
set -euo pipefail

source "$(dirname "$0")/mysql-env.sh"

SQL_FILE="${ROOT_DIR}/.cursor/dev/database.sql"
APP_CONFIG="${ROOT_DIR}/application/config/app-config.php"

DB_NAME="${PERFEX_DB_NAME:-alphabuild}"
DB_USER="${PERFEX_DB_USER:-alphabuild}"
DB_PASS="${PERFEX_DB_PASS:-alphabuild}"
DB_HOST="${PERFEX_DB_HOST:-localhost}"
APP_URL="${PERFEX_APP_URL:-http://127.0.0.1:8080/}"
ADMIN_EMAIL="${PERFEX_ADMIN_EMAIL:-admin@example.com}"
ADMIN_PASSWORD="${PERFEX_ADMIN_PASSWORD:-admin123456}"
ENC_KEY="${PERFEX_ENC_KEY:-$(openssl rand -hex 16)}"

mysql_exec() {
  if mysql --socket="${MYSQL_SOCKET}" -uroot "$@" 2>/dev/null; then
    return 0
  fi
  sudo mysql --socket="${MYSQL_SOCKET}" "$@"
}

"${ROOT_DIR}/.cursor/scripts/start-services.sh"
source "$(dirname "$0")/mysql-env.sh"

if [[ ! -f "${SQL_FILE}" ]]; then
  echo "Missing database seed at ${SQL_FILE}"
  exit 1
fi

mysql_exec -e "CREATE DATABASE IF NOT EXISTS \`${DB_NAME}\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql_exec -e "CREATE USER IF NOT EXISTS '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASS}';"
mysql_exec -e "GRANT ALL PRIVILEGES ON \`${DB_NAME}\`.* TO '${DB_USER}'@'localhost';"
mysql_exec -e "FLUSH PRIVILEGES;"

TABLE_COUNT="$(mysql_exec -N -e "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='${DB_NAME}';")"
if [[ "${TABLE_COUNT}" == "0" ]]; then
  echo "Importing Perfex base schema..."
  mysql_exec "${DB_NAME}" < "${SQL_FILE}"
fi

"${ROOT_DIR}/.cursor/scripts/apply-custom-tables.sh"

if [[ ! -f "${APP_CONFIG}" ]]; then
  echo "Creating application/config/app-config.php..."
  cat > "${APP_CONFIG}" <<PHP
<?php

defined('BASEPATH') or exit('No direct script access allowed');

define('APP_BASE_URL', '${APP_URL}');
define('APP_ENC_KEY', '${ENC_KEY}');
define('APP_DB_HOSTNAME', '${DB_HOST}');
define('APP_DB_USERNAME', '${DB_USER}');
define('APP_DB_PASSWORD', '${DB_PASS}');
define('APP_DB_NAME', '${DB_NAME}');
define('APP_DB_CHARSET', 'utf8mb4');
define('APP_DB_COLLATION', 'utf8mb4_unicode_ci');
define('SESS_DRIVER', 'database');
define('SESS_SAVE_PATH', 'sessions');
define('APP_SESSION_COOKIE_SAME_SITE', 'Lax');
define('APP_CSRF_PROTECTION', false);
define('APP_LOG_THRESHOLD', 4);
PHP
fi

php "${ROOT_DIR}/.cursor/scripts/seed-admin.php" \
  --email="${ADMIN_EMAIL}" \
  --password="${ADMIN_PASSWORD}"

CURRENT_VERSION="$(mysql_exec -N -e "SELECT version FROM \`${DB_NAME}\`.tblmigrations LIMIT 1;")"
TARGET_VERSION="316"

if [[ "${CURRENT_VERSION}" != "${TARGET_VERSION}" ]]; then
  echo "Upgrading database from ${CURRENT_VERSION} to ${TARGET_VERSION}..."
  php "${ROOT_DIR}/.cursor/scripts/run-db-upgrade.php"
fi

echo "Database bootstrap complete."
echo "Admin login: ${ADMIN_EMAIL} / ${ADMIN_PASSWORD}"
echo "App URL: ${APP_URL}admin/authentication"
