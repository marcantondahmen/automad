#!/bin/bash

ls ./node_modules/@expo-google-fonts/inter | grep ttf

mkdir -p ./automad/dist/fonts/open-graph/
cp ./node_modules/@expo-google-fonts/inter/Inter_500Medium.ttf ./automad/dist/fonts/open-graph/
cp ./node_modules/@expo-google-fonts/inter/Inter_700Bold.ttf ./automad/dist/fonts/open-graph/
