#!/bin/bash

rm -rf ./automad/dist/*/*

mkdir -p ./automad/dist/prism/themes/
cp -R ./node_modules/automad-prism-themes/dist/prism-*.css ./automad/dist/prism/themes/

mkdir -p ./automad/dist/fonts/open-graph/
cp ./node_modules/@expo-google-fonts/inter/Inter_500Medium.ttf ./automad/dist/fonts/open-graph/
cp ./node_modules/@expo-google-fonts/inter/Inter_700Bold.ttf ./automad/dist/fonts/open-graph/
