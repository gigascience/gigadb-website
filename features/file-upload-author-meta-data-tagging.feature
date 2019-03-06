
Feature:
	As an Author
	I want to add attributes to a file I have uploaded
	So that I can describe it better for more precise querying

Scenario: there is a button to add attributes in the file metadata page when all mandatory fields are filled in
	Given I am on the file upload page
	And I add a set of files to the uploading queue for dataset "100006"
	And all the files have been uploaded
	When I click the "Next" button
	And I fill in "file-1-data-type" with "Text"
	And I fill in "file-1-description" with "this is file description for file 1"
	And I fill in "file-2-data-type" with "Text"
	Then I should see the button "Add attributes to file file1.txt"
	And I should not see the button "Add attributes to file file2.csv"
	And I should not see the button "Add attributes to file file3.jpg"

Scenario: adding an attribute to a file
	Given I have uploaded a set of files to the drop box for dataset "100006"
	And I am on the file metadata page
	And I fill in "file-2-data-type" with "Text"
	And I fill in "file-2-description" with "this is file description for file 2"
	When I click on the button "Add attributes to file file2.csv"
	Then a popup window appears
	And I should see the button "New attribute"
	And I should see form elements:
	| Attribute ID | Value | Unit |
	| file-2-data-tag-attribute-id | file-2-data-tag-value | file-2-data-tag-unit |
	And I should sees the button "Add"

Scenario: seeing added attributes
	Given I have uploaded a set of files to the drop box for dataset "100006"
	And I am on the file metadata page
	And I have added an attribute to "file2.csv"
	When I click on the button "Add attributes to file file2.csv"
	Then a popup window appears
	And I should see:
	| Attribute  Name| Value | Unit |
	| Geographic location (latitude and longitude) | 22.303997, 114.192517 | |
	And I should see the button "New attribute"

Scenario: form element to link with a sample
	Given I have uploaded a set of files to the drop box for dataset "100006"
	And I am on the file metadata page
	When I click on the button "Add attributes to file file2.csv"
	Then a popup window appears
	And I should see a field "Sample ID"
	And I should see the button "Link sample"

Scenario: link a file with a sample
	Given I have uploaded a set of files to the drop box for dataset "100006"
	And I am on the file metadata page
	And I have a sample with ID "100"
	And I click on the button "Add attributes to file file2.csv"
	When I fill in the field "Sample ID" with "100"
	And I click on the button "Link sample"
	Then I should see a message "file2.csv linked to sample 100"

Scenario: Well-formated spreadsheet with metadata and attributes populated for some or all files with no prior metadata
	Given I have uploaded a set of files to the drop box for dataset "100006"
	And I am on the file metadata page
	When I click the "Upload Files Metadata" button and select a spreadsheet with metadata and attributes for some or all files
	Then the files metadata table form elements should be populated with the spreadsheet's values for the matching files

Scenario: Well-formated spreadsheet with metadata and attributes populated for some files with pre-existing metadata
	Given I have uploaded a set of files to the drop box for dataset "100006"
	And I am on the file metadata page
	And I have filled in metadata for all the files
	When I click the "Upload Files Metadata" button and select a spreadsheet with metadata and attributes for some files
	Then The files metadata table form elements should be replaced with the spreadsheet's values for the matching files
	And The files metadata table form elements should keep previous values for uploaded files not in the spreadsheet

Scenario: The file is not valid TSV or CSV (attributes columns are malformed)
	Given I have uploaded a set of files to the drop box for dataset "100006"
	And I am on the file metadata page
	When I click the "Upload Files Metadata" button and select a file with malformed attributes
	Then I should see the message "Format not recognized, only upload valid TSV or CSV formatted spreadsheet"
