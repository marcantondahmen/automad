#!/bin/bash


# version.sh
# 
# (c) 2013-2020 by Marc Anton Dahmen 
#
# A version number will be generated from the position of the working copy in the Git history.
#
# Following the conventions of semantic versioning, a plus sign (+),
# followed by a number representing the distance from the latest tag to the next committed revision (working copy parent +1) 
# will be appended to the latest tag to create a unique version number on all dev branches.
#
# Example: 
# If 1.0.0-beta was the lasted release and the next commit on a dev branch will be 123 commits ahead of that tag,
# the generated version number will be 1.0.0-beta+123.


file="automad/version.php"

echo
echo "---"
echo "Generating version number"
echo

tag=$(git describe --tags $(git rev-list --tags --max-count=1))

echo "Find latest tag across all branches: $tag"
	
distance=$(($(git rev-list --count $(git rev-list --tags --max-count=1)..HEAD) + 1))
echo "Distance to $tag: $distance"
	
# Append a plus sign followed by the number of commits ahead of the latest tag.
version="$tag+$distance"
		
echo "Generated version number: $version"	
echo "---"
echo
echo "<?php define('AM_VERSION', '$version'); ?>" > $file
