Feature:
	As an Author
	I want to add metadata to the files I have uploaded
	So that the files associated with my dataset can be queried precisely

Background:
	Given Gigadb web site is loaded with "gigadb_testdata.pgdmp" data
	And user "joy_fox" is loaded # Author
	And a drop box has been created for the submitted dataset

Scenario: Metadata form elements for all uploaded files, but Save button inactive: none of the field for any file is filled in
	Given I sign in as a user
	And I am on "/uploader/files"
	And I add a set of files to the uploading queue for dataset "100006"
	And all the files have been uploaded
	When I press "Next"
	Then I should see form elements:
	| File name | Data type field | Format | Size | Description field | actions button |
	| file1.txt | file-1-data-type | TEXT | 1Kib | file-1-description | file-1-delete |
	| file2.csv | file-2-data-type | TEXT | 1Kib | file-2-description | file-2-delete |
	| file3.jpg | file-3-data-type | JPEG | 3.4Mib | file-3-description | file-3-delete |
	And I should see a "Save Files Metadata" button
	And I should see a "Save Files Metadata" inactive button
	And I should see a "Previous" button
	And I should see a "Complete and return to Your Uploaded Datasets page" inactive button


Scenario: Saving all metadata for all files
	Given I sign in as a user
	And I have uploaded a set of files to the drop box for dataset "100006"
	And I am on "/uploader/meta"
	When I fill in the "file-1-data-type" field with "Text"
	And I fill in the "file-1-description" field with "this is file description for file 1"
	And I fill in the "file-2-data-type" field with "Text"
	And I fill in the "file-2-description" field with "this is file description for file 2"
	And I fill in the "file-3-data-type" field with "Text"
	And I fill in the "file-3-description" field with "this is file description for file 3"
	And I press "Save Files Metadata"
	Then I should see a "File Metadata Saved for 3 out of 3 files" flash message
	And I should see a "Complete and return to Your Uploaded Datasets page" button

Scenario: Saving all mandatory metadata for some files
	Given I sign in as a user
	And I have uploaded a set of files to the drop box for dataset "100006"
	And I am on "/uploader/meta"
	When I fill in the "file-1-data-type" field with "Text"
	And I fill in the "file-1-description" field with "this is file description for file 1"
	And I fill in the "file-2-data-type" field with "Text"
	And I fill in the "file-2-description" field with "this is file description for file 2"
	And I press "Save Files Metadata"
	Then I should see a "File Metadata Saved for 2 out of 3 files" flash message
	And I should see a "Complete and return to Your Uploaded Datasets page" inactive button

Scenario: Saving some mandatory metadata for some files is not allowed
	Given I sign in as a user
	And I have uploaded a set of files to the drop box for dataset "100006"
	And I am on "/uploader/meta"
	When I fill in the "file-1-data-type" field with "Text"
	And I fill in the "file-1-description" field with "this is file description for file 1"
	And I fill in the "file-2-data-type" field with "Text"
	Then I should see a "Save Files Metadata" inactive button
	And I should see a "Complete and return to Your Uploaded Datasets page" inactive button

Scenario: Completion: status set to DataAvailableForReview, email sent to editors, author taken to Your Uploaded Dataset page
	Given I sign in as a user
	And I have uploaded a set of files to the drop box for dataset "100006"
	And I am on "/uploader/meta"
	When I saved all mandatory file metadata for all files
	And I press "Complete and return to Your Uploaded Datasets page"
	Then the response should contain "Your Uploaded Datasets"
	And the response sould contain "10.5524/100006"
	And the response sould contain "DataAvailableForReview"
	And an email notification is sent to "editorial@gigasciencejournal.com"
	And I should be on "/user/view_profile#submitted"

Scenario: Return to previous page (file upload page)
	Given I sign in as a user
	And I have uploaded a set of files to the drop box for dataset "100006"
	And I am on "/uploader/meta"
	When I press "Previous"
	Then I should be on "/uploader/files"
	And I should see "File Uploader for dataset 100006"
	And all the files should be shown as complete
	And I should see a "Choose file" button
	And I should see a "Next" button
