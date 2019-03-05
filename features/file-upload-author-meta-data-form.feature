Feature:
	As an Author
	I want to add metadata to the files I have uploaded
	So that the files associated with my manuscript's dataset can be queried precisely

Scenario: Metadata form elements for all uploaded files
	Given I am on the file upload page
	And I add a set of files to the uploading queue for dataset "100006"
	And all the files have been uploaded
	When I click the "Next" button
	Then I should see form elements:
	| File name | Data type field | Format | Size | Description field | actions button |
	| file1.txt | file-1-data-type | TEXT | 1Kib | file-1-description | file-1-delete |
	| file2.csv | file-2-data-type | TEXT | 1Kib | file-2-description | file-2-delete |
	| file3.jpg | file-3-data-type | JPEG | 3.4Mib | file-3-description | file-3-delete |
	And I should see a button "Save Files Metadata"
	And I should see a button "Previous"
	And I should not see the button "Complete and return to Your Uploaded Datasets page"


Scenario: Saving all metadata for all files
	Given I am on the file metadata page
	And I have uploaded a set of files to the drop box for dataset "100006"
	When I fill in "file-1-data-type" with "Text"
	And I fill in "file-1-description" with "this is file description for file 1"
	And I fill in "file-2-data-type" with "Text"
	And I fill in "file-2-description" with "this is file description for file 2"
	And I fill in "file-3-data-type" with "Text"
	And I fill in "file-3-description" with "this is file description for file 3"
	And I click the "Save Files Metadata" button
	Then the response should contains "All File Metadata Saved"
	And I should be on the metadata form page
	And I should see the button "Complete and return to Your Uploaded Datasets page"

Scenario: Saving all metadata for some files
	Given I am on the file metadata page
	And I have uploaded a set of files to the drop box for dataset "100006"
	When I fill in "file-1-data-type" with "Text"
	And I fill in "file-1-description" with "this is file description for file 1"
	And I fill in "file-2-data-type" with "Text"
	And I fill in "file-2-description" with "this is file description for file 2"
	Then the response should contains "File Metadata Saved for 2 out of 3 files"
	And I should not see the button "Complete and return to Your Uploaded Datasets page"

Scenario: Saving some metadata for some files is not allowed
	Given I am on the file metadata page
	And I have uploaded a set of files to the drop box for dataset "100006"
	When I fill in "file-1-data-type" with "Text"
	And I fill in "file-1-description" with "this is file description for file 1"
	And I fill in "file-2-data-type" with "Text"
	And I click the "Save Files metadata" button
	Then the response should contains "Mandatory fields must be filled in"
	And I should be on the metadata form page
	And I should not see the button "Complete and return to Your Uploaded Datasets page"

Scenario: Finishing the process: status changed to DataAvailableForReview and email sent to editors
	Given I am on the file metadata page
	And I have uploaded a set of files to the drop box for dataset "100006"
	When I save file metadata for all files
	And I click the "Complete and return to Your Uploaded Datasets page"
	Then the response should contain "Your Uploaded Datasets"
	And the response sould contain "10.5524/100006"
	And the response sould contain "DataAvailableForReview"
	And an email notification is sent to "editorial@gigasciencejournal.com"

Scenario: Return to previous page (file upload page)
	Given I am on the file metadata page
	And I have uploaded a set of files to the drop box for dataset "100006"
	When I click the "Previous" button
	Then the response should contain "File Uploader for dataset 100006"
	And all the files should be shown as has complete
	And I should see the button "Choose file"
	And the "Next" button should be active
