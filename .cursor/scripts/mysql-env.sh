#!/usr/bin/env bash

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
MYSQL_RUN_DIR="${MYSQL_RUN_DIR:-${ROOT_DIR}/.cursor/dev/mysql-run}"
MYSQL_DATADIR="${MYSQL_DATADIR:-${ROOT_DIR}/.cursor/dev/mysql-data}"
MYSQL_SOCKET="${MYSQL_SOCKET:-${MYSQL_RUN_DIR}/mysqld.sock}"
MYSQL_PID_FILE="${MYSQL_PID_FILE:-${MYSQL_RUN_DIR}/mysqld.pid}"

if [[ -f "${MYSQL_RUN_DIR}/socket" ]]; then
  candidate_socket="$(tr -d '[:space:]' < "${MYSQL_RUN_DIR}/socket")"
  if [[ -n "${candidate_socket}" ]] && { \
    mysqladmin ping --socket="${candidate_socket}" -uubuntu --silent 2>/dev/null \
    || mysqladmin ping --socket="${candidate_socket}" -uroot --silent 2>/dev/null \
    || { command -v sudo >/dev/null 2>&1 && sudo mysqladmin ping --socket="${candidate_socket}" -uroot --silent 2>/dev/null; }; \
  }; then
    MYSQL_SOCKET="${candidate_socket}"
  fi
fi

export ROOT_DIR MYSQL_RUN_DIR MYSQL_DATADIR MYSQL_SOCKET MYSQL_PID_FILE

mysql_try() {
  if "$@" 2>/dev/null; then
    return 0
  fi

  if command -v sudo >/dev/null 2>&1; then
    sudo "$@" 2>/dev/null
  else
    return 1
  fi
}

mysql_admin_exec() {
  if [[ -d "${MYSQL_DATADIR}/mysql" ]]; then
    mysql_try mysql --socket="${MYSQL_SOCKET}" -uubuntu "$@" && return 0
  fi

  mysql_try mysql --socket="${MYSQL_SOCKET}" -uroot "$@" && return 0
  mysql_try mysql --socket="${MYSQL_SOCKET}" "$@"
}
