@attributes
Feature:
	As an Author
	I want to add attributes to a file I have uploaded
	So that I can describe it better for more precise querying

Background:
	Given there is a user "Artie" "Dodger"
	And a dataset with DOI "000007" owned by user "Artie" "Dodger" has status "UserUploadingData"
	And filedrop account for DOI "000007" does exist
@ok
Scenario: Can trigger a form from metadata form for adding new attribute
	Given I sign in as the user "Artie" "Dodger"
	And The user "Artie" "Dodger" is registered as authorised user in the API
	And I am on "/user/view_profile#submitted"
	And the "Your Uploaded Datasets" tab is active
	And I press "Upload Dataset Files"
	And I attach the file "TheProof.csv" in the file drop panel
	And I press "Add more"
	And I attach the file "CC0_pixel.jpg" in the file drop panel
	And I press "Upload 2 files"
	And I wait "60" seconds
	When I press "Next"
	And I wait "1" seconds
	And I press "Attributes"
	Then I should see a text input field "Name"
	Then I should see a text input field "Value"
	Then I should see a text input field "Unit"
	And I should see a "Add" button

@ok
Scenario: Can add new attribute to the attribute list
	Given I sign in as the user "Artie" "Dodger"
	And The user "Artie" "Dodger" is registered as authorised user in the API
	And I am on "/user/view_profile#submitted"
	And the "Your Uploaded Datasets" tab is active
	And I press "Upload Dataset Files"
	And I attach the file "TheProof.csv" in the file drop panel
	And I press "Add more"
	And I attach the file "CC0_pixel.jpg" in the file drop panel
	And I press "Upload 2 files"
	And I wait "60" seconds
	And I press "Next"
	And I wait "1" seconds
	When I press "Attributes"
	And I fill in "Name" with "Temperature"
	And I fill in "Value" with "33"
	And I fill in "Unit" with "Celsius"
	And I press "Add"
	Then I should see
	| Name | Value | Unit |
	| Temperature | 33 | Celsius |

# Scenario: there is a button to add attributes in the file metadata page when all mandatory fields are filled in
# 	Given I sign in as a user
# 	And I am on "/uploader/files"
# 	And I add a set of files to the uploading queue for dataset "100006"
# 	And all the files have been uploaded
# 	When I press "Next"
# 	And I fill in the "file-1-data-type" field with "Text"
# 	And I fill in the "file-1-description" field with "this is file description for file 1"
# 	And I fill in the "file-2-data-type" field with "Text"
# 	Then I should see a "Add attributes to file file1.txt" button
# 	And I should not see a "Add attributes to file file2.csv" button
# 	And I should not see a "Add attributes to file file3.jpg" button

# Scenario: adding an attribute to a file
# 	Given I sign in as a user
# 	And I have uploaded a set of files to the drop box for dataset "100006"
# 	And I am on "/uploader/meta"
# 	And I fill in the "file-2-data-type" field with "Text"
# 	And I fill in the "file-2-description" field with "this is file description for file 2"
# 	When I press "Add attributes to file file2.csv"
# 	Then A Dialog box "file-2-attributes" reads "Attributes"
# 	And I should see form elements:
# 	| Attribute ID | Value | Unit |
# 	| file-2-data-tag-attribute-id | file-2-data-tag-value | file-2-data-tag-unit |
# 	And I should see a "Add attribute" button

# Scenario: seeing added attributes
# 	Given I sign in as a user
# 	And I have uploaded a set of files to the drop box for dataset "100006"
# 	And I am on "/uploader/meta"
# 	And I have added an attribute to "file2.csv"
# 	When I press "Add attributes to file file2.csv"
# 	Then A Dialog box "file-2-attributes" reads "Attributes"
# 	And I should see table:
# 	| Attribute  Name| Value | Unit |
# 	| Geographic location (latitude and longitude) | 22.303997, 114.192517 | |
# 	And I should see form elements:
# 	| Attribute ID | Value | Unit |
# 	| file-2-data-tag-attribute-id | file-2-data-tag-value | file-2-data-tag-unit |
# 	And I should see a "Add attribute" button

# Scenario: form element to link with a sample
# 	Given I sign in as a user
# 	And I have uploaded a set of files to the drop box for dataset "100006"
# 	And I am on "/uploader/meta"
# 	When I press "Add attributes to file file2.csv"
# 	Then A Dialog box "file-2-attributes" reads "Attributes"
# 	And I should see a "Sample ID" text field
# 	And I should see a "Link sample" button

# Scenario: link a file with a sample
# 	Given I sign in as a user
# 	And I have uploaded a set of files to the drop box for dataset "100006"
# 	And I am on "/uploader/meta"
# 	And I have a sample with ID "100"
# 	And I press "Add attributes to file file2.csv"
# 	When I fill in the "Sample ID" field with "100"
# 	And I press "Link sample"
# 	Then I should see a "file2.csv linked to sample 100" flash message

