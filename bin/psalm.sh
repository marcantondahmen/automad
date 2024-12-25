#!/bin/bash

PSALM_PHAR=psalm-5.26.1.phar

if [[ ! -f "$PSALM_PHAR" ]]; then
	curl -L https://github.com/vimeo/psalm/releases/download/5.26.1/psalm.phar --output $PSALM_PHAR
	chmod +x $PSALM_PHAR
fi

./$PSALM_PHAR --clear-global-cache && ./$PSALM_PHAR --no-cache --threads=1
