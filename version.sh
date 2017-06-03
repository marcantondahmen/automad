#!/bin/bash


# version.sh
# 
# (c) 2013-2017 by Marc Anton Dahmen 
#
# A version number will be generated from the position of the working copy in the Mercurial history.
# When doing a normal commit, the version is generated from the latest tagged revision (on all branches, not like --template {latesttag})
# and the distance to the next committed revision (working copy parent +1).
# Example: 1.2.3 > After the next commit the "next" revision will be 3 commits away from the tag 1.2 (the latest found, execpt tip).
# Generating the version number with --template {latesttag}.{latesttagdistance} is not possible, since the latest tags 
# on branch default won't be visible to a working copy on branch develop before merging.
 
file="www/automad/version.php"

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
	
	version=$tag.$distance

else			
	
	version=0.0.$nextRev	

fi
		
echo "Generated version number: $version"	
echo "---"
echo
echo "<?php define('AM_VERSION', '$version'); ?>" > $file

