# Bin

There are some little scripts in this directory to keep the development workflow consistent by automating certain things
such as creating releases and the handling of feature/bugfix/refactor branches.

## Features, Bugfixes and Refactor Branches

In case a feature, bugfix or refactoring process is expected to be more complex than just a single commit, it should be done on a separate branch.

### Starting a Branch

To start a new feature/bugfix/refactor branch simply run the following command:

    bash bin/start.sh

Follow the instructions by selecting a type, defining a scope and providing a name.
The script will checkout the `develop` (or `v2`) branch and then create and checkout a new branch following this naming scheme:

    feat/scope/feature_name

### Commiting on a Branch

Please note that the `CHANGELOG.md` and _GitHub_ releases notes are created automatically generated from commit messages
that start with either `feat`, `fix` or `refactor` **and** follow the [conventional commits](https://www.conventionalcommits.org/en/v1.0.0/) specification.

#### Excluding Commits from the Changelog

When a new _feature_, a _bugfix_ or refactoring is finished, the branch name is used to create a commit message for the merge that qualifies to be included into the changelog.
Therefore all commits on that branch are **not** supposed to generate changelog entries.
To exclude such commits, a message simply has to violate the requirements by not starting with `feat`, `fix` or `refactor` or not following the _conventional commits_ specification.

### Finishing a Branch

When finishing a _feature_, a _bugfix_ or refactoring, the branch is merged back to the `develop` (or `v2`) branch.
Its name is used to generate the commit message following this pattern according to the example above:

    feat(scope): feature name

After merging and pushing successfully, the branch is deleted locally and on the remote.

## Releases

When a new version is about to be finished, a sequence of tasks has to be completed in order to pusblish a release.
This process is automated and can be started using the following command:

    bash bin/release.sh

This will start the release process and initiates the following sequence of tasks:

1. Check whether the current branch is `develop`
2. Run tests
3. Update language packs
4. Bump version numbers by selection between a patch, minor or major version jump
5. Generate changelog
6. Commit
7. Merge `develop` or `v2` into `master` branch
8. Create tag
9. Push

## Generating a Changelog

A changelog can be generated from the Git log by running the following command:

    bash bin/changelog.sh 1 >body.md

This will generate the changelog between the two latest tags and write it to a file called `body.md`.
This file can be used in order to populate the release notes body.

A changelog for an upcoming relase including all new changes grouped under a new version tag that is not created yet
can be generated as follows:

    bash bin/changelog.sh 25 2.0.0 >CHANGELOG.md

This will essentially create a changelog where the newest version will be 2.0.0 after commiting and tagging.

## Bump Year in Files

In order to automatically bumb the year in the file header the `bin/bump-year.sh` util can be used.

## Finding Unused CSS Classes

In order to keep only the needed CSS in the code base, the `bin/fin-unused-classes.sh` util can be used to quickly identify dead code.
