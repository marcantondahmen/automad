#!/bin/env bash

# Use this script to generate a changelog for a defined number of releases
# or to generate the release note for a singe tag.
#
# bash bin/changelog.sh <num> [<tag>]
#
# This example generates a changelog for the last 25 releases and
# also groupes all "unreleased" changes under the version 2.0.0 on top
# of the changelog. This is useful when create release notes during the local
# release process, just before the actual tag 2.0.0 is created.
#
# bash bin/changelog.sh 25 2.0.0 >CHANGELOG.md
#
# In order to create the body of the release notes for just the last tag,
# the following example can be used:
#
# bash bin/changelog.sh 1 >body.md

githubUser=marcantondahmen
githubProject=automad
commitUrlFormat="https://github.com/$githubUser/$githubProject/commit/%H"
commitLinkFormat="[%h]($commitUrlFormat)"
logFormat="%s ($commitLinkFormat)"

getCommitsBetween() {
	git log --format="$logFormat" "$1".."$2"
}

generateSection() {
	local title="$1"
	shift
	local items=("$@")

	if [[ ! -z $items ]]; then
		echo "### $title"
		echo

		IFS=$'\n' sorted=($(sort -n <<<"${items[*]}"))
		unset IFS

		for line in "${sorted[@]}"; do
			msg=$(sed -E "s/\w+!?\: //" <<<$line)
			msg=$(sed -E "s/\w+\((\w+)\)!?\: /**\1**: /" <<<$msg)
			echo "- $msg"
		done

		echo
	fi

}

generateLogBetween() {
	readarray -t output < <(git log --grep="BREAKING" --grep="!:" --format="$logFormat" "$1".."$2")
	generateSection "Breaking Changes" "${output[@]}"

	readarray -t output < <(getCommitsBetween $1 $2 | grep -E '^feat.*\: ')
	generateSection "New Features" "${output[@]}"

	readarray -t output < <(getCommitsBetween $1 $2 | grep -E '^fix.*\: ')
	generateSection "Bugfixes" "${output[@]}"
}

generateChangelog() {
	number=$1
	newTag=$2
	maxReleases=$((number + 1))

	if [[ $number > 1 ]]; then
		echo "# Changelog"
		echo
	fi

	current=''
	previous=''

	if [[ ! -z $newTag ]]; then
		previous=HEAD
	fi

	for tag in $(git tag --sort=-version:refname | head -n $maxReleases); do
		current=$previous
		previous=$tag

		if [[ -z "$current" ]]; then
			continue
		fi

		release=$(generateLogBetween $previous $current)

		if [[ -z "$release" ]]; then
			release="Minor changes and fixes."
		fi

		if [[ $number > 1 ]]; then
			title=$current

			if [[ "$current" == "HEAD" ]]; then
				title=$newTag
			fi

			echo "## [v$title]($(git log -1 --format="$commitUrlFormat" $current))"
			echo
			echo "$(git log -1 --format="%aD" $current)"
			echo
		fi

		echo "$release"
		echo
	done
}

append() {
	log="$1"
	commitHash=$2
	line=$3

	# Adding lines after matches.
	# https://stackoverflow.com/a/48406504
	# https://stackoverflow.com/a/54388421
	echo "$(sed -e '0,/'"$commitHash"'/!b;//a\'$'\n'"$line" <<<"$log")"
}

patch() {
	log="$1"

	# Adding lines.
	item='- change minimum required PHP version to 8.2 ([5efeb8e](https://github.com/marcantondahmen/automad/commit/5efeb8eb083544f7c437ea8c0540e2ea2895f0e6))'
	log="$(append "$log" 4579f83e1d4bc5affb5dde7dc3a506c7e45e140f "$item")"
	item='- use symfony/mailer for sending emails ([5efeb8e](https://github.com/marcantondahmen/automad/commit/5efeb8eb083544f7c437ea8c0540e2ea2895f0e6))'
	log="$(append "$log" 96a1f184ab0435c82a1a382a8c3b8de5e3a2751d "$item")"

	# Removing lines.
	log="$(echo "$log" | grep -v b8569b284c77971a01f96f0aa6bd57b826740b8b)"

	echo "$log"
}

# On macOS use gsed as replacement.
if [[ "$OSTYPE" == "darwin"* ]]; then
	shopt -s expand_aliases
	alias sed="$(brew --prefix)/bin/gsed"
fi

patch "$(generateChangelog $1 $2)"
