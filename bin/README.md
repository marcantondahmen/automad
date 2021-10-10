# Bin

There are some little scripts in this directory to keep the development workflow consistent by automating certain things such as releases, tests, and the handling of feature/bugfix/refactor branches.

- [Features, Bugfixes and Refactor Branches](#features-bugfixes-and-refactor-branches)
	- [Starting a Branch](#starting-a-branch)
	- [Commiting on a Branch](#commiting-on-a-branch)
		- [Excluding Commits from the Changelog](#excluding-commits-from-the-changelog)
	- [Finishing a Branch](#finishing-a-branch)
- [Releases](#releases)
- [Tests](#tests)

## Features, Bugfixes and Refactor Branches

In case a feature, bugfix or refactoring process is expected to be more complex than just a single commit, it should be done on a separate branch.

### Starting a Branch

To start a new feature/bugfix/refactor branch simply run the following command:

    bash bin/start.sh 

Follow the instructions by selecting a type, defining a scope and providing a name. The script will checkout the `develop` branch and then create and checkout a new branch following this naming scheme:

    feat/scope/feature_name 

### Commiting on a Branch

Please note that *GitHub* releases are created when a tag is pushed to `origin`. The included changelog is generated out of all commit messages back to the previous release that start with either `feat`, `fix` or `refactor` **and** follow the [conventional commits](https://www.conventionalcommits.org/en/v1.0.0/) specification.

#### Excluding Commits from the Changelog 

When a new *feature*, a *bugfix* or refactoring is finished, the branch name is used to create a commit message for the merge that qualifies to be included into the changelog. Therefore all commits on that branch are **not** supposed to generate changelog entries. To exclude such commits, a message simply has to violate the requirements by not starting with `feat`, `fix` or `refactor` or not following the *conventional commits* specification.

### Finishing a Branch

When finishing a *feature*, a *bugfix* or refactoring, the branch is merged back to the `develop` branch. Its name is used to generate the commit message following this pattern according to the example above:

    feat(scope): feature name

After merging and pushing successfully, the branch is deleted locally and on the remote.

## Releases

When a new version is about to be finished, a sequence of tasks has to be completed in order to pusblish a release. This process is automated and can be started using the following command:

    bash bin/release.sh

This will start the release process and initiates the following sequence of tasks:

1. Kill all running *Gulp* (watch) tasks
2. Check whether the current branch is `develop`
3. Run unit tests
4. Bump version numbers by selection between a patch, minor or major version jump
5. Run build tasks for UI and themes (*Gulp*)
6. Commit dist files
7. Merge `develop` into `master` branch
8. Create tag
9. Push

## Tests

To make sure, tests are using the correct version of *PHPUnit* with the correct configuration, the following command can be used to run tests:

    bash bin/phpunit.sh