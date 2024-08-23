# Releases How-To

## Merging Pull requests (PR)

* Click "Merge Pull Request" (after ensuring that the "Create a merge commit" is selected in the drop-down)
* Copy the first-from-the-top new entry in the modified CHANGELOG.md from the PR, and append it without the issue number to the left of the Merge title field preserving the "Merge pull request #dddd" part of the default text but surrounding it with parenthesis
* Copy the content under "This is a pull request for the following functionalities:" from the first section of the PR in the Merge message field
* Add the issue number (including the hash symbol) in the Merge message field prefixed with "Refs:".
* Click on "Confirm merge"

>**Note**: In the rare eventually a PR is associated with more than one issue, add all the issue numbers after a "Refs:" prefix in the Merge message field.

## CHANGELOG.md

### Formatting

Each line of the file must correspond to a pull request (PR) to the `develop` branch of https://github.com/gigascience/gigadb-website.
New lines are added at the top.
Each line starts with a two-part prefix, the first part can be one of "Feat" or "Fix".
It is then followed, as second part of the prefix by the Github issue number for which the PR is created for.
After the prefix and a colon separator, there should be a human readable short summary of the added-value(s) of the PR.

```
- Feat #45: Add a new methods to do some old things
```

As PRs are supposed to be small, most of the time a PR is only associated to one issue.
However, in the rare case a PR is associated with several Github issues, there should one line for each issue

```
- Feat #48: The contact form's captcha is now accessible
- Feat #47: The new user registration form's captcha is now accessible
```

It's also possible that it takes more one PR over time to resolve a given Github issue.
In that case, a prefix with the same Github issue will appears for multiple lines

```
- Feat #301: Fix correctly the validation for some models 
...
- Feat #306: The new user registration form's captcha is now accessible
- Feat #301: Fix the validation for some models
```

### Unreleased section

The `Unreleased` section is always at the top of the `CHANGELOG.md` document.
Any merged features should be added under the `Unreleased` section, with the most recent feature at the top.

### x.y.z sections

Where `x` is major version increase with breaking changes, user training, major rewrites or sub project added,  and/or infrastructure changes, `y` is  minor version increase that should be the default for most new features, and `z` is version increase for bug fixes

`x.y.z` sections should be ordered from highest version to the lowest. Within each section the feature selected for that version should appear with the most recent feature at the top.

## Making a release

Physically speaking, a release is a Git Tag pointing to a specific commit of the main `develop` branch 
that is pushed to the repository and has a pipeline triggered on Gitlab.

You will need to be familiar with the following documents before proceeding with making releases:
* docs/SETUP_CI_CD_PIPELINE.md
* docs/sop/DEPLOYING_TO_PRODUCTION.md

Some remarks about Gitlab pipeline with regards to releases:
* The build and deploy stages for the live production environment are constrained to release tags
* The build and deploy stages for the staging production environment work for features and tags branches alike as well as the develop branch

A Git tag has a label, which most of the time is a string representing the semantic version associated with a release.

* The Github home for gigadb-webiste lists all the tags in the right-hand sidebar under the "Releases" section.
* The Gigadb-website shows the tag label associated with the currently deployed release in the footer


### How to decide what and when for a new release

Deployment to the live production environment should always come from a release, so that we can assert and track with absolute certainty what set of features is currently on live production environment from a technical perspective (git tags) and business perspective (CHANGELOG).

What goes into a release should be decided during the weekly Sprint Status meeting when curators are present, using the CHANGELOG as working document.
We should have a recurring slot in that meeting to talk about what's going to be in next release, and when we do it. 
The output of this meeting is an updated CHANGELOG.md, committed directly to the `develop` branch that describes the new release following the following template:
```
 ## v3.1.0  
                                                                                                                      
  - Feat #1235: feature 2
  - Fix #1234: fix 1
  - Feat #1232: feature 1

```

>**Note 1**: In the CHANGELOG, pick the features from the `Unreleased` section from the bottom of the pile up to the one that match the commit hash from previous step.
>**Note 2**: The selected features should be moved from that section into a new `x.y.z` section where `x.y.z` is a [semantic versioning](https://semver.org) based increment to the previous version.
>**Note 3**: The remainder of unreleased features (those at the top of the pile), if any, stay in the `Unreleased` section.

### Committing the new release changelog

It's important to commit the changes to CHANGELOG.md before make the tag release, so that the updated CHANGELOG.md related
to the new release and listing its changes become part of the tag release.

```
cd gigadb-upstream
```
```
git checkout develop
```
```
git add CHANGELOG.md
```
```
git commit CHANGELOG.md -m "Updated CHANGELOG for release x.y.z"
```
```
git push origin
```

## Making a new release

To make a new release means creating a new git annotated git tag.
The tag will be associated with a commit that matches the change to the CHANGELOG.md that introduce the release to make.

```
git log --oneline --decorate --pretty=format:'%C(auto)%h%d (%ai) %cn <%ce> %s'
```
will show a line like this:
```
8744be559 (upstream/develop, origin/develop, origin/HEAD, develop) (2024-08-13 10:37:09 +0200) Rija Ménagé <rm.dev.git.commits@eml.cc> Updated CHANGELOG for release x.y.z
```

Then use the above commit hash to create an annotated tag:

```
git tag -as vx.y.z <commit hash> -m "new release x.y.z"
```

Update CHANGELOG.md to include the date and the commit hash for the release (look at previous releases as template)
```
## vX.Y.Z - 2024-08-13 - 8744be559 - 

  - Feat #1235: feature 2
  - Fix #1234: fix 1
  - Feat #1232: feature 1
```

Then commit and push
```
git add CHANGELOG.md
```
```
git commit CHANGELOG.md -m "Updated CHANGELOG for release x.y.z to add date and commit hash"
```
```
git push origin
```

Push the tag to the remote repository
```
git push origin vx.y.z
```

>**Note**: The order of the calls to `git push` is important. Make sure that tag is the pushed last,
> otherwise the automated deployment of the release to staging will be overwritten

### Post release deployment actions

When the release has been deployment to the live environment of the current production, the CHANGELOG.md doc 
will need to be updated again to indicate live deployment date

```
## vX.Y.Z - 2024-08-13 - 8744be559 - live since 2024-08-18

  - Feat #1235: feature 2
  - Fix #1234: fix 1
  - Feat #1232: feature 1
```

Then commit and push
```
git add CHANGELOG.md
```
```
git commit CHANGELOG.md -m "Updated CHANGELOG for release x.y.z to add live depoloyment date"
```
```
git push origin
```

### Git signing your annotated tags

For security reason, it's better you have setup commit signing before creating a git tag
See: 
* https://docs.github.com/en/authentication/managing-commit-signature-verification/signing-commits
* https://docs.github.com/en/authentication/managing-commit-signature-verification/telling-git-about-your-signing-key

_I believe SSH keys could be used on Github, but it's vendor lock-in and the industry best practice is to use GPG keys for this purpose.
GnuPG can be downloaded here: https://gnupg.org/download
Alternatively, the not-free tool https://gpgtools.org for macOS will also work as it installs gnupg command line tool.  
Even though its main purpose is to be used with emails, it has a nice GUI for managing the keys from gnugpg._

