@bulk-metadata
Feature:
	As an Author
	I want to make bulk assignment of metadata to the files I have uploaded
	So that the large number of files associated with my manuscript's dataset can be queried precisely

Background:
	Given there is "user" "Artie" "Dodger"
	And a dataset with DOI "000007" owned by user "Artie" "Dodger" has status "UserUploadingData"
	And filedrop account for DOI "000007" does exist

@ok
Scenario: bulk upload form for all uploaded files
	Given I sign in as the user "Artie" "Dodger"
	And The user "Artie" "Dodger" is registered as authorised user in the API
	And I am on "/user/view_profile#submitted"
	And the "Your Uploaded Datasets" tab is active
	And I press "Upload Files"
	And I attach the file "TheProof.csv" in the file drop panel
	And I press "Add more"
	And I attach the file "CC0_pixel.jpg" in the file drop panel
	And I press "Upload 2 files"
	And I wait "5" seconds
	When I press "Next"
	Then I should see form elements:
	| File name 	| Data type | Default 		| Description text | Tag action | Delete action|
	| TheProof.csv 	| form select | Script 		| form input 	| button | button |
	| CC0_pixel.jpg | form select | Annotation 	| form input 	| button | button |
	And I should see "Select a spreadsheet:"
	And I should see a "Upload metadata from spreadsheet" button

@ok
Scenario: Uploading CSV spreadsheet to update upload metadata
	Given I sign in as the user "Artie" "Dodger"
	And The user "Artie" "Dodger" is registered as authorised user in the API
	And I am on "/user/view_profile#submitted"
	And the "Your Uploaded Datasets" tab is active
	And I press "Upload Files"
	And I attach the file "TheProof.csv" in the file drop panel
	And I press "Add more"
	And I attach the file "CC0_pixel.jpg" in the file drop panel
	And I press "Upload 2 files"
	And I wait "5" seconds
	And I press "Next"
	When I attach the file "sample1.csv"
	And I press "Upload metadata from spreadsheet"
	And I wait "3" seconds
	Then I should be on "/authorisedDataset/annotateFiles/id/000007"
	And I should see metadata
	| name         | description | datatype |
    | TheProof.csv | first row | Script |
    | CC0_pixel.jpg| second row | Annotation |
    And I should see "Metadata loaded"


@ok
Scenario: Uploading CSV spreadsheet to update upload metadata and attributes
	Given I sign in as the user "Artie" "Dodger"
	And The user "Artie" "Dodger" is registered as authorised user in the API
	And I am on "/user/view_profile#submitted"
	And the "Your Uploaded Datasets" tab is active
	And I press "Upload Files"
	And I attach the file "TheProof.csv" in the file drop panel
	And I press "Add more"
	And I attach the file "CC0_pixel.jpg" in the file drop panel
	And I press "Upload 2 files"
	And I wait "5" seconds
	And I press "Next"
	And I attach the file "sample2_attr.csv"
	And I press "Upload metadata from spreadsheet"
	And I wait "3" seconds
 	When I press Attributes button for "TheProof.csv"
	Then I should see
	| Name | Value | Unit |
	| Max Temp. | 210 | Fahrenheit |

@ok
Scenario: Uploading CSV spreadsheet to update upload metadata, attributes and samples
	Given I sign in as the user "Artie" "Dodger"
	And The user "Artie" "Dodger" is registered as authorised user in the API
	And I am on "/user/view_profile#submitted"
	And the "Your Uploaded Datasets" tab is active
	And I press "Upload Files"
	And I attach the file "TheProof.csv" in the file drop panel
	And I press "Add more"
	And I attach the file "CC0_pixel.jpg" in the file drop panel
	And I press "Upload 2 files"
	And I wait "5" seconds
	And I press "Next"
	And I attach the file "sample2_attr_sample.csv"
	And I press "Upload metadata from spreadsheet"
	And I wait "3" seconds
	When I press Samples button for "CC0_pixel.jpg"
	Then I should see
	| samples |
	| Sample 5 |
	| Sample 6 |

