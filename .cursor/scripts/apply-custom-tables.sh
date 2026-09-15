#!/usr/bin/env bash
set -euo pipefail

DB_NAME="${PERFEX_DB_NAME:-alphabuild}"
MYSQL_SOCKET="/var/run/mysqld/mysqld.sock"

mysql_exec() {
  if mysql --socket="${MYSQL_SOCKET}" -uroot "$@" 2>/dev/null; then
    return 0
  fi
  sudo mysql --socket="${MYSQL_SOCKET}" "$@"
}

if mysql_exec -N -e "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='${DB_NAME}' AND table_name='tblforms';" | grep -q '^1$'; then
  echo "Custom tables already present."
  exit 0
fi

echo "Creating AlphaBuild custom tables..."
mysql_exec "${DB_NAME}" < "$(dirname "$0")/../dev/custom-tables.sql"
