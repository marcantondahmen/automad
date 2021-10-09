#!/bin/zsh

dir=$(dirname "$0")
cd "$dir/.."

phar="phpunit-9.5.10.phar"
local="$(pwd)/$phar"
url="https://phar.phpunit.de/$phar"

if [ ! -f "$phar" ]; then
	curl -o $local --progress-bar $url
fi

php $local