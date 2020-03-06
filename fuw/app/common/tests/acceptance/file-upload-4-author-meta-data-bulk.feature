@bulk-metadata
Feature:
	As an Author
	I want to make bulk assignment of metadata to the files I have uploaded
	So that the large number of files associated with my manuscript's dataset can be queried precisely

Background:
	Given there is a user "Artie" "Dodger"
	And a dataset with DOI "000007" owned by user "Artie" "Dodger" has status "UserUploadingData"
	And filedrop account for DOI "000007" does exist

@ok
Scenario: bulk upload form for all uploaded files
	Given I sign in as the user "Artie" "Dodger"
	And The user "Artie" "Dodger" is registered as authorised user in the API
	And I am on "/user/view_profile#submitted"
	And the "Your Uploaded Datasets" tab is active
	And I press "Upload Dataset Files"
	And I attach the file "TheProof.csv" in the file drop panel
	And I press "Add more"
	And I attach the file "CC0_pixel.jpg" in the file drop panel
	And I press "Upload 2 files"
	And I wait "30" seconds
	When I press "Next"
	Then I should see form elements:
	| File name 	| Data type | Default 	| Description text | Tag action | Delete action|
	| TheProof.csv 	| form select | Text 	| form input 	| button | button |
	| CC0_pixel.jpg | form select | Image 	| form input 	| button | button |
	And I should see "Upload file metadata from spreadsheet:"
	And I should see a "Upload spreadsheet" button

@wip
Scenario: Uploading CSV spreadsheet to update upload metadata
	Given I sign in as the user "Artie" "Dodger"
	And The user "Artie" "Dodger" is registered as authorised user in the API
	And I am on "/user/view_profile#submitted"
	And the "Your Uploaded Datasets" tab is active
	And I press "Upload Dataset Files"
	And I attach the file "TheProof.csv" in the file drop panel
	And I press "Add more"
	And I attach the file "CC0_pixel.jpg" in the file drop panel
	And I press "Upload 2 files"
	And I wait "30" seconds
	And I press "Next"
	When I attach the file "sample1.csv"
	And I press "Upload spreadsheet"
	And I wait "3" seconds
	Then I should be on "/authorisedDataset/annotateFiles/id/000007"
	And I should see metadata
	| name         | description | datatype |
    | TheProof.csv | first row | Text |
    | CC0_pixel.jpg| second row | Text |
    And I should see "Metadata loaded"


# Scenario: Well-formated spreadsheet with metadata populated for some or all files with no prior metadata filled in
# 	Given I sign in as a user
# 	And I have uploaded a set of files to the drop box for dataset "100006"
# 	And I am on "/uploader/meta"
# 	When I press "Upload Files Metadata"
# 	And I select a spreadsheet with metadata for some or all files
# 	Then the files metadata table form elements should be populated with the spreadsheet's values for the matching files

# Scenario: Well-formated spreadsheet with metadata populated for some files with pre-existing metadata
# 	Given I sign in as a user
# 	And I have uploaded a set of files to the drop box for dataset "100006"
# 	And I am on "/uploader/meta"
# 	And I have filled in metadata for all the files
# 	When I press "Upload Files Metadata"
# 	And I select a spreadsheet with metadata for some files
# 	Then The files metadata table form elements should be replaced with the spreadsheet's values for the matching files
# 	And The files metadata table form elements should keep previous values for uploaded files not in the spreadsheet


# Scenario: The file is not valid TSV or CSV
# 	Given I sign in as a user
# 	And I have uploaded a set of files to the drop box for dataset "100006"
# 	And I am on "/uploader/meta"
# 	When I press "Upload Files Metadata"
# 	And I select a malformed file
# 	Then I should see a "Format not recognized, only upload valid TSV or CSV formatted spreadsheet" flash message

# Scenario: file 2 has following problems: missing data type, missing description
# 	Given I sign in as a user
# 	And I have uploaded a set of files to the drop box for dataset "100006"
# 	And I am on "/uploader/meta"
# 	When I press "Upload Files Metadata"
# 	And I select a spreadsheet that include one problematic file with missing metadata
# 	Then I should see a "Problem found when parsing file on line 2: missing data type" flash message

# Scenario: file 2 has following problems: file name doesn't match with any uploaded file
# 	Given I sign in as a user
# 	And I have uploaded a set of files to the drop box for dataset "100006"
# 	And I am on "/uploader/meta"
# 	When I press "Upload Files Metadata"
# 	And I select a spreadsheet that include one problematic file with filename not recognised
# 	Then I should see a "Problem found when parsing file on line 2: file name doesn't match any of the uploaded files" flash message

# Scenario: file 2 has following problems: Sample ID not recognized
# 	Given I sign in as a user
# 	And I have uploaded a set of files to the drop box for dataset "100006"
# 	And I am on "/uploader/meta"
# 	When I press "Upload Files Metadata"
# 	And I select a spreadsheet that include one problematic file with filename not recognised
# 	Then I should see a "Problem found when parsing file on line 2: Sample ID not found in the database" flash message

# Scenario: file 2 has following problems: tag1 is malformed, tag3 is malformed
# 	Given I sign in as a user
# 	And I have uploaded a set of files to the drop box for dataset "100006"
# 	And I am on "/uploader/meta"
# 	When I press "Upload Files Metadata"
# 	And I select a spreadsheet that include one problematic file with malformed tags
# 	Then I should see a "Problem found when parsing file on line 2: tag1 has wrong format (should be: name::value::unit)" flash message