#!/bin/bash

PHPUNIT_PHAR=phpunit-9.6.22.phar

if [[ ! -f "$PHPUNIT_PHAR" ]]; then
	curl -L https://phar.phpunit.de/$PHPUNIT_PHAR --output $PHPUNIT_PHAR
	chmod +x $PHPUNIT_PHAR
fi

./$PHPUNIT_PHAR && ./$PHPUNIT_PHAR -c phpunit-i18n.xml
