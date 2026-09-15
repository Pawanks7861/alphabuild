#!/usr/bin/env bash

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
MYSQL_RUN_DIR="${MYSQL_RUN_DIR:-${ROOT_DIR}/.cursor/dev/mysql-run}"
MYSQL_DATADIR="${MYSQL_DATADIR:-${ROOT_DIR}/.cursor/dev/mysql-data}"
MYSQL_SOCKET="${MYSQL_SOCKET:-${MYSQL_RUN_DIR}/mysqld.sock}"
MYSQL_PID_FILE="${MYSQL_PID_FILE:-${MYSQL_RUN_DIR}/mysqld.pid}"

if [[ -f "${MYSQL_RUN_DIR}/socket" ]]; then
  MYSQL_SOCKET="$(cat "${MYSQL_RUN_DIR}/socket")"
fi

export ROOT_DIR MYSQL_RUN_DIR MYSQL_DATADIR MYSQL_SOCKET MYSQL_PID_FILE
