#!/bin/bash

npm run build &>/dev/null

css="automad/dist/build/admin/index.css"
js="$(cat automad/dist/build/chunks/*.js)"
ignored="am-style-"

grep -o -e '\.am\-[a-z0-9_-]*' $css | sort -u | while read -r item; do
	if [[ ! "$item" =~ $ignored ]]; then
		if [[ ! $(echo "$js" | grep "$item") ]]; then
			echo $item
		fi
	fi
done
