#!/usr/bin/env bash
set -euo pipefail

source "$(dirname "$0")/mysql-env.sh"

DB_NAME="${PERFEX_DB_NAME:-alphabuild}"

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

if ! mysql_exec -N -e "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='${DB_NAME}' AND table_name='tbltickets_status';" | grep -q '^1$'; then
  echo "Skipping custom tables until Perfex schema upgrade completes."
  exit 0
fi

echo "Creating AlphaBuild custom tables..."
if ! mysql_exec "${DB_NAME}" < "$(dirname "$0")/../dev/custom-tables.sql"; then
  echo "Warning: custom table bootstrap reported errors; continuing if core tables exist."
fi
