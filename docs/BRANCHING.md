# GigaDB git branching model

The development of GigaDB uses [Git](https://git-scm.com) for
versioning tool its source code. It is important that you have a good
understanding of how to use git in order to develop GigaDB. This can
be gained by following the [git course on codecademy](https://www.codecademy.com/learn/learn-git)
and the [git lesson by Software Carpentry](http://swcarpentry.github.io/git-novice/).

In order to manage the different versions of the GigaDB source code
that is being written by developers, we use a [specific branching
model](http://nvie.com/posts/a-successful-git-branching-model/)
which has been described by Vincent Driessen:

<img src="http://nvie.com/img/git-model@2x.png">](http://nvie.com/posts/a-successful-git-branching-model/)
Declare attribution!

## Main branches

The GigaDB repository at [https://github.com/gigascience/gigadb-website](https://github.com/gigascience/gigadb-website)
is the central "truth" repo and can be referred to as `origin`. This
`origin` repo contains a number of branches. There are two main
branches which are always present:

* `master`
* `develop`

<img src="http://nvie.com/img/main-branches@2x.png">](http://nvie.com/posts/a-successful-git-branching-model/)

The `origin/master` branch is the main branch which contains code
that is ready for production deployment. This `origin/develop` branch
is sometimes known as the "integration branch". It contains the latest
code that has been developed and will be used in the next release of
GigaDB.

When the source code in the `develop` branch is ready to be released,
it is somehow merged into the `master` branch and tagged with a
production release version number.

## Supporting branches

A number of other branches are used to fix bugs, track new features
being developed and prepare for production releases in GigaDB:

* `new-feature/` branches
* `fix/` branches
* `release` branches
* `hotfix` branches

These branches have a limited life and will be deleted eventually.

### Feature and fix branches

May branch off from: `develop`

Must merge back into: `develop`

Branch naming convention: anything in the form of `new-feature/*` or
`fix/reported_bug` except `master`, `develop`, `release-*` or `hotfix-*`

<img src="http://nvie.com/img/fb@2x.png">](http://nvie.com/posts/a-successful-git-branching-model/)

Feature branches are used to develop new pieces of functionality for
a future release. This type of branch exists as long as the new
feature is in development and will be merged back into `develop` or
discarded.

Fix branches are similar to feature branches but are so called
because they contain new code to fix a bug in GigaDB. These branches
exist for as long as the bug is being fixed and will be merged back
into `develop`.

#### Creating a feature branch

When starting work on a new feature, branch off from the `develop`
branch:

```bash
$ git checkout -b new-feature/myfeature develop
Switched to a new branch "new-feature/myfeature"
 ```

#### Incorporating a finished feature on develop

When coding is complete on a new feature, it can be merged into the
`develop` branch to add them to the upcoming release:

```bash
$ git checkout develop
Switched to branch 'develop'
$ git merge --no-ff new-feature/myfeature
Updating ea1b82a..05e9557
(Summary of changes)
$ git branch -d new-feature/myfeature
Deleted branch new-feature/myfeature (was 05e9557).
$ git push origin develop
```

The --no-ff flag causes the merge to always create a new commit
object, even if the merge could be performed with a fast-forward.
This avoids losing information about the historical existence of a
feature branch and groups together all commits that together added
the feature.

#### Creating a fix branch

When starting work on a fixing a bug, branch off from the `develop`
branch:

```bash
$ git checkout -b fix/reported-bug develop
Switched to a new branch "fix/reported-bug"
 ```

#### Incorporating a fixed bug into develop

When code is complete for fixing a bug, it can be merged into the
`develop` branch to add them to the upcoming release:

```bash
$ git checkout develop
Switched to branch 'develop'
$ git merge --no-ff fix/reported-bug
Updating ea1b82a..05e9557
(Summary of changes)
$ git branch -d fix/reported-bug
Deleted branch fix/reported-bug (was 05e9557).
$ git push origin develop
```

The --no-ff flag causes the merge to always create a new commit
object, even if the merge could be performed with a fast-forward.
This avoids losing information about the historical existence of a
feature branch and groups together all commits that together added
the feature.


### Release branches

May branch off from: `develop`

Must merge back into: `develop` and `master`

Branch naming convention: `release-*`

Release branches help to prepare for a new production release and
allow for last minute changes as well as allowing for small bug fixes
and preparing metadata for the release (such as version number and
build dates). By doing this work on a release branch, the `develop`
branch is cleared to receive new feature for the next release.

A release branch is created when the `develop` branch contains all of
the changes to go into a new release.

#### Creating a release branch

Say, for example the current version of GigaDB is version 3 and the
`develop` branch contains code ready for the release of version 3.1.
We branch off the `develop` branch and give the release branch a name
reflecting the new release:

```bash
$ git checkout -b release-3.1 develop
Switched to a new branch "release-3.1"
$ ./bump-version.sh 3.1
Files modified successfully, version bumped to 3.1.
$ git commit -a -m "Bumped version number to 3.1"
[release-3.1 74d9424] Bumped version number to 3.1
1 files changed, 1 insertions(+), 1 deletions(-)
```

After creating a new branch and switching to it, a fictional shell
script called `bump-version.sh` is used to make some changes in the
code to reflect the new version which are then committed. This
release branch may exist for some time until the release is
officially rolled out onto the production GigaDB server. During this
time, bug fixes can be committed into this branch (rather than on the
develop branch). The addition of large new features is not allowed
which must be incorporated into the next new release.

#### Finishing a release branch

When the state of the release branch is ready to become a real
release, a number of steps need to be taken. The release branch is
merged with `master` since every commit on `master` is a new release.
Next, the master must be tagged for easy reference to this
historical version. Finally, the changes made on the release branch
must be merged into `develop` so that future releases contain its
bug fixes.

The first two steps in git:

 ```bash
$ git checkout master
Switched to branch 'master'
$ git merge --no-ff release-3.1
Merge made by recursive.
(Summary of changes)
$ git tag -a 3.1
 ```

The release is made and tagged for future reference.

To keep the changes made in the release branch, we need to merge
them into `develop`:

 ```bash
 $ git checkout develop
 Switched to branch 'develop'
 $ git merge --no-ff release-3.1
 Merge made by recursive.
 (Summary of changes)
 ```

 This step may lead to a merge conflict since we have changed the
 version number. If so, fix and commit it.

 Now we are really done and the release branch may be removed, since
 we don’t need it anymore:

 ```bash
 $ git branch -d release-3.1
 Deleted branch release-3.1 (was ff452fe).
 ```

### Hotfix branches

May branch off from: `master`

Must merge back into: `develop` and `master`

Branch naming convention: `hotfix-*`

<img src="http://nvie.com/img/hotfix-branches@2x.png">](http://nvie.com/posts/a-successful-git-branching-model/)

Hotfix branches are used to immediately fix critical bugs in a
production release of GigaDB. The hotfix branch is branched off from
the corresponding tag on the `master` branch that marks the
production version. This branch is required so that work on the
`develop` branch can continue simultaneously on the hotfix branch.

#### Creating a hotfix branch

Hotfix branches are created from the `master` branch. For example,
say version 3.0 is the currently running live and there is a severe
bug with it but changes on develop are unstable. We can create a
hotfix branch to fix the problem:

```bash
$ git checkout -b hotfix-3.0.1 master
Switched to a new branch "hotfix-3.0.1"
$ ./bump-version.sh 3.0.1
Files modified successfully, version bumped to 3.0.1.
$ git commit -a -m "Bumped version number to 3.0.1"
[hotfix-3.0.1 41e61bb] Bumped version number to 3.0.1
1 files changed, 1 insertions(+), 1 deletions(-)
```

Don’t forget to bump the version number after branching off!

Then, fix the bug and commit the fix in one or more separate commits.

```bash
$ git commit -m "Fixed severe production problem"
[hotfix-3.0.1 abbe5d6] Fixed severe production problem
5 files changed, 32 insertions(+), 17 deletions(-)
```

#### Finishing a hotfix branch

The bug has been fixed in the hotfix branch then its code has to be
merged back into `master` and also into `develop` so that the bugfix
included in the next release. This is similar to how release branches
are finished.

First update `master` and tag the release:

```bash
$ git checkout master
Switched to branch 'master'
$ git merge --no-ff hotfix-3.0.1
Merge made by recursive.
(Summary of changes)
$ git tag -a 3.0.1
```

Next, include the bugfix in the `develop` branch too:

```bash
$ git checkout develop
Switched to branch 'develop'
$ git merge --no-ff hotfix-3.0.1
Merge made by recursive.
(Summary of changes)
```

The one exception to this rule is that if a release branch currently
exists then the hotfix branch needs to be merged into this release
branch and not the `develop` branch.

Finally, remove the temporary branch:

```bash
$ git branch -d hotfix-3.0.1
Deleted branch hotfix-3.0.1 (was abbe5d6).
```

