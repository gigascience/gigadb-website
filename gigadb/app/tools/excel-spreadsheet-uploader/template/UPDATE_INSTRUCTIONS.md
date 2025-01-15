# Update the dataset spreadsheet template

Below are instructions for safely making changes to the dataset spreadsheet template.
For more  information about that document's usage, check the corresponding [Giganet user guide](https://sites.google.com/gigasciencejournal.com/giganet/gigadb/curation/spreadsheet-upload-scripts)

>note: It's fine to keep past file-name encoded versions of the template for compatbility with already circulating documents, but going forward, [Do not encode some kind of version in the file name](https://carpentries-incubator.github.io/git-novice-branch-pr/01-basics/), as Git is already versioned, and [it's easy](https://github.com/gigascience/gigadb-website/commits/develop/gigadb/app/tools/excel-spreadsheet-uploader/template) accessing previous versions of the file. So adding some kind of version in the file name will confuse everyone who need interacting with only the latest version of that file and defeats the purpose of storing this file in Git in the first place

## Using Github Desktop

1. Assuming you have already cloned Gigascience's gigadb-website Github project in Github Desktop
2. Open Github Desktop, and ensure that:
    * The `gigadb-website` project is selected as the "Current repository` in the toolbar
    * The `develop` branch is selected as the "Current branch" in the toolbar
    * Click "Fetch origin" to ensure you have the latest state of the truth from the server
    * The "Changes" column on the left should be empty
3. Identify where Github Desktop has clone the repository on your computer and navigate to it.
4. On your computer, make the change you want to the file in `gigadb/app/tools/excel-spreadsheet-uploader/template`
  - replace the current template file with the new version 
  - remember to include a testable example file 
5. Then return to Github Desktop, you should see that the "Changes" column on the left is not empty and reflects the changes your made
6. Create a new branch by clicking on the drop-down menu right of "Current branch" in the toolbar
7. Click on "New branch", choose a meaningful name (e.g: fix-doi-hint), and click "Create branch"
8. Then make sure that out of the two options shown, you select "Bring my changes to (name of the branch just created)"
9. Click "Switch branch", "Current branch" in the toolbar should now show the name of the branch with your changes
10. It is time to commit the changes by adding a summary and description to the commit message form at the bottom left, and then click "commit to (name of the just created branch)"
11. The main pane will show a list of what to do next. Choose the top one (already highlighted) "Publish your branch" by clicking the "Publish branch" button
12. The top option in the main pane is highlighted and says "Preview the pull request from you current branch", and has a button "Preview Pull Request" that you click
13. A preview pane will pop up, check that:
    *  it says "commit into base:develop from (name of the branch with your changes)"
    *  the main pane contain the change you want to publish and be reviewed
    *  At the bottom left, it should have a green tick "Able to merge"
14. If all good, you can click "Create Pull Request"
15. Your web browser will open on a new Pull request form on the Gigascience's gigadb-website repository
16. Fill in the form as instructed inline
17. Click on "Create Pull Request" (if instead you see "Draft Pull Request", click the drop-down menu to bring the other option)
18. Add the pull request to the current sprint project by pasting its link into a new note in the Tasks To Do column. Sprint projects are listed in [Projects (classic)](https://github.com/orgs/gigascience/projects?type=classic). Then slide the note in the "👆 Reviewing required" section



## Reviewing

During the reviewing, comments may be exchanged on the pull requests directly between reviewers and author.

If additional changes to the spreadsheet are necessary, a number of rounds of steps 9, 10 and 11 are to be performed again. When the pull request has been approved by two members of the tech team, it will be merged to the codebase

## Merging and sharing with the users

When two developers have approved the pull request, we will merge the pull request and will send a general email announcing the change with description and instructions for downloading the file.
