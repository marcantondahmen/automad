#!/bin/sh

cd ..

baseDir=$(pwd)
distDir=$baseDir/dist
dockerDir=$baseDir/docker
dockerLiteSpeedDir=$baseDir/docker-litespeed
name=automad/automad
platforms=linux/amd64,linux/arm64

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

	echo "Building and pushing LiteSpeed image ..."

	docker buildx build \
		--platform $platforms \
		--build-arg version=$version \
		-t $name:${version}-litespeed \
		-t $name:${major}-litespeed \
		-t $name:latest-litespeed \
		--push .
)

echo '---------------------------------------------------------------------------'
echo "Nginx image"
echo "Cloning Nginx docker repository ..."
(
	git clone https://github.com/automadcms/automad-docker.git $dockerDir
	cd $dockerDir

	echo "Building and pushing Nginx image ..."

	docker buildx build \
		--platform $platforms \
		--build-arg version=$version \
		-t $name:$version \
		-t $name:$major \
		-t $name:latest \
		--push .
)
