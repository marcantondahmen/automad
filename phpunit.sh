#!/bin/zsh

phar="phpunit-7.5.0.phar"
local="$(pwd)/$phar"
url="https://phar.phpunit.de/$phar"

if [ ! -f "$phar" ]; then
	curl -o $local --progress-bar $url
fi

php $local