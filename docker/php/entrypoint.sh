#!/bin/sh

set -e

setfacl -dR -m u:1000:rwX -m u:$(whoami):rwX /app
setfacl -R -m u:1000:rwX -m u:$(whoami):rwX /app

exec tail -f /dev/null
