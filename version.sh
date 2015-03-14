#!/bin/bash


# version.sh
# 
# (c) 2013 by Marc Anton Dahmen 
#
# A version number will be generated from the position of the working copy in the Mercurial history.
# When finishing a release or hotfix with hg-flow, the number will be taken from the flow argument.
# When doing a normal commit, the version is generated from the latest tagged revision (on all branches, not like --template {latesttag})
# and the distance to the next committed revision (working copy parent +1).
# Example: 1.2.3 > After the next commit the "next" revision will be 3 commits away from the tag 1.2 (the latest found, execpt tip).
# Generating the version number with --template {latesttag}.{latesttagdistance} is not possible when using hg-flow, since the latest tags 
# on branch default won't be visible to a working copy on branch develop before merging.
 

arg="$@"


# If script gets called from command line, show messages how to add hooks to .hg/hgrc.
if [[ $arg == "" ]]; then

	echo
	echo "---"	
	echo 'Add the following lines to .hg/hgrc to automatically generate a version number with Mercurial:'
	echo 
	echo '[hooks]'
	echo 'pre-commit = ./version.sh "$HG_ARGS"'
	echo 'pre-flow = ./version.sh "$HG_ARGS"'
	echo "---"
	
fi


flowCommitMessage="Updated version number to"
file="www/automad/version.php"


# If $arg contains the $flowCommitMessage, 
# the script got called after commiting the updated version number in $file 
# when finishing a release or hotfix with hg-flow.
# In that case, generating the version number gets skipped.
if [[ $arg != *"$flowCommitMessage"* ]]; then
	
	# Finish release or hotfix (hg-flow)
	if [[ $arg =~ (release|hotfix).*finish ]]; then
	 
		version=${arg##*finish }.0
			
		echo
		echo "---"
		echo "Get version number from hg-flow argument: $version"
		echo "<?php define('AM_VERSION', '$version'); ?>" > $file
		echo "Commit $file"
		echo "---"
		echo
		
		hg commit -m "$flowCommitMessage $version"
	
	# Normal commit
	elif [[ ! $arg =~ ^flow.* ]]; then
		
		echo
		echo "---"
		echo "Generate version number from normal Commit"
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
		
	# Start release or hotfix (hg-flow)	
	elif [[ $arg =~ (release|hotfix).*start ]]; then
	
		echo "Skip version number when starting a release/hotfix with hg-flow"
		
	# Features (hg-flow)	
	elif [[ $arg =~ ^flow.*feature ]]; then
	
		echo "Skip version number when starting/finishing a feature with hg-flow"
		
	fi
	
fi
