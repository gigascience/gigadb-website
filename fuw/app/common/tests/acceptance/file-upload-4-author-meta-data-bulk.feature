Feature:
	As an Author,
	I want to make bulk assignment of metadata to the files I have uploaded
	So that the large number of files associated with my manuscript's dataset can be queried precisely

Background:
	Given Gigadb web site is loaded with "gigadb_testdata.pgdmp" data
	And user "joy_fox" is loaded # Author
	And a drop box has been created for the submitted dataset

Scenario: bulk upload form for all uploaded files
	Given I sign in as a user
	And I am on "/uploader/files"
	And I add a set of files to the uploading queue for dataset "100006"
	And all the files have been uploaded
	When I press "Next"
	Then I should see in the files metadata table the form elements:
	| File name | Data type field | Format | Size | Description field | actions button |
	| file1.txt | file-1-data-type | TEXT | 1Kib | file-1-description | file-1-delete |
	| file2.csv | file-2-data-type | TEXT | 1Kib | file-2-description | file-2-delete |
	| file3.jpg | file-3-data-type | JPEG | 3.4Mib | file-3-description | file-3-delete |
	And I should see a "Upload Files Metadata" button
	And I should see a "Save Files Metadata" button
	And I should see a "Previous" button
	And I should not see a "Complete and return to Your Uploaded Datasets page" button

Scenario: Well-formated spreadsheet with metadata populated for some or all files with no prior metadata filled in
	Given I sign in as a user
	And I have uploaded a set of files to the drop box for dataset "100006"
	And I am on "/uploader/meta"
	When I press "Upload Files Metadata"
	And I select a spreadsheet with metadata for some or all files
	Then the files metadata table form elements should be populated with the spreadsheet's values for the matching files

Scenario: Well-formated spreadsheet with metadata populated for some files with pre-existing metadata
	Given I sign in as a user
	And I have uploaded a set of files to the drop box for dataset "100006"
	And I am on "/uploader/meta"
	And I have filled in metadata for all the files
	When I press "Upload Files Metadata"
	And I select a spreadsheet with metadata for some files
	Then The files metadata table form elements should be replaced with the spreadsheet's values for the matching files
	And The files metadata table form elements should keep previous values for uploaded files not in the spreadsheet


Scenario: The file is not valid TSV or CSV
	Given I sign in as a user
	And I have uploaded a set of files to the drop box for dataset "100006"
	And I am on "/uploader/meta"
	When I press "Upload Files Metadata"
	And I select a malformed file
	Then I should see a "Format not recognized, only upload valid TSV or CSV formatted spreadsheet" flash message

Scenario: file 2 has following problems: missing data type, missing description
	Given I sign in as a user
	And I have uploaded a set of files to the drop box for dataset "100006"
	And I am on "/uploader/meta"
	When I press "Upload Files Metadata"
	And I select a spreadsheet that include one problematic file with missing metadata
	Then I should see a "Problem found when parsing file on line 2: missing data type" flash message

Scenario: file 2 has following problems: file name doesn't match with any uploaded file
	Given I sign in as a user
	And I have uploaded a set of files to the drop box for dataset "100006"
	And I am on "/uploader/meta"
	When I press "Upload Files Metadata"
	And I select a spreadsheet that include one problematic file with filename not recognised
	Then I should see a "Problem found when parsing file on line 2: file name doesn't match any of the uploaded files" flash message

Scenario: file 2 has following problems: Sample ID not recognized
	Given I sign in as a user
	And I have uploaded a set of files to the drop box for dataset "100006"
	And I am on "/uploader/meta"
	When I press "Upload Files Metadata"
	And I select a spreadsheet that include one problematic file with filename not recognised
	Then I should see a "Problem found when parsing file on line 2: Sample ID not found in the database" flash message

Scenario: file 2 has following problems: tag1 is malformed, tag3 is malformed
	Given I sign in as a user
	And I have uploaded a set of files to the drop box for dataset "100006"
	And I am on "/uploader/meta"
	When I press "Upload Files Metadata"
	And I select a spreadsheet that include one problematic file with malformed tags
	Then I should see a "Problem found when parsing file on line 2: tag1 has wrong format (should be: name::value::unit)" flash message