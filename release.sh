#!/bin/bash


# release.sh
# 
# (c) 2017 by Marc Anton Dahmen 
#
# This script handles the release process by writing a new version number to version.php, 
# merging the develop branch into default and creating a tag.

file=www/automad/version.php

# Get current version.
latestTag=$(hg tags | sed -n '2 p' | cut -d ' ' -f 1)

# Check if working directory is clean.
if [[ $(hg status) ]]
then
	echo "Working directory is not clean!"
	exit 1
fi

echo "---"
echo
echo "Current version is: $latestTag"
read -p "Please enter a new version number: " tag
echo

# Wait for confirmation.
while true
do
	read -p "Create release \"$tag\"? (y/n) " continue
	case $continue in
                [Yy]* ) 
			break
			;;
                [Nn]* ) 
			exit
			;;
                * ) 
			echo "Please only enter \"y\" or \"n\"."
			;;
        esac
done

# Create release.
echo
echo "Updating to branch develop ..."
hg update develop
echo
echo "Writing and committing version.php ..."
echo "<?php define('AM_VERSION', '$tag'); ?>" > $file
hg commit -m "Updated version number to $tag" $file
echo
echo "Updating to branch default ..."
hg update default
echo
echo "Merging branch develop ..."
hg merge develop
echo
echo "Committing merge ..."
hg commit -m "Merged branch develop (release $tag)"
echo
echo "Creating tag $tag ..."
hg tag $tag
echo
echo "Updating to branch develop ..."
hg update develop
echo 
hg log -l 3
echo "---"