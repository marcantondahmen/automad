#!/bin/bash

# Run this script to checkout develop and start a fresh feature, bugfix or refactor branch.

# Change to the base directory of the repository.
dir=$(dirname "$0")
cd "$dir/.."

if [[ $(git status -s) ]]
then
	echo "Working directory is not clean!"
	git status -s
	exit 0
fi

git checkout develop

echo "Choose type of branch:"
echo
echo "  1) Feature (default)"
echo "  2) Bugfix"
echo "  3) Refactor"
echo
read -n 1 -p "Please select a number or press Enter for a Feature: " option
echo

case $option in 
	1) branchType="feat";;
	2) branchType="fix";;
	3) branchType="refactor";;
	*) branchType="feat";;
esac

read -p "Please enter a scope: " branchScope

read -p "Please enter a name:  " branchName

branch="$branchType/$branchScope/$( echo $branchName | tr '[:upper:]' '[:lower:]' | sed 's/ /_/g' )"

while true
do
	read -n 1 -p "Create $branch? (y/n) " option 
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

git branch $branch
git checkout $branch