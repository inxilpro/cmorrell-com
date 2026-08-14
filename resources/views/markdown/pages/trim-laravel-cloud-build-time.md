---
title: Trim Laravel Cloud Build Time
published_at: 2026-08-12
summary: Some micro-optimizations to cut your Laravel Cloud builds by ~40%
---

# Trim Laravel Cloud Build Time

I've been using [Laravel Cloud](https://laravel.com/cloud) a bunch lately. Build times take about 18 seconds on the app that I'm working on. My entire build script used to be:

```shell
composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader
npm ci --audit false
npm run build
```

Simple and clean.

The problem is, it's also doing **a lot of waiting around**. We have to wait 5–10 seconds for composer to finish installing before we can kick of npm. That drives me nuts. So I wrote a little shell
script to fix that. It's called [`parallel.sh`](https://github.com/glhd/parallel-build) and works like this:

```shell
chain "install dependencies" \
  "composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader"
  
chain "build assets" \
  "npm ci --audit false" \
  "npm run build"

run
```

You start by defining any "chains" of commands that need to happen in sequence (in this case, we need to run `npm ci` _before_ we can build our assets). Once your chains are defined, you call `run`
and everything runs in parallel.

Here are some benchmarks:

| Scenario                              | Before | After | Speedup |
|---------------------------------------|-------:|------:|--------:|
| Typical Composer + NPM build          | 11.01s | 7.09s |    1.6x |
| Four CI checks                        | 12.01s | 5.03s |    2.4x |
| A failing step late in the build      |  6.01s | 1.03s |    5.8x |
| One long process + several quick ones | 10.01s | 6.04s |    1.7x |

In my real-world testing, this has **brought my Laravel Cloud build down from ~18s to ~11s**.

[Check the parallel-build repo out](https://github.com/glhd/parallel-build). It's a single script you can just copy-and-paste into your project.

## The earlier implementation

Before I abstracted this away into a shell function, I had a much less elegant solution. Below you'll find the earlier version of this page:

### The ugly solution

Well, if you want something _less_ simple and clean, but a little bit faster, you can speed up your builds by parallelizing (almost) everything. Here's my current script. It:

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
