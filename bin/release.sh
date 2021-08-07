#!/bin/bash


# release.sh
# 
# (c) 2017-2021 by Marc Anton Dahmen 
#
# This script handles the release process for new Automad versions
# by doing the following:
#
#	1.	Kill all running watch tasks
#	2.	Check whether the current branch is develop
#	3.	Run tests
#	4.	Update version numbers in automad/version.php and all related JSON files
#	5.	Run Gulp tasks for UI and themes (to update version numbers in dist files)
#	6.	Commit changed files
#	7.	Merge branch develop into master
#	8.	Create tag for release
#	9. 	Push changes to origin


# Change to the base directory of the repository.
dir=$(dirname "$0")
cd "$dir/.."


# Test branch.
if [[ $(git branch | grep \* | cut -d ' ' -f2) != "develop" ]]
then
	echo "Please checkout branch develop to create a release!"
	exit 0
fi


# Kill all watch tasks.
ps | grep "gulp watch" | grep -v grep | awk '{print $1}' | xargs kill


# Run tests.
bash bin/phpunit.sh
echo


# Get latest tag.
latestTag=$(git describe --tags $(git rev-list --tags --max-count=1))


# Check if working directory is clean.
if [[ $(git status -s) ]]
then
	echo "Working directory is not clean!"
	git status -s
	echo
fi


# Choose type of release.
echo "Current version is: $latestTag"
echo 

IFS='.' read -ra elem <<< "$latestTag"

major=${elem[0]}
minor=${elem[1]}
patch=${elem[2]}

newMajorTag=$((major + 1)).0.0
newMinorTag=$major.$((minor + 1)).0
newPatchTag=$major.$minor.$((patch + 1))

echo "Choose type of release:"
echo
echo "  1) Patch $newPatchTag (default)"
echo "  2) Minor $newMinorTag"
echo "  3) Major $newMajorTag"
echo
read -n 1 -p "Please select a number or press Enter for a patch: " option
echo

case $option in 
	1) tag=$newPatchTag;;
	2) tag=$newMinorTag;;
	3) tag=$newMajorTag;;
	*) tag=$newPatchTag;;
esac


# Wait for confirmation.
while true
do
	read -p "Create release \"$tag\"? (y/n) " continue
	case $continue in
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
echo


# Updating version numbers.
echo "Updating version numbers ..."
echo "<?php define('AM_VERSION', '$tag'); ?>" > automad/version.php

for json in {automad,packages/{*/*,*}}/{package,package-lock,theme}.json
do
	if [[ -f $json ]]
	then
		mv $json $json.bak
		sed "1,/version/s/[0-9][^\"]*/$tag/" $json.bak > $json
		rm $json.bak
	fi
done
echo


# Running Gulp tasks.
echo "Running Gulp tasks ..."
(
	cd automad
	gulp
)
(
	cd packages/standard
	gulp
)
echo


# Status of repo.
echo "The following files got updated:"
git status -s
echo


# Wait for confirmation to commit and merge.
while true
do
	read -p "Commit and merge? (y/n) " continue
	case $continue in
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
echo


# Commit changes.
echo "Committing changes ..."
git add -A && git commit -m "build(release): prepare release $tag"
echo


# Check out master branch.
echo "Checking out branch master ..."
git checkout master
echo


# Merging.
echo "Merging branch develop ..."
git merge develop --no-ff -m "build(release): merge branch develop (release $tag)"
echo


# Creating tag.
echo "Creating tag $tag ..."
git tag -a -m "Release $tag" $tag
echo


# Check out back develop.
echo "Checking out branch develop ..."
git checkout develop
echo


# Show log.
git log -n 2 --graph --all
echo

# Wait for confirmation to push.
while true
do
	read -p "Push changes to origin? (y/n) " continue
	case $continue in
		[Yy]* ) 
			echo "Pushing branches ..."
			git push origin --all -u
			echo "Pushing tags ..."
			git push origin --tags
			echo
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

# Show branches.
git branch
echo
