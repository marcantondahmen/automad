#!/bin/bash

PHPUNIT_PHAR=phpunit-12.5.4.phar

if [[ ! -f "$PHPUNIT_PHAR" ]]; then
	curl -L https://phar.phpunit.de/$PHPUNIT_PHAR --output $PHPUNIT_PHAR
	chmod +x $PHPUNIT_PHAR
fi

./$PHPUNIT_PHAR --display-warnings && ./$PHPUNIT_PHAR -c phpunit-i18n.xml --display-warnings
