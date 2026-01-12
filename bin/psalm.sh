#!/bin/bash

PSALM_VERSION=6.14.3
PSALM_PHAR=psalm-${PSALM_VERSION}.phar
PHP_BIN=php

if [[ ! -f "$PSALM_PHAR" ]]; then
	curl -L https://github.com/vimeo/psalm/releases/download/$PSALM_VERSION/psalm.phar --output $PSALM_PHAR
	chmod +x $PSALM_PHAR
fi

$PHP_BIN ./$PSALM_PHAR --clear-cache && $PHP_BIN ./$PSALM_PHAR --no-cache --threads=1
