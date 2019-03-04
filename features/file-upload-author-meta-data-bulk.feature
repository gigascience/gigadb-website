Feature:
	As an Author,
	I want to make bulk assignment of metadata to the files I have uploaded
	So that the large number of files associated with my manuscript's dataset can be queried precisely


Scenario: bulk upload form for all uploaded files
	Given I am on the file upload page
	And I add a set of files to the uploading queue for dataset "100006"
	And all the files have been uploaded
	When I click the "Next" button
	Then I should see in the files metadata table the form elements:
	| File name | Data type field | Format | Size | Description field | actions button |
	| file1.txt | file-1-data-type | TEXT | 1Kib | file-1-description | file-1-delete |
	| file2.csv | file-2-data-type | TEXT | 1Kib | file-2-description | file-2-delete |
	| file3.jpg | file-3-data-type | JPEG | 3.4Mib | file-3-description | file-3-delete |
	And I should see a file upload button "Upload Files Metadata"
	And I should see a button "Save Files Metadata"
	And I should see a button "Previous"
	And I should not see the button "Complete and return to Your Author Dadasets page"

Scenario: Well-formated spreadsheet with metadata populated for some or all files with no prior metadata filled in
	Given I am on the file metadata page
	And I have uploaded a set of files to the drop box for dataset "100006"
	When I click the "Upload Files Metadata" button and select a spreadsheet with metadata for some or all files
	Then the files metadata table form elements should be populated with the spreadsheet's values for the matching files

Scenario: Well-formated spreadsheet with metadata populated for some files with pre-existing metadata
	Given I am on the file metadata page
	And I have uploaded a set of files to the drop box for dataset "100006"
	And I have filled metadata for all the files
	When I click the "Upload Files Metadata" button and select a spreadsheet with metadata for some files
	Then The files metadata table form elements should be replaced with the spreadsheet's values for the matching files
	And The files metadata table form elements should keep previous values for uploaded files not in the spreadsheet


Scenario: The file is not valid TSV or CSV
	Given I am on the file metadata page
	And I have uploaded a set of files to the drop box for dataset "100006"
	When I click the "Upload Files Metadata" button and select a malformed file
	Then I should see the message "Format not recognized, only upload valid TSV or CSV formatted spreadsheet"