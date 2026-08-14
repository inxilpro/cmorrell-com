---
title: Trim Laravel Cloud Build Time
published_at: 2026-08-12
summary: Some micro-optimizations to cut your Laravel Cloud builds by ~40%
---

# Trim Laravel Cloud Build Time

> [!note]
> I have since abstracted this code into a shell function that you can use in your build
> scripts. See [parallel-build](https://github.com/glhd/parallel-build) for details.

I've been using [Laravel Cloud](https://laravel.com/cloud) a bunch lately. Build times take about 
18 seconds on the app that I'm working on. My entire build script used to be:

```shell
composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader
npm ci --audit false
npm run build
```

Simple and clean.

Well, if you want something _less_ simple and clean, but a little bit faster, you can speed up your
builds by parallelizing (almost) everything. Here's my current script. It:

- Runs everying it can in parallel
- Fails as fast as possible
- Still gets you the command output in the end

Maybe it's not worth it, but while I'm iterating fast, the 11s build feels a lot better than the 18s one!

```shell
composer_log=$(mktemp)
npm_log=$(mktemp)
build_log=$(mktemp)

cleanup() {
  rm -f "$composer_log" "$npm_log" "$build_log"
}
trap cleanup EXIT

# Start composer install in bg
composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader > "$composer_log" 2>&1 &
composer_pid=$!

# Start npm install in fg because "run build" depends on it
echo "--- npm install ---"
npm ci --audit false
npm_status=$?

if [ $npm_status -ne 0 ]; then
  echo "[!] npm ci failed"
  kill $composer_pid 2>/dev/null
  wait $composer_pid 2>/dev/null
  echo "--- composer install (maybe partial) ---"
  cat "$composer_log"
  exit 1
fi

npm run build > "$build_log" 2>&1 &
build_pid=$!

wait $composer_pid
composer_status=$?

echo "--- composer install ---"
cat "$composer_log"

if [ $composer_status -ne 0 ]; then
  echo "[!] composer failed"
  kill $build_pid 2>/dev/null
  wait $build_pid 2>/dev/null
  echo "--- npm build (maybe partial) ---"
  cat "$build_log"
  exit 1
fi

wait $build_pid
build_status=$?

echo "--- npm build ---"
cat "$build_log"

if [ $build_status -ne 0 ]; then
  echo "[!] npm build failed"
  exit 1
fi
```

Enjoy!
