#!/usr/bin/env bash
# Run PII benchmarks on current branch context and compare main vs V2.
set -euo pipefail

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
PHP="${PHP_BIN:-/Applications/XAMPP/xamppfiles/bin/php}"

if [[ ! -x "$PHP" ]]; then
  echo "PHP not found. Set PHP_BIN to your php executable."
  exit 1
fi

cd "$ROOT"

# Save current branch
CURRENT_BRANCH="$(git branch --show-current)"

cleanup() {
  git checkout "$CURRENT_BRANCH" 2>/dev/null || true
}
trap cleanup EXIT

echo "=== Benchmark: main (plain) ==="
git checkout main --quiet
"$PHP" scripts/benchmark_pii_performance.php --mode=plain --label=main --iterations=500

echo ""
echo "=== Benchmark: V2 (encrypted) ==="
git checkout V2 --quiet

if [[ ! -f .env ]]; then
  echo "Generating .env for V2 benchmark..."
  "$PHP" scripts/generate_encryption_keys.php
  if [[ -f .env.generated ]]; then
    cp .env.generated .env
  fi
fi

"$PHP" scripts/benchmark_pii_performance.php --mode=encrypted --label=V2 --iterations=500

echo ""
echo "=== Comparison report ==="
"$PHP" scripts/benchmark_compare.php

git checkout "$CURRENT_BRANCH" --quiet
