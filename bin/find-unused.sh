#!/bin/bash

findUnusedText() {
	echo -e "\033[0;34mSearching unused text modules ...\033[0m"

	# Enable **
	shopt -s globstar

	local lang=automad/lang/english.json
	local js="$(cat automad/dist/build/admin/index.js)"
	local php="$(cat automad/src/server/**/*.php)"
	local md="$(cat automad/lang/README.md)"

	# Disable **
	shopt -u globstar

	local code="$js$php$md"

	grep -o -P '^\s*"\K[a-zA-Z]+' $lang | sort -u | while read -r item; do
		if [[ ! $(echo "$code" | grep "$item") ]]; then
			echo "  $item"
		fi
	done

	echo -e "\033[0;32m Done\033[0m\n"
}

findUnusedCSS() {
	echo -e "\033[0;34mSearching unused CSS classes ...\033[0m"

	local css=automad/dist/build/admin/index.css
	local js="$(cat automad/dist/build/admin/index.js)"
	local ignored="am-style-"

	grep -o -e '\.am-[a-z0-9_-]*' $css | sort -u | while read -r item; do
		if [[ ! "$item" =~ $ignored ]]; then
			if [[ ! $(echo "$js" | grep "$item") ]]; then
				echo "  $item"
			fi
		fi
	done

	echo -e "\033[0;32m Done\033[0m\n"
}

echo -e "\n\033[0;34mBuilding dist bundle ...\033[0m\n"
npm run build &>/dev/null

findUnusedCSS
findUnusedText
