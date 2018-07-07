#!/bin/bash


# release.sh
# 
# (c) 2017-2018 by Marc Anton Dahmen 
#
# This script handles the release process for new Automad versions
# by doing the following:
#
#	1.	Update version numbers in automad/version.php and all related JSON files
#	2.	Run Gulp tasks for GUI and themes (to update version numbers in dist files)
#	3.	Commit changed files
#	4.	Merge branch develop into default
#	5.	Create tag for release


# Get current version.
latestTag=$(hg tags | sed -n '2 p' | cut -d ' ' -f 1)

# Check if working directory is clean.
if [[ $(hg status) ]]
then
	echo "Working directory is not clean!"
	hg status
fi

echo "---"
echo
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

# Update to branch develop.
echo "Updating to branch develop ..."
hg update develop

# Updating version numbers.
echo "Updating version numbers ..."
echo "<?php define('AM_VERSION', '$tag'); ?>" > automad/version.php

for json in {automad/gui,packages/{*/*,*}}/{package,theme}.json
do
	if [[ -f $json ]]
	then
		mv $json $json.bak
		sed "/version/s/[0-9][^\"]*/$tag/" $json.bak > $json
		rm $json.bak
	fi
done

# Running Gulp tasks.
echo "Running Gulp tasks ..."
(
	cd automad/gui
	gulp
)
(
	cd packages/standard
	gulp
)

# Status of repo.
echo "The following files got updated:"
hg status

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

# Commit changes.
echo "Committing changes ..."
hg commit -m "Prepared release $tag"

# Update to branch default.
echo "Updating to branch default ..."
hg update default

# Merging.
echo "Merging branch develop ..."
hg merge develop
echo "Committing merge ..."
hg commit -m "Merged branch develop (release $tag)"

# Creating tag.
echo "Creating tag $tag ..."
hg tag $tag

# Update back to develop.
echo "Updating to branch develop ..."
hg update develop

# Show log.
echo
hg log -l 3
echo "Branch $(hg branch)"
echo

echo "---"

