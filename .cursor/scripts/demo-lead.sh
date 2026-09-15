#!/usr/bin/env bash
set -euo pipefail

source "$(dirname "$0")/mysql-env.sh"

APP_URL="${PERFEX_APP_URL:-http://127.0.0.1:8080/}"
COOKIE_JAR="/tmp/perfex-demo-cookies.txt"
LEAD_NAME="Cloud Agent Test Lead"

curl -sS -c "${COOKIE_JAR}" -b "${COOKIE_JAR}" \
  -X POST "${APP_URL}admin/authentication" \
  -d "email=admin@example.com" \
  -d "password=admin123456" \
  -o /dev/null

STATUS_ID="$(mysql --socket="${MYSQL_SOCKET}" -uroot -N -e "SELECT id FROM alphabuild.tblleads_status ORDER BY statusorder LIMIT 1;" 2>/dev/null || sudo mysql --socket="${MYSQL_SOCKET}" -N -e "SELECT id FROM alphabuild.tblleads_status ORDER BY statusorder LIMIT 1;")"
SOURCE_ID="$(mysql --socket="${MYSQL_SOCKET}" -uroot -N -e "SELECT id FROM alphabuild.tblleads_sources ORDER BY id LIMIT 1;" 2>/dev/null || sudo mysql --socket="${MYSQL_SOCKET}" -N -e "SELECT id FROM alphabuild.tblleads_sources ORDER BY id LIMIT 1;")"

RESPONSE="$(curl -sS -b "${COOKIE_JAR}" \
  -X POST "${APP_URL}admin/leads/lead" \
  -d "name=${LEAD_NAME}" \
  -d "title=Cloud Agent Test" \
  -d "email=cloud-agent@example.com" \
  -d "status=${STATUS_ID}" \
  -d "source=${SOURCE_ID}")"

echo "${RESPONSE}"

LEAD_COUNT="$(mysql --socket="${MYSQL_SOCKET}" -uroot -N -e "SELECT COUNT(*) FROM alphabuild.tblleads WHERE name='${LEAD_NAME}';" 2>/dev/null || sudo mysql --socket="${MYSQL_SOCKET}" -N -e "SELECT COUNT(*) FROM alphabuild.tblleads WHERE name='${LEAD_NAME}';")"
echo "Lead rows matching name: ${LEAD_COUNT}"