@ok
Scenario: Spreadsheet with malformed attributes
	Given I sign in as the user "Artie" "Dodger"
	And The user "Artie" "Dodger" is registered as authorised user in the API
	And I am on "/user/view_profile#submitted"
	And the "Your Uploaded Datasets" tab is active
	And I press "Upload Files"
	And I attach the file "TheProof.csv" in the file drop panel
	And I press "Add more"
	And I attach the file "CC0_pixel.jpg" in the file drop panel
	And I press "Upload 2 files"
	And I wait "5" seconds
	And I press "Next"
	When I attach the file "sample3_malformed_attr.csv"
	And I press "Upload metadata from spreadsheet"
	And I wait "3" seconds
	Then I should be on "/authorisedDataset/annotateFiles/id/000007"
	And I should see metadata
	| name         | description | datatype |
    | TheProof.csv | first row | Script |
    And I should see "Metadata loaded"
    And I should see "(CC0_pixel.jpg) Malformed attribute: Rating:9::Some guys's scale"

 @ok
 Scenario: Spreadsheet with mispelled column header
 	Given I sign in as the user "Artie" "Dodger"
	And The user "Artie" "Dodger" is registered as authorised user in the API
	And I am on "/user/view_profile#submitted"
	And the "Your Uploaded Datasets" tab is active
	And I press "Upload Files"
	And I attach the file "TheProof.csv" in the file drop panel
	And I press "Add more"
	And I attach the file "CC0_pixel.jpg" in the file drop panel
	And I press "Upload 2 files"
	And I wait "5" seconds
	And I press "Next"
	When I attach the file "sample6_unknown_column.csv"
	And I press "Upload metadata from spreadsheet"
	And I wait "3" seconds
	Then I should be on "/authorisedDataset/annotateFiles/id/000007"
    And I should see "Could not load spreadsheet, missing column(s): Description"

@ok
Scenario: Unknown Data Type (all spreadsheet entries have error)
	Given I sign in as the user "Artie" "Dodger"
	And The user "Artie" "Dodger" is registered as authorised user in the API
	And I am on "/user/view_profile#submitted"
	And the "Your Uploaded Datasets" tab is active
	And I press "Upload Files"
	And I attach the file "TheProof.csv" in the file drop panel
	And I press "Add more"
	And I attach the file "CC0_pixel.jpg" in the file drop panel
	And I press "Upload 2 files"
	And I wait "5" seconds
	And I press "Next"
	When I attach the file "sample4_unknown_datatype.csv"
	And I press "Upload metadata from spreadsheet"
	And I wait "3" seconds
	Then I should be on "/authorisedDataset/annotateFiles/id/000007"
	And I should not see "Metadata loaded"
    And I should see "(TheProof.csv) Cannot load file, incorrect Data type: Rich Text"
    And I should see "(CC0_pixel.jpg) Cannot load file, incorrect Data type: Photo"

@ok
Scenario: Unknown Data Type (one spreadsheet entry in error)
	Given I sign in as the user "Artie" "Dodger"
	And The user "Artie" "Dodger" is registered as authorised user in the API
	And I am on "/user/view_profile#submitted"
	And the "Your Uploaded Datasets" tab is active
	And I press "Upload Files"
	And I attach the file "TheProof.csv" in the file drop panel
	And I press "Add more"
	And I attach the file "CC0_pixel.jpg" in the file drop panel
	And I wait "1" seconds
	And I press "Add more"
	And I attach the file "lorem.txt" in the file drop panel	
	And I press "Upload 3 files"
	And I wait "5" seconds
	And I press "Next"
	When I attach the file "sample4_unknown_datatype2.csv"
	And I press "Upload metadata from spreadsheet"
	And I wait "3" seconds
	Then I should be on "/authorisedDataset/annotateFiles/id/000007"
	And I should see metadata
	| name         | description | datatype |
    | TheProof.csv | first row | Repeat sequence |
    | CC0_pixel.jpg | last row | Annotation |
    And I should see "Metadata loaded"
    And I should see "(lorem.txt) Cannot load file, incorrect Data type: Reafme"

@ok
Scenario: Unknown file format (one spreadsheet entry in error)
	Given I sign in as the user "Artie" "Dodger"
	And The user "Artie" "Dodger" is registered as authorised user in the API
	And I am on "/user/view_profile#submitted"
	And the "Your Uploaded Datasets" tab is active
	And I press "Upload Files"
	And I attach the file "TheProof.csv" in the file drop panel
	And I press "Add more"
	And I attach the file "CC0_pixel.jpg" in the file drop panel
	And I wait "2" seconds
	And I press "Add more"
	And I attach the file "lorem.txt" in the file drop panel	
	And I press "Upload 3 files"
	And I wait "5" seconds
	And I press "Next"
	When I attach the file "sample5_unknown_format.csv"
	And I press "Upload metadata from spreadsheet"
	And I wait "3" seconds
	Then I should be on "/authorisedDataset/annotateFiles/id/000007"
	And I should see metadata
	| name         | description | datatype |
    | TheProof.csv | first row | Script |
    And I should see "Metadata loaded"
    And I should see "(CC0_pixel.jpg) Cannot load file, incorrect File format: ZZZ"

