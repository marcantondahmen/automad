#!/bin/bash

# Exit on error.
set -e

workBranch="v2"
releaseBranch="master"

# Change to the base directory of the repository.
dir=$(dirname "$0")
cd "$dir/.."

# Test branch.
if [[ $(git branch | grep \* | cut -d ' ' -f2) != "$workBranch" ]]; then
	echo "Please checkout branch $workBranch to create a release!"
	exit 0
fi

# Check if working directory is clean.
if [[ $(git status -s) ]]; then
	echo "Working directory is not clean!"
	git status -s
	exit 0
fi

# Run tests.
echo "Running tests ..."
npm run test
echo

# update language packs.
echo "Updating language packs ..."
(
	cd lib
	composer update automad/language-packs
)

# Get latest tag.
latestTag=$(git describe --tags $(git rev-list --tags --max-count=1))

# Choose type of release.
echo "Current version is: $latestTag"
echo

IFS='.' read -ra elem <<<"$latestTag"

major=${elem[0]}
minor=${elem[1]}
patch=${elem[2]}
pre=${elem[3]}

newMajorTag=$((major + 1)).0.0
newMinorTag=$major.$((minor + 1)).0
newPatchTag=$major.$minor.$((patch + 1))
newPreTag=$major.$minor.$patch.$((pre + 1))

echo "Choose type of release:"
echo
echo "  1) Patch       $newPatchTag (default)"
echo "  2) Minor       $newMinorTag"
echo "  3) Major       $newMajorTag"
echo "  4) Pre-Release $newPreTag"
echo "  5) Custom"
echo
read -n 1 -p "Please select a number or press Enter for a patch: " option
echo

case $option in
1) tag=$newPatchTag ;;
2) tag=$newMinorTag ;;
3) tag=$newMajorTag ;;
4) tag=$newPreTag ;;
5) read -p "Tag: " tag ;;
*) tag=$newPatchTag ;;
esac

# Wait for confirmation.
while true; do
	read -p "Create release \"$tag\"? (y/n) " continue
	case $continue in
	[Yy]*)
		break
		;;
	[Nn]*)
		exit 0
		;;
	*)
		echo "Please only enter \"y\" or \"n\"."
		;;
	esac
done
echo

# Generate changelog.
echo "Generating changelog ..."
bash bin/changelog.sh 50 $tag >CHANGELOG.md

# Updating version numbers.
echo "Updating version numbers ..."

app="automad/src/server/App.php"
mv $app $app.bak
sed "/const VERSION/s/[0-9][^\"']*/$tag/" $app.bak >$app
rm $app.bak

for json in {package,package-lock}.json; do
	if [[ -f $json ]]; then
		mv $json $json.bak
		sed "1,/version/s/[0-9][^\"]*/$tag/" $json.bak >$json
		rm $json.bak
	fi
done
echo

# Status of repo.
echo "The following files have been updated:"
git status -s
echo

# Wait for confirmation to commit and merge.
while true; do
	read -p "Commit and merge? (y/n) " continue
	case $continue in
	[Yy]*)
		break
		;;
	[Nn]*)
		exit 0
		;;
	*)
		echo "Please only enter \"y\" or \"n\"."
		;;
	esac
done
echo

# Commit changes.
echo "Committing changes ..."
git add -A && git commit -m "prepare release $tag"
echo

# Check out $releaseBranch branch.
echo "Checking out branch $releaseBranch ..."
git checkout $releaseBranch
echo

# Merging.
echo "Merging branch $workBranch ..."
git merge $workBranch --no-ff -m "merge branch $workBranch (release $tag)"
echo

# Creating tag.
echo "Creating tag $tag ..."
git tag -a -m "Release $tag" $tag
echo

# Check out back $workBranch.
echo "Checking out branch $workBranch ..."
git checkout $workBranch
echo

# Show log.
git log -n 2 --graph --all
echo

# Wait for confirmation to push.
while true; do
	read -p "Push changes to origin? (y/n) " continue
	case $continue in
	[Yy]*)
		echo "Pushing branches ..."
		git push origin --all -u
		echo "Pushing tags ..."
		git push origin --tags
		echo
		break
		;;
	[Nn]*)
		exit 0
		;;
	*)
		echo "Please only enter \"y\" or \"n\"."
		;;
	esac
done

# Show branches.
git branch
echo
