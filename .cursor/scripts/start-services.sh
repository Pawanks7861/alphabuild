#!/usr/bin/env bash
set -euo pipefail

source "$(dirname "$0")/mysql-env.sh"

APACHE_PORT="${APACHE_PORT:-8080}"

mkdir -p "${MYSQL_RUN_DIR}" "${MYSQL_DATADIR}"

mysql_ping() {
  mysqladmin ping --socket="${MYSQL_SOCKET}" -uubuntu --silent 2>/dev/null \
    || mysqladmin ping --socket="${MYSQL_SOCKET}" -uroot --silent 2>/dev/null \
    || sudo mysqladmin ping --socket="${MYSQL_SOCKET}" -uroot --silent 2>/dev/null
}

if mysqladmin ping --socket="/var/run/mysqld/mysqld.sock" -uroot --silent 2>/dev/null \
  || sudo mysqladmin ping --socket="/var/run/mysqld/mysqld.sock" -uroot --silent 2>/dev/null; then
  MYSQL_SOCKET="/var/run/mysqld/mysqld.sock"
  export MYSQL_SOCKET
fi

start_system_mariadb() {
  local system_run_dir="/var/run/mysqld"
  mkdir -p "${system_run_dir}" 2>/dev/null || sudo mkdir -p "${system_run_dir}"
  chown mysql:mysql "${system_run_dir}" 2>/dev/null || sudo chown mysql:mysql "${system_run_dir}" 2>/dev/null || true
  MYSQL_SOCKET="${system_run_dir}/mysqld.sock"
  export MYSQL_SOCKET

  if command -v sudo >/dev/null 2>&1; then
    sudo mysqld_safe \
      --datadir=/var/lib/mysql \
      --pid-file="${system_run_dir}/mysqld.pid" \
      --socket="${MYSQL_SOCKET}" >/tmp/mariadb.log 2>&1 &
  else
    mysqld_safe \
      --datadir=/var/lib/mysql \
      --pid-file="${system_run_dir}/mysqld.pid" \
      --socket="${MYSQL_SOCKET}" >/tmp/mariadb.log 2>&1 &
  fi
}

start_local_mariadb() {
  if [[ ! -d "${MYSQL_DATADIR}/mysql" ]]; then
    echo "Initializing local MariaDB data directory at ${MYSQL_DATADIR}..."
    if command -v mariadb-install-db >/dev/null 2>&1; then
      mariadb-install-db --user="$(whoami)" --datadir="${MYSQL_DATADIR}" >/tmp/mariadb-init.log 2>&1
    else
      mysql_install_db --user="$(whoami)" --datadir="${MYSQL_DATADIR}" >/tmp/mariadb-init.log 2>&1
    fi
  fi

  mysqld_safe \
    --datadir="${MYSQL_DATADIR}" \
    --pid-file="${MYSQL_PID_FILE}" \
    --socket="${MYSQL_SOCKET}" \
    --bind-address=127.0.0.1 \
    --port=3307 >/tmp/mariadb.log 2>&1 &
}

if ! mysql_ping; then
  echo "Starting MariaDB..."
  if [[ -w /var/run ]] && [[ -d /var/lib/mysql ]]; then
    start_system_mariadb || start_local_mariadb
  else
    start_local_mariadb
  fi

  for _ in $(seq 1 45); do
    if mysql_ping; then
      break
    fi
    sleep 1
  done
fi

if ! mysql_ping; then
  echo "MariaDB failed to start. See /tmp/mariadb.log"
  exit 1
fi

web_server_ready() {
  curl -sS --max-time 2 "http://127.0.0.1:${APACHE_PORT}/" >/dev/null 2>&1
}

if ! web_server_ready; then
  echo "Starting PHP dev server on port ${APACHE_PORT}..."
  php -S "127.0.0.1:${APACHE_PORT}" -t /workspace /workspace/.cursor/scripts/php-router.php >/tmp/php-server.log 2>&1 &
  for _ in $(seq 1 20); do
    if web_server_ready; then
      break
    fi
    sleep 1
  done
fi

if ! web_server_ready; then
  echo "Web server failed to start. See /tmp/php-server.log"
  exit 1
fi

echo "${MYSQL_SOCKET}" > "${MYSQL_RUN_DIR}/socket"

echo "Services ready: MariaDB (${MYSQL_SOCKET}), Web (http://127.0.0.1:${APACHE_PORT})"