@ok
Scenario: Uploading TSV spreadsheet to update upload metadata and attributes
	Given I sign in as the user "Artie" "Dodger"
	And The user "Artie" "Dodger" is registered as authorised user in the API
	And I am on "/user/view_profile#submitted"
	And the "Your Uploaded Datasets" tab is active
	And I press "Upload Files"
	And I attach the file "TheProof.csv" in the file drop panel
	And I press "Add more"
	And I attach the file "CC0_pixel.jpg" in the file drop panel
	And I press "Upload 2 files"
	And I wait "5" seconds
	And I press "Next"
	And I attach the file "sample1.tsv"
	And I press "Upload metadata from spreadsheet"
	And I wait "3" seconds
 	When I press Attributes button for "CC0_pixel.jpg"
	Then I should see
	| Name | Value | Unit |
	| Max Temp. | 210 | Fahrenheit |

# Scenario: Well-formated spreadsheet with metadata populated for some or all files with no prior metadata filled in
# 	Given I sign in as a user
# 	And I have uploaded a set of files to the drop box for dataset "000005"
# 	And I am on "/uploader/meta"
# 	When I press "Upload Files Metadata"
# 	And I select a spreadsheet with metadata for some or all files
# 	Then the files metadata table form elements should be populated with the spreadsheet's values for the matching files

# Scenario: Well-formated spreadsheet with metadata populated for some files with pre-existing metadata
# 	Given I sign in as a user
# 	And I have uploaded a set of files to the drop box for dataset "000005"
# 	And I am on "/uploader/meta"
# 	And I have filled in metadata for all the files
# 	When I press "Upload Files Metadata"
# 	And I select a spreadsheet with metadata for some files
# 	Then The files metadata table form elements should be replaced with the spreadsheet's values for the matching files
# 	And The files metadata table form elements should keep previous values for uploaded files not in the spreadsheet


# Scenario: The file is not valid TSV or CSV
# 	Given I sign in as a user
# 	And I have uploaded a set of files to the drop box for dataset "000005"
# 	And I am on "/uploader/meta"
# 	When I press "Upload Files Metadata"
# 	And I select a malformed file
# 	Then I should see a "Format not recognized, only upload valid TSV or CSV formatted spreadsheet" flash message

# Scenario: file 2 has following problems: missing data type, missing description
# 	Given I sign in as a user
# 	And I have uploaded a set of files to the drop box for dataset "000005"
# 	And I am on "/uploader/meta"
# 	When I press "Upload Files Metadata"
# 	And I select a spreadsheet that include one problematic file with missing metadata
# 	Then I should see a "Problem found when parsing file on line 2: missing data type" flash message

# Scenario: file 2 has following problems: file name doesn't match with any uploaded file
# 	Given I sign in as a user
# 	And I have uploaded a set of files to the drop box for dataset "000005"
# 	And I am on "/uploader/meta"
# 	When I press "Upload Files Metadata"
# 	And I select a spreadsheet that include one problematic file with filename not recognised
# 	Then I should see a "Problem found when parsing file on line 2: file name doesn't match any of the uploaded files" flash message

# Scenario: file 2 has following problems: Sample ID not recognized
# 	Given I sign in as a user
# 	And I have uploaded a set of files to the drop box for dataset "000005"
# 	And I am on "/uploader/meta"
# 	When I press "Upload Files Metadata"
# 	And I select a spreadsheet that include one problematic file with filename not recognised
# 	Then I should see a "Problem found when parsing file on line 2: Sample ID not found in the database" flash message

# Scenario: file 2 has following problems: tag1 is malformed, tag3 is malformed
# 	Given I sign in as a user
# 	And I have uploaded a set of files to the drop box for dataset "000005"
# 	And I am on "/uploader/meta"
# 	When I press "Upload Files Metadata"
# 	And I select a spreadsheet that include one problematic file with malformed tags
# 	Then I should see a "Problem found when parsing file on line 2: tag1 has wrong format (should be: name::value::unit)" flash message