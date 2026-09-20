#!/bin/bash

# ---------------------------------------------------------------------------
# DEPLOYMENT SCRIPT — EXAMPLE / TEMPLATE
#
# 1. Copy this file to deploy.sh:
#       cp deploy-example.sh deploy.sh
#
# 2. Fill in your server details below.
#
# 3. Make it executable:
#       chmod +x deploy.sh
#
# 4. Run it from the project root:
#       ./deploy.sh
#
# deploy.sh is gitignored — your credentials will never be committed.
#
# NOTE: vendor/ and kirby/ are excluded from the upload. Composer runs on
# the server after each deploy to install dependencies for the server's
# PHP version. See README for first-time server setup instructions.
# ---------------------------------------------------------------------------

SSH_USER="your-user"                     # SSH username on the server
SSH_HOST="your-server.com"               # server hostname or IP
REMOTE_PATH="/var/www/your-site"         # absolute path to the site root on the server
SSH_PORT=22                              # change if your server uses a non-standard port
PHP_BIN="/usr/bin/php"                   # path to PHP binary on the server (e.g. /usr/local/php83/bin/php on DreamHost)
COMPOSER_BIN="~/composer"                # path to composer on the server
# ---------------------------------------------------------------------------

set -e

# Failsafe: rsync below overwrites the server's content/ (pages + uploads) with
# the local copy, so any edit made in the live Panel since the last ./pull.sh
# would be lost. Default answer is "no".
echo "⚠  This deploy will OVERWRITE the content on ${SSH_HOST} with your LOCAL content."
echo "   Pages or images edited in the live Panel since your last pull will be lost."
echo
read -r -p "   Are you sure you want to deploy? [y/N]  (n = exit, then run ./pull.sh first) " CONFIRM || CONFIRM=""
if [[ ! "$CONFIRM" =~ ^[Yy]([Ee][Ss])?$ ]]; then
  echo "✗ Deploy cancelled. Run ./pull.sh first to bring the server's content down, review it, then deploy again."
  exit 1
fi
echo

echo "→ Building assets..."
npm run build

echo "→ Deploying to ${SSH_USER}@${SSH_HOST}:${REMOTE_PATH}"
rsync -avz --progress \
  --exclude='.git' \
  --exclude='.gitignore' \
  --exclude='node_modules' \
  --exclude='src/' \
  --exclude='vendor/' \
  --exclude='kirby/' \
  --exclude='deploy.sh' \
  --exclude='deploy-example.sh' \
  --exclude='pull.sh' \
  --exclude='pull-example.sh' \
  --exclude='README.md' \
  --exclude='site/accounts' \
  --exclude='site/sessions' \
  --exclude='site/cache' \
  -e "ssh -p ${SSH_PORT}" \
  ./ "${SSH_USER}@${SSH_HOST}:${REMOTE_PATH}/"

echo "→ Installing dependencies on server..."
ssh -p "${SSH_PORT}" "${SSH_USER}@${SSH_HOST}" \
  "${PHP_BIN} ${COMPOSER_BIN} install --no-dev --optimize-autoloader -d ${REMOTE_PATH}"

echo "→ Setting permissions..."
ssh -p "${SSH_PORT}" "${SSH_USER}@${SSH_HOST}" \
  "chmod -R 775 ${REMOTE_PATH}/content ${REMOTE_PATH}/media ${REMOTE_PATH}/site/cache ${REMOTE_PATH}/site/sessions ${REMOTE_PATH}/site/accounts"

echo "✓ Deploy complete."
