#!/usr/bin/env bash
set -euo pipefail

# Builds a distributable plugin zip: production vendor deps + built assets, no dev files.
SLUG="${1:?usage: build-release-zip.sh <slug>}"
BUILD_DIR="$(mktemp -d)/${SLUG}"

composer install --no-dev --optimize-autoloader --no-interaction
npm ci && npm run build

mkdir -p "$BUILD_DIR"
rsync -a ./ "$BUILD_DIR/" \
  --exclude='.git*' --exclude='.superpowers' --exclude='.idea' --exclude='node_modules' --exclude='tests' \
  --exclude='changelog' --exclude='bin' --exclude='/build' --exclude='*.dist.xml' \
  --exclude='.wp-env*.json' --exclude='.phpcs.xml' --exclude='.phpcs.tests.xml' \
  --exclude='.phpstan.neon' --exclude='.composer-require-checker.json' --exclude='infection.json' \
  --exclude='playwright.config.js' --exclude='postcss.config.js' --exclude='.editorconfig' \
  --exclude='package*.json' --exclude='composer.json' --exclude='composer.lock' \
  --exclude='README.scaffold.md'

( cd "$(dirname "$BUILD_DIR")" && zip -rq "$SLUG.zip" "$SLUG" )
mv "$(dirname "$BUILD_DIR")/$SLUG.zip" ./
echo "Built $SLUG.zip"
