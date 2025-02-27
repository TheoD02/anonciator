#!/usr/bin/env sh

set -e

echo "Executing docker-entrypoint.sh"

echo "Autorun"
if [ -d "/autorun" ]; then
    echo "Autorun directory exists"
    for f in /autorun/*; do
        if [ -f "$f" ] && [ -x "$f" ]; then
            echo "Executing $f"
            "$f"
        fi
    done
    echo "Autorun directory processed"
else
    echo "Autorun directory does not exist"
fi

exec "$@"
