#!/bin/bash

cleanup() {
	# Show again ctrl c
	stty -echoctl
	echo -e "\n[PHP] stopping server ...\n"
	bash bin/server.sh stop
}

# Always run cleanup when script exits
# and also handle Ctrl+c
trap cleanup EXIT

# Hide ctrl c
stty -echoctl

echo -e "[Prebuild] Running prebuild tasks ..."
bash bin/prebuild.sh

echo -e "[PHP] starting server ...\n"
bash bin/server.sh start

echo -e "\n[Esbuild] starting esbuild ...\n"
node esbuild.js --dev
