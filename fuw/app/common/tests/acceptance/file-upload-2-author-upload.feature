Feature:
 	As an author
 	I want to upload from my GigaDB profile page the files for my manuscript's dataset
 	So that the dataset can be reviewed and made available online

Background:
	Given there is a user "Artie" "Dodger"
	And a dataset with DOI "100007" owned by user "Artie" "Dodger" has status "UserUploadingData"
	And filedrop account for DOI "100007" does exist

@wip
Scenario: Click upload files button when dataset has appropriate status
	Given I sign in as the user "Artie" "Dodger"
	And I go to "/user/view_profile#submitted"
	When I press "Upload Files for dataset 100007"
	Then I should see "File Uploader for dataset 100007"
	And I am on "/uploader/files"

# Scenario: There's no button for uploading files if dataset doesn't have the right status
# 	Given I sign in as a user
# 	And I am on "/user/view_profile#submitted"
# 	And the uploaded dataset has status "UserStartedIncomplete"
# 	Then I should not see a "Upload Files for dataset 100006" button

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
# 	Then all the files are transfered to the file drop box for dataset "100006"
