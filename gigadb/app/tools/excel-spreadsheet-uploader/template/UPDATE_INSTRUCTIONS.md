# Update the dataset spreadsheet template

Below are instructions for safely making changes to the dataset spreadsheet template.
For more  information about that document's usage, check the corresponding [Giganet user guide](https://sites.google.com/gigasciencejournal.com/giganet/gigadb/curation/excel-submission-sheet-guidelines)

1. Ensure you have a personal [fork](https://docs.github.com/en/get-started/quickstart/fork-a-repo) of https://github.com/gigascience/gigadb-website

2. Clone your fork of the gigadb-website repository

```
$ git clone https://github.com/<your GitHub username>/gigadb-website
```

3. Change to the directory where the template is: 

```
$ cd gigadb-website/gigadb/app/tools/excel-spreadsheet-uploader/template/
```

4. At present, there should only be two files in that directory, this `UPDATE_INSTRUCTIONS.md` file and the dataset spreadsheet template

5. If you need to make a change, create a new branch

```
$ git checkout -b <meaningful name with no spaces/special characters>
```

6. Now, feel free to make the changes you feel is necessary to that file.

>note: [Do not encode some kind of version in the file name](https://carpentries-incubator.github.io/git-novice-branch-pr/01-basics/), as Git is already versioned, and [it's easy](https://github.com/gigascience/gigadb-website/commits/develop/gigadb/app/tools/excel-spreadsheet-uploader/template) accessing previous versions of the file. So adding some kind of version in the file name will confuse everyone who need interacting with only the latest version of that file and defeats the purpose of storing this file in Git in the first place

7. Stage and commit the change locally (you may want to have multiple rounds of that step)

```
$ git add .
$ git commit -m "description of the changes"
```

8. When you are ready to share the changes, you can push them to the remote repository

```
$ git push --set-upstream origin
```

9. Now you can create [a pull request](https://carpentries-incubator.github.io/git-novice-branch-pr/10-pull-requests/) that will notify the tech team that a change need to be reviewed and merged in the codebase

Navigate to your fork on Github and switch to the branch `<meaningful name with no spaces/special characters>`, click on the `Contribute` drop-down menu and press the "Open pull request",
Then fill in the form as guided and save it. 
The developers will be notified of the pull request and will schedule its reviewing.

10. Reviewing

During the reviewing, comments may be exchange on the pull requests directly between reviewers and author.

If additional changes to the spreadsheet are necessary, a number of rounds of steps 6, 7 and 8 are to be performed again. When the pull request has been approved by two members of the tech team, it will be merged to the codebase

11. Merging and sharing with the users

The developers will merge the pull request and will send a general email announcing the change with description and instructions for downloading the file.
