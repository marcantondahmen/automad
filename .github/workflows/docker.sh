#!/bin/sh

cd ..

baseDir=$(pwd)
distDir=$baseDir/dist
dockerDir=$baseDir/docker
dockerLiteSpeedDir=$baseDir/docker-litespeed
name=automad/automad

echo '---------------------------------------------------------------------------'
echo "Cloning dist repository ..."

git clone https://github.com/automadcms/automad-dist.git $distDir
cd $distDir

git status

version=$(git describe --tags $(git rev-list --tags --max-count=1))
major=v${version%%.*}
echo "Latest version: $version"

echo '---------------------------------------------------------------------------'
echo "LiteSpeed image"
echo "Cloning LiteSpeed docker repository ..."
(
	git clone https://github.com/automadcms/automad-docker-litespeed.git $dockerLiteSpeedDir
	cd $dockerLiteSpeedDir

	echo "Building LiteSpeed image ..."

	docker build \
		--build-arg version=$version \
		-t $name:${version}-litespeed \
		-t $name:${major}-litespeed \
		-t $name:latest-litespeed .
)

echo '---------------------------------------------------------------------------'
echo "Nginx image"
echo "Cloning Nginx docker repository ..."
(
	git clone https://github.com/automadcms/automad-docker.git $dockerDir
	cd $dockerDir

	echo "Building Nginx image ..."

	docker build \
		--build-arg version=$version \
		-t $name:$version \
		-t $name:$major \
		-t $name:latest .
)

docker images

echo '---------------------------------------------------------------------------'
echo "Pushing ..."

docker push $name:${version}-litespeed
docker push $name:${major}-litespeed
docker push $name:latest-litespeed
docker push $name:$version
docker push $name:$major
docker push $name:latest
