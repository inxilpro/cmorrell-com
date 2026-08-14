#!/bin/sh
set -u

# get the parallel build script
_cwd=$(dirname "$0")
. "$_cwd/parallel.sh"

# prep our build chain
chain "composer" \
    "composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader"

chain "npm" \
    "npm ci --audit false" \
    "npm run build"

# run the build
run
