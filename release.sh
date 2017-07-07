#!/bin/bash


# release.sh
# 
# (c) 2017 by Marc Anton Dahmen 
#
# This script handles the release process by writing a new version number to version.php, 
# merging the develop branch into default and creating a tag.
#
# To create a new major version use:
# bash release.sh major
#
# To create a new minor version use:
# bash release.sh minor


file=www/automad/version.php


# Build tag.
latestTag=$(hg tags | sed -n '2 p' | cut -d ' ' -f 1)
currentMajor=$(echo $latestTag | cut -d '.' -f 1)
currentMinor=$(echo $latestTag | cut -d '.' -f 2)

case $1 in
	major)
		tag=$(($currentMajor + 1)).0
		;;
	minor)
		tag=$currentMajor.$(($currentMinor + 1))
		;;
	*)
		echo "Please specify \"major\" or \"minor\" as argument!"
		exit 0
		;;	
esac


# Check if working directory is clean.
if [[ $(hg status) ]]
then
	echo "Working directory is not clean!"
	exit 1
fi


# Wait for confirmation.
while true
do
	read -p "Create release $tag? (y/n) " continue
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
echo "<?php define('AM_VERSION', '$tag.0'); ?>" > $file
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