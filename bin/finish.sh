#!/bin/bash

# Run this script on a feature, bugfix or refactoring branch to squash and merge changes to develop.

# Change to the base directory of the repository.
dir=$(dirname "$0")
cd "$dir/.."

branch=$(git branch | egrep -v "(master|develop)" | grep \* | sed "s|\* ||")

if [[ ! $branch ]] 
then
	echo "You are not on a feature, bugfix or refactor branch!"
	exit 0
fi

if [[ $(git status -s) ]]
then
	echo "Working directory is not clean!"
	git status -s
	exit 0
fi

while true
do
	read -n 1 -p "Finish $branch? (y/n) " option 
	case $option in
		[Yy]* ) 
			break
			;;
		[Nn]* ) 
			exit 0
			;;
		* ) 
			echo "Please only enter \"y\" or \"n\"."
			;;
	esac
done

ps | grep "gulp watch" | grep -v grep | awk '{print $1}' | xargs kill

msg="$( echo $branch | sed -E 's|([^/]+)/([^/]+)/(.*)|\1(\2): \3|' | sed 's|_| |g' )"

git checkout develop
git merge $branch --no-ff -m "$msg" && git push origin --all -u && git branch --delete $branch && git push -d origin $branch