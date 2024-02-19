#!/bin/zsh

css="automad/dist/admin/main.bundle.css"
js="automad/dist/admin/main.bundle.js"
ignored="am-style-"

grep -o -e '\.am\-[a-z0-9_-]*' $css | sort -u | while read -r item ; do
	if [[ ! "$item" =~ $ignored ]]
	then
		if [[ ! $(grep "$item" $js) ]] 
		then
			echo $item
		fi
	fi
done
