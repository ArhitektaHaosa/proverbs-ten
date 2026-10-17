#!/usr/bin/env bash
# Fails if the temporary domain (or the contact mailbox) is hardcoded outside .env.example and docs/.
# Run from the repo root: scripts/check-domain-agnostic.sh
set -euo pipefail

cd "$(dirname "$0")/.."

PATTERN='admin\.education'

hits=$(grep -RIn --exclude-dir={.git,vendor,node_modules,storage,bootstrap,public/build,docs} \
  --exclude='.env.example' --exclude='check-domain-agnostic.sh' \
  --exclude='AGENTS.md' --exclude='CLAUDE.md' \
  -E "$PATTERN" . || true)

if [ -n "$hits" ]; then
  echo "Hardcoded domain found. Use APP_URL / SITE_NAME / CONTACT_EMAIL instead:"
  echo "$hits"
  exit 1
fi

echo "OK: no hardcoded domain."
