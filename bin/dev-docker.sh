#!/bin/bash

export USER_ID=$(id -u)
export GROUP_ID=$(id -g)

selection=$(
	find docker -mindepth 1 -maxdepth 1 -type d |
		while read -r d; do
			[ -f "$d/compose.yml" ] && basename "$d"
		done |
		sort |
		fzf --prompt="Select server: " --reverse
)

[ -z "$selection" ] && exit 0

dir="$(pwd)/docker/$selection"
title="dev:docker:$selection"
compose="docker compose --project-directory $dir -f $dir/compose.yml"

printf '\033]0;Automad - %s\007' "$title"

if [ -n "${TMUX:-}" ]; then
	tmux rename-window "$title"
fi

cleanup() {
	# Show again ctrl c
	stty -echoctl

	echo -e "[Docker] compose down ..."
	$compose down
}

# Always run cleanup when script exits
# and also handle Ctrl+c
trap cleanup EXIT

# Hide ctrl c
stty -echoctl

echo -e "[Docker] compose up ..."
$compose up --build -d

echo -e "\n[Prebuild] Running prebuild tasks ..."
bash bin/prebuild.sh

echo -e "\n[Esbuild] starting esbuild ...\n"
node esbuild.js --dev
