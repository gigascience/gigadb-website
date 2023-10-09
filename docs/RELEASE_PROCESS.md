# Releases How-To

## CHANGELOG.md

### Unreleased section

The `Unreleased` section is always at the top of the `CHANGELOG.md` document.
Any merged features should be added under the `Unreleased` section, with the most recent feature at the top.

### x.y.z sections

Where `x` is major version increase with breaking changes, user training, major rewrites or sub project added,  and/or infrastructure changes, `y` is  minor version increase that should be the default for most new features, and `z` is version increase for bug fixes

`x.y.z` sections should be ordered from highest version to the lowest. Within each section the feature selected for that version should appear with the most recent feature at the top.

## How to decide what and when for a new release

Deployment to the live production environment should always come from a release, so that we can assert and track with absolute certainty what set of features is currently on live production environment from a technical perspective (git tags) and business perspective (CHANGELOG).


What goes into a release should be decided during the weekly Sprint Status meeting when curators are present, using the CHANGELOG as working document.
We should have a recurring slot in that meeting to talk about what's going to be in next release, and when we do it. 


## Make a new release

0. Prerequisite: you have a checkout of gigascience/gigadb-website with the `.env` file for deploying to Upstream
1. When a new release is decided, select the commit hash in the `develop` branch up to which we want to cut the new release.
2. In the CHANGELOG, pick the features from the `Unreleased` section from the bottom of the pile up to the one that match the commit hash from previous step.
3. The selected features should be moved from that section into a new `x.y.z` section where `x.y.z` is a [semantic versioning](https://semver.org) based increment to the previous version.
4. The remainder of unreleased features (those at the top of the pile), if any, stay in the `Unreleased` section.
5. Create a new git "annotated tag" (not the lightweight  tag) as shown below:

```
$ cd gigascience/gigadb-website # this is the checkout of the official gigadb-website repository, not your fork
$ git checkout develop
$ git tag  -as vx.y.z <commit hash after committing the changelog changes> -m "new release x.y.z"
$ git push origin vx.y.z
```
6. Write the new version in the VERSION file:
```
$ echo "vx.y.z" > VERSION
```

>**Note 1**: Only code owners should perform this task

>**Note 2**: The changes to the CHANGELOG made in step 2 and 3 have to be directly committed to the develop branch and it's the resulting commit hash which is to be used in the `git tag` command, not the one that we've selected in step 1, otherwise a given release's changelog will have incorrect information

>**Note 3**: For security reason, it's better you have setup commit signing before performing this task

See: 
* https://docs.github.com/en/authentication/managing-commit-signature-verification/signing-commits
* https://docs.github.com/en/authentication/managing-commit-signature-verification/telling-git-about-your-signing-key

I believe SSH keys could be used on Github, but it's vendor lock-in and the industry best practice is to use GPG keys for this purpose.
GnuPG can be downloaded here: https://gnupg.org/download

Alternatively, the not-free tool https://gpgtools.org for macOS will also work as it installs gnupg command line tool.  
Even though its main purpose is to be used with emails, it has a nice GUI for managing the keys from gnugpg.


## Gitlab pipeline for tag

* The build and deploy stages for the live production environment should be constrained to release tags
* The build and deploy stages for the staging production environment should work for features and tags branches alike as well as the develop branch

## Display of versions

* The Github home for gigadb-webiste lists all the tags in the right-hand sidebar under the "Releases" section.
* The current version on a given environment will be shown on the admin home of gigadb-website
