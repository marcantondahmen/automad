#!/bin/bash

rm -rf ./automad/dist/build

prismThemes="./automad/dist/prism-themes/"
openGraphFonts="./automad/dist/open-graph/"

mkdir -p $prismThemes
cp -R ./node_modules/automad-prism-themes/dist/prism-*.css $prismThemes

mkdir -p $openGraphFonts
cp ./node_modules/@expo-google-fonts/inter/Inter_500Medium.ttf $openGraphFonts
cp ./node_modules/@expo-google-fonts/inter/Inter_700Bold.ttf $openGraphFonts
