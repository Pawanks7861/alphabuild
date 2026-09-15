#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
cd "${ROOT_DIR}"

if [[ -f package-lock.json ]]; then
  npm ci
else
  npm install
fi

chmod +x .cursor/scripts/*.sh .cursor/scripts/apply-custom-tables.sh
"${ROOT_DIR}/.cursor/scripts/db-bootstrap.sh"

echo "Install complete."
