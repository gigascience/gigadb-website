Feature:
 	As an Author
 	I want to upload from my GigaDB profile page the files for my manuscript's dataset
 	So that the dataset can be reviewed and made available online

Scenario: Click upload files button when dataset has appropriate status
	Given I am on the "Your Authored Datasets" tab of current user's profile
	And user has a dataset "100006" with status "UserUploadingData"
	When I click the button "Upload Files" for dataset "100006"
	Then the response should contain "File Uploader for dataset 100006"

Scenario: There's no button for uploading files if dataset doesn't have the right status
	Given I have a dataset "100006" with status "UserStartedIncomplete"
	When I go to the "Your dataset page" of current user's profile
	Then I should not see a "Upload Files" button for dataset "100006"

Scenario: Selecting files to upload using file dialog box
	Given I am on the file upload page
	And  I click "Choose File" button
	When I select files in the file dialog box
	Then I should see the selected files in the uploading queue


Scenario: Selecting files to upload using drag and drop
	Given I am on the file upload page
	When I drag and drop a set of files to the screen
	Then I should see that set of files in the uploading queue

Scenario: Files in the uploading queue are uploading sequentially (first file)
	Given I am on the file upload page
	When I add a set of files to the uploading queue
	Then the first file is uploading
	And the other files are shown as queued
	And the "Next" button should be inactive

Scenario: Files in the uploading queue are uploading sequentially (subsquent files)
	Given I am on the file upload page
	When I add a set of files to the uploading queue
	And the first file has been uploaded
	Then the first file should be shown has complete
	And the second file should be uploading
	And the other files are shown as queued
	And the "Next" button should be inactive

Scenario: Files in the uploading queue are uploading sequentially (all files uploaded)
	Given I am on the file upload wizard page
	When I add a set of files to the uploading queue
	And all the files have been uploaded
	Then all the files should be shown as has complete
	And the "Next" button should be active

Scenario: Files uploaded are in the appropriate drop box
	Given I am on the file upload page
	When I add a set of files to the uploading queue
	And all the files have been uploaded
	Then all the files are transfered to the file drop box for dataset "100006"