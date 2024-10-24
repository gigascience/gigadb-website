@attributes
Feature:
	As an Author
	I want to add attributes to a file I have uploaded
	So that I can describe it better for more precise querying

Background:
	Given there is "user" "Artie" "Dodger"
	And a dataset with DOI "000007" owned by user "Artie" "Dodger" has status "UserUploadingData"
	And filedrop account for DOI "000007" does exist

@ok
Scenario: Can trigger a form from metadata form for adding new attribute
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
	And I press "Attributes"
	Then I should see a text input field "Name"
	And I should see a text input field "Value"
	And I should see a text input field "Unit"
	And I should see a "Add" button

@ok
Scenario: Can add new attribute to the attribute list
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
	And I press "Attributes"
	And I fill in "Name" with "Temperature"
	And I fill in "Value" with "33"
	And I fill in "Unit" with "Celsius"
	And I press "Add"
	Then I should see
	| Name | Value | Unit |
	| Temperature | 33 | Celsius |


@ok
Scenario: Can add new samples to a file upload
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
	And I press "Sample IDs"
	And I add new sample "Sequence 3"
	And I add new sample "Sequence 4"
	And I press "Save"
	And I wait "2" seconds
	And I press "Sample IDs"
	Then I should see
	| samples |
	| Sequence 3 |
	| Sequence 4 |

@ok
Scenario: Saving file metadata with attributes and samples
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
	And I fill in the form with
	| File name 	| Data type | Description text 	|
	| TheProof.csv 	| Script 	| foo bar 			| 
	| CC0_pixel.jpg | Annotation| hello world 		|
	And I press Samples button for "CC0_pixel.jpg"
	And I add new sample "Sequence 3"
	And I add new sample "Sequence 4"
	And I wait "1" seconds
	And I press "Save"
	And I wait "1" seconds
	And I press Attributes button for "TheProof.csv"
	And I fill in "Name" with "Temperature"
	And I fill in "Value" with "33"
	And I fill in "Unit" with "Celsius"
	And I press "Add"
	And I press the close button
	And I wait "1" seconds
	When I press "Complete and return to Your Uploaded Datasets page"
	Then I should be on "/user/view_profile#submitted"
	And I should see "File uploading complete"
	And I should see "1 attribute(s) added for upload TheProof.csv"
	And I should see "2 sample(s) added for upload CC0_pixel.jpg"
	And I should not see "1 sample(s) added for upload TheProof.csv"
	And I should see "DataAvailableForReview"

# Scenario: there is a button to add attributes in the file metadata page when all mandatory fields are filled in
# 	Given I sign in as a user
# 	And I am on "/uploader/files"
# 	And I add a set of files to the uploading queue for dataset "000005"
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
# 	And I have uploaded a set of files to the drop box for dataset "000005"
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
# 	And I have uploaded a set of files to the drop box for dataset "000005"
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
# 	And I have uploaded a set of files to the drop box for dataset "000005"
# 	And I am on "/uploader/meta"
# 	When I press "Add attributes to file file2.csv"
# 	Then A Dialog box "file-2-attributes" reads "Attributes"
# 	And I should see a "Sample ID" text field
# 	And I should see a "Link sample" button

# Scenario: link a file with a sample
# 	Given I sign in as a user
# 	And I have uploaded a set of files to the drop box for dataset "000005"
# 	And I am on "/uploader/meta"
# 	And I have a sample with ID "100"
# 	And I press "Add attributes to file file2.csv"
# 	When I fill in the "Sample ID" field with "100"
# 	And I press "Link sample"
# 	Then I should see a "file2.csv linked to sample 100" flash message

