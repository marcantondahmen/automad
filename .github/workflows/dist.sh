#!/bin/sh

if [ -z ${BOT_TOKEN} ]; then
	echo "BOT_TOKEN is not set. Skipping workflow ..."
	exit 1
fi

git config --global url.https://x-access-token:${BOT_TOKEN}@github.com/.insteadOf https://github.com/
git config --global user.name "${GITHUB_ACTOR}"
git config --global user.email "${GITHUB_ACTOR}@users.noreply.github.com"

cd ..

baseDir=$(pwd)
srcDir=$baseDir/src
distDir=$baseDir/dist

echo '---------------------------------------------------------------------------'
echo "Cloning source repository ..."
git clone https://github.com/marcantondahmen/automad.git $srcDir
cd $srcDir

git status

srcVersion=$(cat package.json | jq -r .version)
echo "Latest source version: $srcVersion"

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
npm --loglevel=error ci
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
	-a \
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
	$srcDir/index.php \
	$srcDir/LICENSE.md \
	$distDir

echo $srcVersion >VERSION
echo ".gitattributes export-ignore" >.gitattributes
echo "VERSION export-ignore" >>.gitattributes

echo '---------------------------------------------------------------------------'
echo 'Commit and push'

git add .
git commit -m "build version $srcVersion"
git push origin --all -u
git push --tags
