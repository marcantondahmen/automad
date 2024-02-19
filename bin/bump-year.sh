#!/bin/bash

year=2023

bumpDate () {
	mv $1 $1.bak
	sed -E "/by Marc Anton Dahmen/s/( 20[0-9]{2})\-20[0-9]{2} /\1-$year /g" $1.bak > $1
	rm $1.bak

	mv $1 $1.bak
	sed -E "/by Marc Anton Dahmen/s/( 202(0|1|2)) /\1-$year /g" $1.bak > $1
	rm $1.bak
}

for file in automad/src/{client/admin,server}/{.,*,*/*,*/*/*,*/*/*/*}/*.{php,ts,less}
do
	if [[ -f $file ]]
	then
		bumpDate "$file"
	fi
done