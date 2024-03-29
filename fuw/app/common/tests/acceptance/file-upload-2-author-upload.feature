@author-fileupload @tusd
Feature:
 	As an author
 	I want to upload from my GigaDB profile page the files for my manuscript's dataset
 	So that the dataset can be reviewed and made available online

Background:
	Given there is "user" "Artie" "Dodger"
	And a dataset with DOI "000007" owned by user "Artie" "Dodger" has status "UserUploadingData"
	And filedrop account for DOI "000007" does exist

@ok
Scenario: Upload files button when dataset has appropriate status (UserUploadingData)
	Given I sign in as the user "Artie" "Dodger"
	And I wait "2" seconds
	When I go to "/user/view_profile#submitted"
	Then I should see "Your profile page"
	And the "Your Uploaded Datasets" tab is active
	And I should see a "Upload Files" link

@ok
Scenario: Upload files button when dataset has appropriate status (DataPending)
	Given a dataset with DOI "000007" owned by user "Artie" "Dodger" has status "DataPending"
	And I sign in as the user "Artie" "Dodger"
	And I wait "2" seconds
	When I go to "/user/view_profile#submitted"
	Then I should see "Your profile page"
	And the "Your Uploaded Datasets" tab is active
	And I should see a "Upload Files" link

@ok
Scenario: No Upload files button when dataset hasn't got to the appropriate status yet
	Given there is "user" "Chloe" "Decker"
	And a dataset with DOI "100008" owned by user "Chloe" "Decker" has status "AssigningFTPbox"
	And I sign in as the user "Chloe" "Decker"
	And I wait "2" seconds
	When I go to "/user/view_profile#submitted"
	Then I should see "Your profile page"
	And the "Your Uploaded Datasets" tab is active
	And I should not see a "Upload Files" link


@ok
Scenario: Pressing the upload button bring up File Upload Wizard upload screen
	Given I sign in as the user "Artie" "Dodger"
	And The user "Artie" "Dodger" is registered as authorised user in the API
	And I am on "/user/view_profile#submitted"
	And the "Your Uploaded Datasets" tab is active
	When I press "Upload Files"
	Then I should be on "/authorisedDataset/uploadFiles/"
	And I should see "Uploading files for the dataset 000007"
	And I should see "Drop files here, paste or browse"

# @file-upload @mkdir-failed-two-scenarios-later
# Scenario: Can add files to the upload queue
# 	Given I sign in as the user "Artie" "Dodger"
# 	And The user "Artie" "Dodger" is registered as authorised user in the API
# 	And I am on "/user/view_profile#submitted"
# 	And the "Your Uploaded Datasets" tab is active
# 	And I press "Upload Files"
# 	And I should be on "/authorisedDataset/uploadFiles/"
# 	And I wait "1" seconds
# 	When I attach the file "TheProof.csv" in the file drop panel
# 	And I wait "1" seconds
# 	Then I should see "TheProof.csv"

@ok @file-upload
Scenario: All files in the queue are uploaded
	Given I sign in as the user "Artie" "Dodger"
	And The user "Artie" "Dodger" is registered as authorised user in the API
	And I am on "/user/view_profile#submitted"
	And the "Your Uploaded Datasets" tab is active
	And I press "Upload Files"
	When I attach the file "TheProof.csv" in the file drop panel
	And I press "Upload 1 file"
	And I wait "1" seconds
	Then I should see the file upload completed

@ok @file-upload
Scenario: Queued files are all uploaded
	Given I sign in as the user "Artie" "Dodger"
	And The user "Artie" "Dodger" is registered as authorised user in the API
	And I am on "/user/view_profile#submitted"
	And the "Your Uploaded Datasets" tab is active
	And I press "Upload Files"
	When I attach the file "TheProof.csv" in the file drop panel
	And I press "Add more"
	And I attach the file "TheProof2.csv" in the file drop panel
	And I press "Upload 2 files"
	And I wait "1" seconds
	Then I should see the file upload completed

@ok
Scenario: There is a Next button
	Given I sign in as the user "Artie" "Dodger"
	And The user "Artie" "Dodger" is registered as authorised user in the API
	And I am on "/user/view_profile#submitted"
	And the "Your Uploaded Datasets" tab is active
	When I press "Upload Files"
	And I attach the file "TheProof.csv" in the file drop panel
	And I press "Add more"
	And I attach the file "TheProof2.csv" in the file drop panel
	And I press "Upload 2 files"
	And I wait "1" seconds
	Then I should see a "Next (Metadata Form)" link to "/authorisedDataset/annotateFiles/id/000007"

@ok
Scenario: Next button to proceed to file metadata annotation form
	Given I sign in as the user "Artie" "Dodger"
	And The user "Artie" "Dodger" is registered as authorised user in the API
	And I am on "/user/view_profile#submitted"
	And the "Your Uploaded Datasets" tab is active
	And I press "Upload Files"
	And I attach the file "TheProof.csv" in the file drop panel
	And I press "Add more"
	And I attach the file "TheProof2.csv" in the file drop panel
	And I press "Upload 2 files"
	And I wait "30" seconds
	When I press "Next (Metadata Form)"
	Then I should be on "/authorisedDataset/annotateFiles/id/000007"


# Scenario: There's no button for uploading files if dataset doesn't have the right status
# 	Given I sign in as a user
# 	And I am on "/user/view_profile#submitted"
# 	And the uploaded dataset has status "UserStartedIncomplete"
# 	Then I should not see a "Upload Files for dataset 000005" button

# Scenario: Selecting files to upload using file dialog box
# 	Given I sign in as a user
# 	And I am on "/uploader/files"
# 	And  I click "Choose File" button
# 	When I select files in the file selection box
# 	Then I should see the selected files in the uploading queue


# Scenario: Selecting files to upload using drag and drop
# 	Given I sign in as a user
# 	And I am on "/uploader/files"
# 	When I drag and drop a set of files to the screen
# 	Then I should see that set of files in the uploading queue

# Scenario: Files in the uploading queue are uploading sequentially (first file)
# 	Given I sign in as a user
# 	And I am on "/uploader/files"
# 	When I add a set of files to the uploading queue
# 	Then the first file is uploading
# 	And the other files are shown as queued
# 	And I should see a "Next" inactive button

# Scenario: Files in the uploading queue are uploading sequentially (subsquent files)
# 	Given I sign in as a user
# 	And I am on "/uploader/files"
# 	When I add a set of files to the uploading queue
# 	And the first file has been uploaded
# 	Then the first file should be shown has complete
# 	And the second file should be uploading
# 	And the other files are shown as queued
# 	And I should see a "Next" inactive button

# Scenario: Files in the uploading queue are uploading sequentially (all files uploaded)
# 	Given I sign in as a user
# 	And I am on "/uploader/files"
# 	When I add a set of files to the uploading queue
# 	And all the files have been uploaded
# 	Then all the files should be shown as has complete
# 	And I should see a "Next" button

# Scenario: Files uploaded are in the appropriate drop box
# 	Given I sign in as a user
# 	And I am on "/uploader/files"
# 	When I add a set of files to the uploading queue
# 	And all the files have been uploaded
# 	Then all the files are transfered to the file drop box for dataset "000005"
