#!/bin/bash

# Run this script on a feature, bugfix or refactoring branch to squash and merge changes to develop.

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

ps | grep "gulp watch" | grep -v grep | awk '{print $1}' | xargs kill

msg=$( echo $branch | sed "s|/|: |" | sed "s|_| |g" )

git checkout develop
git merge --squash $branch
git commit -m "$msg"
git branch $branch -D