#!/bin/bash

cleanup() {
	# Show again ctrl c
	stty -echoctl
	echo -e "[Docker] compose down ..."
	docker compose down
}

# Always run cleanup when script exits
# and also handle Ctrl+c
trap cleanup EXIT

# Hide ctrl c
stty -echoctl

repo=$(pwd)

cd $repo/docker

export USER_ID=$(id -u)
export GROUP_ID=$(id -g)

dir=$(
	find . -mindepth 1 -maxdepth 1 -type d |
		while read -r d; do
			[ -f "$d/compose.yml" ] && basename "$d"
		done |
		sort |
		fzf --prompt="Select server: " --reverse
)

[ -z "$dir" ] && exit 0

cd "$dir"

title="dev:docker:$dir"

printf '\033]0;Automad - %s\007' "$title"

if [ -n "${TMUX:-}" ]; then
	tmux rename-window "$title"
fi

echo -e "[Docker] compose up ..."
docker compose up --build -d

echo -e "[Prebuild] Running prebuild tasks ..."
bash bin/prebuild.sh

echo -e "\n[Esbuild] starting esbuild ...\n"
node $repo/esbuild.js --dev
