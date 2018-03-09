#!/bin/bash


# version.sh
# 
# (c) 2013-2017 by Marc Anton Dahmen 
#
# A version number will be generated from the position of the working copy in the Mercurial history.
#
# Following the conventions of semantic versioning, a plus sign (+),
# followed by a number representing the distance from the latest tag to the next committed revision (working copy parent +1) 
# will be appended to the latest tag to create a unique version number on all dev branches.
#
# Example: 
# If 1.0.0-beta was the lasted release and the next commit on a dev branch will be 123 commits ahead of that tag,
# the generated version number will be 1.0.0-beta+123.
#
# Generating the version number with --template {latesttag}.{latesttagdistance} is not possible, since the latest tags 
# on branch default won't be visible to a working copy on branch develop before merging.
 
file="automad/version.php"

echo
echo "---"
echo "Generating version number"
echo

tagsOutput=$(hg tags | sed -n '2 p')
tag=${tagsOutput%% *}
echo "Find latest tag across all branches: $tag"

# $nextRev is the future revision number from the next commit (tip +1)
nextRev=$(($(hg tip --template "{rev}") + 1))
echo "Next revision will be: $nextRev"

if [[ $tag != "" ]]; then

	taggedRev=$(hg log -r "$tag" --template "{rev}\n")
	echo "Revsion number for $tag is: $taggedRev"
	
	distance=$(($nextRev - $taggedRev))
	echo "Distance in history: $distance"
	
	# Append a plus sign followed by the number of commits ahead of the latest tag.
	version="$tag+$distance"

fi
		
echo "Generated version number: $version"	
echo "---"
echo
echo "<?php define('AM_VERSION', '$version'); ?>" > $file

