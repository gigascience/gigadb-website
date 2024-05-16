Below are the agreed steps that will be the basic workflow for curators following the move to the new servers & tools:

Last updated: 16 May 2024

0 - Creating a dropbox - currently tech team will create them in batches for curators to use, but soon the curators will be able to create them themselves and specify userbox names (which will be able to be idividualised, e.g. use the MS submission ID and/or Dataset DOI (TBC))

1 - Provide the userbox login details to the authors for them to upload their files into private dropboxes on the new FTP server (see [template letters](https://sites.google.com/gigasciencejournal.com/giganet/gigadb/google-mail/letter-templates?authuser=0#h.xy93n9deqqjy))

2 - Curators are able to access userdrop boxes via SSH into bastion server using thier personal credentials, or via FTP using the authors FTP credtials(above).

3 - Curator to check the content of the userbox (by accessing the private drop boxes via SSH). 
Check MD5 of files provided by authors match the values provided by authors
Manipulate files as required (tar/untar etc)
At the appropriate point make a copy of the submitted files for backup purposes:

$ cp -r /share/dropbox/user109 /share/dropbox/user109.orig

If you need to restore the 'orig' to the userbox:
$ rm -rf /share/drobox/user109 
$ cp -r /share/dropbox/user109.orig /share/dropbox/user109 

 
4 - Curators can provide reviewer access details to the editorial team to share with peer-reviewers

If the MS is rejected at peer-review - we didn't discuss what happens yet, but the previous workflow was that the data is kept in the private dropbox for 3 months and then deleted (a manual process carried out by CIH)

If the MS passes peer-review:

5 - Curators prepare the upload spreadsheet & userbox area:

    a - Get the user dropbox looking as we expect the final dataset to look
    Must ensure only relevant files are present (i.e. files match exactly whats in the spreadsheet - except for the readme_nnnnnn.txt)

    b - Prepare the upload spreadsheet
    Include the readme_nnnnnn.txt file in the file list
    Validate the spreadsheet - a 2nd curator? and/or checklist, includes checking files list matches dropbox (TBC)

7 - Execute the md5.sh on the bastion server: **(this script is yet to be deployed)**
The script will create two files, <DOI>.md5 and <DOI>.filesize, in userbox

8 - Send spreadsheet to tech for upload to DB (add spreadsheet to google drive and email tech team)
This includes creation of new user accounts for authors that have not previously submitted, those new users will have in-activate GigaDB accounts, we are working on fixing the bug that prevents users from activating their account, and this doc will be updated when that is ready. For now we proceed with unactivated user accounts.

9 - After spreadsheet upload has been done (tech will notify curators):
    a - Curators can create mockup link in admin pages and visually inspect it
    b - Do any manual curation on things as required via admin pages

10 - Once the dataset details are curated in admin pages, curators can create the readme file (using a script) in the userbox on the bastion server.**(this script is yet to be deployed)**
The script is currently part of the post-upload script, but tech will be splitting it out into a separate script that curators can run.

11 - Curators can then run the check files script  **(this script is yet to be deployed)**
This will checks DB vs userbox (and vice versa)
Curators should fix any discrepencies found.

12 - Curators run the update md5& filesize script on bastion server **(this script is yet to be deployed)**
This script will add the MD5 and size-in-bytes values from userbox to the DB

13 - Curators can now copy the userbox files to the relevant Wasabi public live area using rclone.
Logged into the bastion server use the rclone "copy" command to copy the entire userbox to Wasabi, the generic command looks like this:
$ rclone copy source:sourcepath dest:destpath
[rclone hepl page](https://rclone.org/commands/rclone_copy/)

example command line:
# make sure I am in the relevant userbox (e.g. user109)
$ cd /share/dropbox/user109
# then run the rclone copy command by specifying the source as the current directory with a '.' and the destination as the relevant DOI in the wasabi live area:
$ rclone copy . wasabi_ChrisH:gigadb-datasets/live/pub/10.5524/102001_103000/102524

14 - Curators can then send mockup to authors for them to check over & perform the curators second eyes check (either way around is OK as long as both are done)

15 - If all is agreed, its ready to be published and mint the DOI via admin page

If something is wrong with Files - make DB changes via admin pages and repeat steps 9-13 on bastion
NB- if lots of files are changed them consider rerunning upload script.

NB - the publication date needs to be set in step 9b, which means its a bit of a guess as to when it will be published! That date gets put in the readme file at step 10. If the actual publication is more than 1 week from the date set in step 9b then please adjust the date and regenerate the readme file (step10) and correct the size and md5 value of the readme file within the database (as they will have changed when you recreate the readme file).

