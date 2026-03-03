# Claude Code remote setup for Laravel

When you use Claude Code on the web, your code runs in a sandboxed VM that starts
fresh each session. The VM is configured for typical dev workflows, but won't have
everything your project needs.

At InterNACHI, we use the `SessionStart` hook to automatically provision Claude VMs
in a way that doesn't slow the process down too much…

## The setup script

You'll need a script for Claude to run in your project. Here's the minimal shell:

```bash
#!/bin/bash
set -euo pipefail

# Only run in remote Claude Code environments
if [ "${CLAUDE_CODE_REMOTE:-}" != "true" ]; then
    exit 0
fi

# Run in the background so the session starts immediately
echo '{"async": true, "asyncTimeout": 300000}'

# Ensure our 'sentinel' file is missing
SENTINEL_FILE="/tmp/.claude-vm-setup-complete"
rm -f "$SENTINEL_FILE"

# Do whatever your project needs to set up the VM
# ...

# Signal that setup is done by touching the 'sentinel' file
touch "$SENTINEL_FILE"
```

From here, install whatever your project needs. In our case, we set up composer and php extensions:

```bash
# Install Composer if missing
if ! command -v composer &> /dev/null; then
    EXPECTED_CHECKSUM="$(php -r 'copy("https://composer.github.io/installer.sig", "php://stdout");')"
    php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
    ACTUAL_CHECKSUM="$(php -r "echo hash_file('sha384', 'composer-setup.php');")"

    if [ "$EXPECTED_CHECKSUM" != "$ACTUAL_CHECKSUM" ]; then
        echo 'ERROR: Invalid composer installer checksum' >&2
        rm composer-setup.php
        exit 1
    fi

    php composer-setup.php --quiet --install-dir=/usr/local/bin --filename=composer
    rm composer-setup.php
fi

cd "$CLAUDE_PROJECT_DIR"

# Install missing PHP extensions
PHP_VERSION="$(php -r 'echo PHP_MAJOR_VERSION.".".PHP_MINOR_VERSION;')"
MISSING_EXTS=()
for ext in gmp; do
    if ! php -m 2>/dev/null | grep -qi "^${ext}$"; then
        MISSING_EXTS+=("php${PHP_VERSION}-${ext}")
    fi
done
if [ ${#MISSING_EXTS[@]} -gt 0 ]; then
    apt-get update -qq
    apt-get install -y -qq "${MISSING_EXTS[@]}"
fi
```

Then we set up our environment files for the VM:

```bash
if [ ! -f .env ]; then
    cp .env.example .env
    sed -i 's/^CACHE_DRIVER=.*/CACHE_DRIVER=file/' .env
    sed -i 's/^SESSION_DRIVER=.*/SESSION_DRIVER=file/' .env
    sed -i 's/^QUEUE_CONNECTION=.*/QUEUE_CONNECTION=sync/' .env
    php artisan key:generate --no-interaction
fi
```

Then we install dependencies:

```bash
composer install --no-interaction --no-progress
yarn install --frozen-lockfile --non-interactive
```

Once your script is good to go, make it executable:

```bash
chmod +x bin/setup-claude-vm.sh
```

And then register the hook in your project's  `.claude/settings.json` file:

```json
{
    "hooks": {
        "SessionStart": [
            {
                "matcher": "startup",
                "hooks": [
                    {
                        "type": "command",
                        "command": "\"$CLAUDE_PROJECT_DIR\"/bin/setup-claude-vm.sh"
                    }
                ]
            }
        ]
    }
}
```

Commit this to your repo. `$CLAUDE_PROJECT_DIR` is provided by Claude Code and
points to your project root.

## When to wait

Because the script runs asynchronously, there's a window where the session is active
but dependencies aren't installed yet. You can tell Claude Code about this in your
main `CLAUDE.md` file. Something like:

```markdown
## Remote VM Setup

The `SessionStart` hook (`bin/setup-claude-vm.sh`) runs asynchronously when a
remote session starts. Before running tests, linters, or any command that depends
on project dependencies, wait for setup to finish:

    if [ "$CLAUDE_CODE_REMOTE" = "true" ]; then
        while [ ! -f /tmp/.claude-vm-setup-complete ]; do sleep 2; done
    fi
```

It's not absolutely necessary, but can help Claude know when to wait for the setup to complete.

## Have Claude do it for you…

If you want Claude to set this up for you, feel free to append `.md` to the end of this
URL to get this article in agent-friendly markdown :)
