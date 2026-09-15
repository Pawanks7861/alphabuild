#!/usr/bin/env bash
set -euo pipefail

MYSQL_RUN_DIR="/var/run/mysqld"
MYSQL_SOCKET="${MYSQL_RUN_DIR}/mysqld.sock"
APACHE_PORT="${APACHE_PORT:-8080}"

mkdir -p "${MYSQL_RUN_DIR}"
chown mysql:mysql "${MYSQL_RUN_DIR}" 2>/dev/null || sudo chown mysql:mysql "${MYSQL_RUN_DIR}"

if ! mysqladmin ping --socket="${MYSQL_SOCKET}" -uroot --silent 2>/dev/null \
  && ! sudo mysqladmin ping --socket="${MYSQL_SOCKET}" -uroot --silent 2>/dev/null; then
  echo "Starting MariaDB..."
  if command -v sudo >/dev/null 2>&1; then
    sudo mysqld_safe \
      --datadir=/var/lib/mysql \
      --pid-file="${MYSQL_RUN_DIR}/mysqld.pid" \
      --socket="${MYSQL_SOCKET}" >/tmp/mariadb.log 2>&1 &
  else
    mysqld_safe \
      --datadir=/var/lib/mysql \
      --pid-file="${MYSQL_RUN_DIR}/mysqld.pid" \
      --socket="${MYSQL_SOCKET}" >/tmp/mariadb.log 2>&1 &
  fi

  for _ in $(seq 1 30); do
    if mysqladmin ping --socket="${MYSQL_SOCKET}" -uroot --silent 2>/dev/null \
      || sudo mysqladmin ping --socket="${MYSQL_SOCKET}" -uroot --silent 2>/dev/null; then
      break
    fi
    sleep 1
  done
fi

if ! mysqladmin ping --socket="${MYSQL_SOCKET}" -uroot --silent 2>/dev/null \
  && ! sudo mysqladmin ping --socket="${MYSQL_SOCKET}" -uroot --silent 2>/dev/null; then
  echo "MariaDB failed to start. See /tmp/mariadb.log"
  exit 1
fi

if ! curl -fsS "http://127.0.0.1:${APACHE_PORT}/" >/dev/null 2>&1; then
  echo "Starting PHP dev server on port ${APACHE_PORT}..."
  php -S "127.0.0.1:${APACHE_PORT}" -t /workspace /workspace/.cursor/scripts/php-router.php >/tmp/php-server.log 2>&1 &
  for _ in $(seq 1 20); do
    if curl -fsS "http://127.0.0.1:${APACHE_PORT}/" >/dev/null 2>&1; then
      break
    fi
    sleep 1
  done
fi

if ! curl -fsS "http://127.0.0.1:${APACHE_PORT}/" >/dev/null 2>&1; then
  echo "Web server failed to start. See /tmp/php-server.log"
  exit 1
fi

echo "Services ready: MariaDB (${MYSQL_SOCKET}), Web (http://127.0.0.1:${APACHE_PORT})"
