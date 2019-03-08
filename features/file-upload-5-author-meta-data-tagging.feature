
Feature:
	As an Author
	I want to add attributes to a file I have uploaded
	So that I can describe it better for more precise querying

Background:
	Given Gigadb web site is loaded with "gigadb_testdata.pgdmp" data
	And user "joy_fox" is loaded # Author
	And a drop box has been created for the submitted dataset

Scenario: there is a button to add attributes in the file metadata page when all mandatory fields are filled in
	Given I sign in as a user
	And I am on "/uploader/files"
	And I add a set of files to the uploading queue for dataset "100006"
	And all the files have been uploaded
	When I press "Next"
	And I fill in the "file-1-data-type" field with "Text"
	And I fill in the "file-1-description" field with "this is file description for file 1"
	And I fill in the "file-2-data-type" field with "Text"
	Then I should see a "Add attributes to file file1.txt" button
	And I should not see a "Add attributes to file file2.csv" button
	And I should not see a "Add attributes to file file3.jpg" button

Scenario: adding an attribute to a file
	Given I sign in as a user
	And I have uploaded a set of files to the drop box for dataset "100006"
	And I am on "/uploader/meta"
	And I fill in the "file-2-data-type" field with "Text"
	And I fill in the "file-2-description" field with "this is file description for file 2"
	When I press "Add attributes to file file2.csv"
	Then A Dialog box "file-2-attributes" reads "Attributes"
	And I should see form elements:
	| Attribute ID | Value | Unit |
	| file-2-data-tag-attribute-id | file-2-data-tag-value | file-2-data-tag-unit |
	And I should see a "Add attribute" button

Scenario: seeing added attributes
	Given I sign in as a user
	And I have uploaded a set of files to the drop box for dataset "100006"
	And I am on "/uploader/meta"
	And I have added an attribute to "file2.csv"
	When I press "Add attributes to file file2.csv"
	Then A Dialog box "file-2-attributes" reads "Attributes"
	And I should see table:
	| Attribute  Name| Value | Unit |
	| Geographic location (latitude and longitude) | 22.303997, 114.192517 | |
	And I should see form elements:
	| Attribute ID | Value | Unit |
	| file-2-data-tag-attribute-id | file-2-data-tag-value | file-2-data-tag-unit |
	And I should see a "Add attribute" button

Scenario: form element to link with a sample
	Given I sign in as a user
	And I have uploaded a set of files to the drop box for dataset "100006"
	And I am on "/uploader/meta"
	When I press "Add attributes to file file2.csv"
	Then A Dialog box "file-2-attributes" reads "Attributes"
	And I should see a "Sample ID" text field
	And I should see a "Link sample" button

Scenario: link a file with a sample
	Given I sign in as a user
	And I have uploaded a set of files to the drop box for dataset "100006"
	And I am on "/uploader/meta"
	And I have a sample with ID "100"
	And I press "Add attributes to file file2.csv"
	When I fill in the "Sample ID" field with "100"
	And I press "Link sample"
	Then I should see a "file2.csv linked to sample 100" flash message

Scenario: Well-formated spreadsheet with metadata and attributes populated for some or all files with no prior metadata
	Given I sign in as a user
	And I have uploaded a set of files to the drop box for dataset "100006"
	And I am on "/uploader/meta"
	When I press "Upload Files Metadata"
	And I select a spreadsheet with metadata and attributes for some or all files
	Then the files metadata table form elements should be populated with the spreadsheet's values for the matching files

Scenario: Well-formated spreadsheet with metadata and attributes populated for some files with pre-existing metadata
	Given I sign in as a user
	And I have uploaded a set of files to the drop box for dataset "100006"
	And I am on "/uploader/meta"
	And I have filled in metadata for all the files
	When I press "Upload Files Metadata"
	And I select a spreadsheet with metadata and attributes for some files
	Then The files metadata table form elements should be replaced with the spreadsheet's values for the matching files
	And The files metadata table form elements should keep previous values for uploaded files not in the spreadsheet

Scenario: The file is not valid TSV or CSV (attributes columns are malformed)
	Given I sign in as a user
	And I have uploaded a set of files to the drop box for dataset "100006"
	And I am on "/uploader/meta"
	When I press "Upload Files Metadata"
	And I select a file with malformed attributes
	Then I should see a "Format not recognized, only upload valid TSV or CSV formatted spreadsheet" flash message
