#!/bin/bash


# release.sh
# 
# (c) 2017-2020 by Marc Anton Dahmen 
#
# This script handles the release process for new Automad versions
# by doing the following:
#
#	1.	Check whether the current branch is develop
#	2.	Run tests
#	3.	Update version numbers in automad/version.php and all related JSON files
#	4.	Run Gulp tasks for GUI and themes (to update version numbers in dist files)
#	5.	Commit changed files
#	6.	Merge branch develop into master
#	7.	Create tag for release
#	8. 	Push changes to origin


# Test branch.
if [[ $(git branch | grep \* | cut -d ' ' -f2) != "develop" ]]
then
	echo "Please checkout branch develop to create a release!"
	exit 0
fi


# Run tests.
bash phpunit.sh
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


echo "Current version is: $latestTag"
read -p "Please enter a new version number: " tag


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

for json in {automad,packages/{*/*,*}}/{package,theme}.json
do
	if [[ -f $json ]]
	then
		mv $json $json.bak
		sed "/version/s/[0-9][^\"]*/$tag/" $json.bak > $json
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
git add -A && git commit -m "Prepared release $tag"
echo


# Update to branch default.
echo "Checking out branch master ..."
git checkout master
echo


# Merging.
echo "Merging branch develop ..."
git merge develop --no-ff -m "Merged branch develop (release $tag)"
echo


# Creating tag.
echo "Creating tag $tag ..."
git tag -a -m "Release $tag" $tag
echo


# Update back to develop.
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
