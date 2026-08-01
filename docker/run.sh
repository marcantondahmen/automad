#!/bin/bash

cd -- "$(dirname -- "${BASH_SOURCE[0]}")"

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

printf '\033]0;Automad - %s\007' "docker:$dir"

if [ -n "${TMUX:-}" ]; then
	tmux rename-window "docker:$dir"
fi

docker compose up --build
