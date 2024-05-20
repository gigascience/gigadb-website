# Below are the agreed steps that will be the basic workflow for curators following the move to the new servers & tools:

Last updated: 16 May 2024
Added in all the other curation steps from our "normal" [workflow intranet page](https://sites.google.com/gigasciencejournal.com/giganet/gigadb/curation/gs-curation-step-by-step?authuser=0) that the curators are more familiar with.

## Preview "get-data" steps

1. Editors flag MS for "get-data" in MS submission system (EM or RVT)

2. Someone (CIH/NN/NQ) adds the yellow "get-data" flag in the curators DOI-tracking sheet (see [tracking page](https://sites.google.com/gigasciencejournal.com/giganet/gigadb/curation/tracking?authuser=0) for details about the tracking sheet).

3. The next available curator picks up that item to-do and puts their name on it. There is no selection of curator based on MS subject matter.
NB - there is a [pre-review checklist](https://docs.google.com/spreadsheets/d/1tcoIPY7VQ3MuT8hihvwTGHxFwKKDhKo5tikHF6_sfQo/edit#gid=0) for curators.

4. Curator reads salient parts of MS (e.g. abstract, methods, figures, data availability section)
    During DataSeer Phase2 trail- The normal process will be slightly different while we test the use of DataSeer (details to be added as we make them up!)
       - Upload copy of MS to DataSeer and "run" DS - will be shown how to do that by DS staff, then add details here
       - Use the DS curation interface to mark-up salient parts of the MS
       - produce the DS report
       - tailor the report to be sent to the author as per steps 5-7 below:

5. If required, Create a dropbox for the authors to use - currently tech team will create them in batches for curators to use, but soon the curators will be able to create them themselves and specify userbox names (which will be able to be idividualised, e.g. use the MS submission ID and/or Dataset DOI (TBC))

6. Provide the userbox login details to the authors for them to upload their files into private dropboxes on the new FTP server (see [template letters](https://sites.google.com/gigasciencejournal.com/giganet/gigadb/google-mail/letter-templates?authuser=0#h.xy93n9deqqjy))
Sometimes discussions between authors and curators maybe required to clarify things (CC'ing Eds)

7. Curator to check the content of the userbox by accessing the private drop boxes via SSH to the bastion server[see notes on using the bastion host](https://github.com/pli888/gigadb-website/blob/curator-docs/docs/curators/FILE_MANAGEMENT.md). 
    - Check MD5 of files provided by authors match the values provided by authors
    - Manipulate files as required (tar/untar etc)
    - At the appropriate point make a copy of the submitted files for backup purposes:

      $ cp -r /share/dropbox/user109 /share/dropbox/user109.orig
        
        If you need to restore the 'orig' to the userbox:

      $ rm -rf /share/drobox/user109 

      $ cp -r /share/dropbox/user109.orig /share/dropbox/user109 

8. Complete the [pre-review checklist](https://docs.google.com/spreadsheets/d/1tcoIPY7VQ3MuT8hihvwTGHxFwKKDhKo5tikHF6_sfQo/edit#gid=0).

9. Confirmation of “get-data” complete is sent (GS by email, GB via RVT) to authors (CC’ing editorial@), see [template email](https://sites.google.com/gigasciencejournal.com/giganet/gigadb/google-mail/letter-templates?authuser=0#h.dv5s2q6iengv).

Additional curation details on the pre-review step are available [here](https://sites.google.com/gigasciencejournal.com/giganet/gigadb/curation/gs-curation-step-by-step?authuser=0#h.qyzp07dvajfb).

If the MS is rejected at peer-review - we didn't discuss what happens yet, but the previous workflow was that the data is kept in the private dropbox for 3 months and then deleted (a manual process carried out by CIH)

## Post peer-review, create dataset steps
If the MS passes peer-review.

10. The Eds flag the MS as “prepare dataset” in their system (EM or RVT)

11. Someone (CIH/NN/NQ) move the relevant row in the curators DOI-tracking sheet to the prepare dataset sheet

12. The next available curator picks up that item to do and puts their name on it
NB We should attempt to keep the same curator from the get-data step, but that’s not always possible due to workload.

13. Check all prior emails in database@ mailbox

14. Check notes in DOI tracking sheet and the pre-review checklist.

15. Check the DataSeer report to make sure all the expected items on that have been done

16. Retrieves the Pre-filled spreadsheet from the database@ mailbox
     Check that the spreadsheet is the [current](https://github.com/gigascience/gigadb-website/tree/develop/gigadb/app/tools/excel-spreadsheet-uploader/template) version, if not, copy the metadata to the current template version before continuing.

17. <em>Do curation</em> (if communications with author are required CC Eds), [this template email](https://sites.google.com/gigasciencejournal.com/giganet/gigadb/google-mail/letter-templates?authuser=0#h.fay77fv6vmv4) may be useful.
    - Read the most recent revised MS & sup files to ensure all data are available
    - Ensure all external data are public and appropriately curated/annotated (SRA has correct samples, Github has license, other repo’s are suitable etc…)
    - Get the user dropbox looking as we expect the final dataset to look 
        Rename as required, request extra files etc.
        Must ensure only relevant files are present (i.e. files match exactly whats in the spreadsheet - except for the readme_nnnnnn.txt)
    - Complete upload spreadsheet (sample and files tabs)as appropriate ([see intranet guidelines on spreadsheet use](https://sites.google.com/gigasciencejournal.com/giganet/gigadb/curation/excel-submission-sheet-guidelines?authuser=0), 
        - Ensure samples tab has Info rows removed.
        - The files tab should include the readme_<DOI>.txt filename
        - Spreadsheet should be named following this convention: GigaDBUpload_v18_<DOI-number>_<MS-ID>_<short-human-readable-name>.xls
        - In the event an update is required please append v2 (or v3 etc) after the shortname e.g.: GigaDBUpload_v18_<DOI-number>_<MS-ID>_<short-human-readable-name>_v2.xls
        - Validate the spreadsheet - a 2nd curator? and/or checklist, includes checking files list matches dropbox (TBC)

18. Execute the md5.sh on the bastion server: **(this script is yet to be deployed)**
The script will create two files, <DOI>.md5 and <DOI>.filesize, in userbox
    NB Do run as "sudo" and do Not use "&", e.g. 
	$ sudo /usr/local/src/gigadb/md5.sh **(need to confirm the exact location of the script in Bastion)**

19. Send spreadsheet to tech for upload to DB (add spreadsheet to google drive and email tech team)
    This includes creation of new user accounts for authors that have not previously submitted, those new users will have in-activate GigaDB accounts, we are working on fixing the bug that prevents users from activating their account, and this doc will be updated when that is ready. For now we proceed with unactivated user accounts.
    - Upload completed spreadsheet to the [submission_spreadsheets.dir](https://drive.google.com/drive/folders/1znaz-pQIeQO-tGTr2dMiARYWbi_vptdd?usp=sharing) folder.
    - Email database admin to ask them to upload it (tech @ gigasciencejournal.com)
    - DB admin will reply by email to inform when its been uploaded and/or if errors found in spreadsheet.

20. After spreadsheet upload has been done (tech will notify curators):
    - find the dataset in the gigadb.org admin pages and click "Create/Reset Private URL" 
    - add your user name as the handling curator 
    - add the MS ID.
    - set a publication date (usually 2 weeks from now)
    - add the thumbnail image (& check its details uploaded from spreadsheet are correct)
    - click "save"
    - check mockup view for all appropriate metadata (you may wish to use the 2nd eyes checklist as a guide)

21. Once the dataset details are curated in admin pages, curators can create the readme file (using a script) in the userbox on the bastion server.**(this script is yet to be deployed)**
The script is currently part of the post-upload script, but tech will be splitting it out into a separate script that curators can run.

22. Curators can then run the check files script  **(this script is yet to be deployed)**
This will checks DB vs userbox (and vice versa)
Curators should fix any discrepancies found.

23. Curators run the update md5& filesize script on bastion server **(this script is yet to be deployed)**
This script will add the MD5 and size-in-bytes values from userbox to the DB

24. Curators can now copy the userbox files to the relevant Wasabi public live area using rclone.
Logged into the bastion server use the rclone "copy" command to copy the entire userbox to Wasabi, the generic command looks like this:

$ rclone copy source:sourcepath dest:destpath

[rclone help page](https://rclone.org/commands/rclone_copy/)

example command line:
make sure I am in the relevant userbox (e.g. user109)

$ cd /share/dropbox/user109

then run the rclone copy command by specifying the source as the current directory with a '.' and the destination as the relevant DOI in the wasabi live area:

$ rclone copy . wasabi_ChrisH:gigadb-datasets/live/pub/10.5524/102001_103000/102524

25. Curators can then send mockup to authors for them to check over & perform the curators second eyes check (either way around is OK as long as both are done)

If all is agreed, its ready to be published and mint the DOI via admin page

If something is wrong with Files - make DB changes via admin pages and repeat steps 9-13 on bastion
NB- if lots of files are changed them consider rerunning upload script.

NB - the publication date needs to be set in step 9b, which means its a bit of a guess as to when it will be published! That date gets put in the readme file at step 10. If the actual publication is more than 1 week from the date set in step 9b then please adjust the date and regenerate the readme file (step10) and correct the size and md5 value of the readme file within the database (as they will have changed when you recreate the readme file).

26. Ensure the dataset status is set to private and all corrections from the 2nd eyes check are completed, and the release date is set to the correct date, click "save" on dataset admin page.

27. Mint the DOI
28. Release dataset by setting the status to "published"
29. Inform the authors and CC editorial, see [template email](https://sites.google.com/gigasciencejournal.com/giganet/gigadb/google-mail/letter-templates?authuser=0#h.iqmqic6waksa).
