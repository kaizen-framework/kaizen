#!/bin/sh

set -e

setfacl -dR -m u:1000:rwX -m u:$(whoami):rwX .
setfacl -R -m u:1000:rwX -m u:$(whoami):rwX .

exec php-fpm