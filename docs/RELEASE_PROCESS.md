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
The output of this meeting is an updated CHANGELOG.md, committed directly to the `develop` branch that describes the new release following the following template:
```
 ## v3.1.0  
                                                                                                                      
  - Feat: feature 1
  - Fix: fix 1
  - Feat: feature 1

```

>**Note 1**: In the CHANGELOG, pick the features from the `Unreleased` section from the bottom of the pile up to the one that match the commit hash from previous step.
>**Note 2**: The selected features should be moved from that section into a new `x.y.z` section where `x.y.z` is a [semantic versioning](https://semver.org) based increment to the previous version.
>**Note 3**: The remainder of unreleased features (those at the top of the pile), if any, stay in the `Unreleased` section.


## Make a new release

0. Prerequisite: you have a checkout of gigascience/gigadb-website's `develop` branch and CHANGELOG.md has been updated with new release `x.y.z`
```
$ cd gigascience/gigadb-website # this is the checkout of the official gigadb-website repository, not your fork
$ git checkout develop
$ grep X.Y.Z CHANGELOG.md
## vX.Y.Z 
```
1. Identify the commit hash in the `develop` branch up to which we want to include in the new release.
2. Create a new **signed**, **annotated** git tag (not the lightweight tag) as shown below:

```
$ git tag -as vx.y.z <selected commit hash> -m "new release x.y.z"
```
3. Push the tag to the remote repository
```
$ git push origin vx.y.z
```
4. Update CHANGELOG.md to include the date and the commit hash for the release (look at previous releases as template), then commit and push.

>**Note 1**: Only code owners should perform this task
>**Note 2**: For security reason, it's better you have setup commit signing before performing this task

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
