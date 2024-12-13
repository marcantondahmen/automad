#!/bin/bash

if [ -z ${BOT_TOKEN} ]; then
	echo "BOT_TOKEN is not set. Skipping workflow ..."
	exit 1
fi

git config --global url.https://x-access-token:${BOT_TOKEN}@github.com/.insteadOf https://github.com/
git config --global user.name "${GITHUB_ACTOR}"
git config --global user.email "${GITHUB_ACTOR}@users.noreply.github.com"
git config --global advice.detachedHead false

cd ..

baseDir=$(pwd)
srcDir=$baseDir/src
distDir=$baseDir/dist

echo '---------------------------------------------------------------------------'
echo "Cloning source repository ..."
git clone https://github.com/marcantondahmen/automad.git $srcDir
cd $srcDir

git fetch --tags

srcVersion=$(git describe --tags "$(git rev-list --tags --max-count=1)")
echo "Latest source version: $srcVersion"

git checkout $srcVersion
git status

echo "Cloning dist repository ..."
git clone https://github.com/automadcms/automad-dist.git $distDir
cd $distDir

git ls-remote --tags 2>/dev/null | grep $srcVersion 1>/dev/null

if [ "$?" == 0 ]; then
	echo "Tag $srcVersion already exists."

	exit 0
fi

cd $srcDir

echo '---------------------------------------------------------------------------'
echo "Building from source ..."
npm install --loglevel=error
npm run build

echo '---------------------------------------------------------------------------'
echo "Installing PHP dependencies ..."

composer install
(cd lib && composer install)

cd $distDir

branch=v${srcVersion%%.*}

echo '---------------------------------------------------------------------------'
echo "Target Branch: $branch"

git fetch origin
git switch $branch || git switch -c $branch

echo "Syncing changes ..."

rsync \
	-rcvh \
	--delete \
	--stats \
	--exclude=.git \
	--exclude=automad/src/client \
	--exclude=automad/tests \
	$srcDir/automad \
	$srcDir/cache \
	$srcDir/config \
	$srcDir/lib \
	$srcDir/packages \
	$srcDir/pages \
	$srcDir/shared \
	$srcDir/vendor \
	$srcDir/.htaccess \
	$srcDir/composer.json \
	$srcDir/composer.lock \
	$srcDir/index.php \
	$srcDir/LICENSE.md \
	$distDir

echo $srcVersion >VERSION
echo ".gitattributes export-ignore" >.gitattributes
echo ".github export-ignore" >>.gitattributes
echo "VERSION export-ignore" >>.gitattributes

echo '---------------------------------------------------------------------------'
echo 'Commit and push'

git add .
git commit -m "build version $srcVersion"
git tag -a -m "v$srcVersion" $srcVersion

git checkout master
git merge $branch --ff -m "merge branch $branch"

git push origin --all -u
git push --tags
