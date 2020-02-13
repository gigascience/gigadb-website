@metadata
Feature:
	As an Author
	I want to add metadata to the files I have uploaded
	So that the files associated with my dataset can be queried precisely

Background:
	Given there is a user "Artie" "Dodger"
	And a dataset with DOI "000007" owned by user "Artie" "Dodger" has status "UserUploadingData"
	And filedrop account for DOI "000007" does exist

@ok
Scenario: Metadata form elements for all uploaded files
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
	Then I should see form elements:
	| File name 	| Data type | Default 	| Description text | Tag action | Delete action|
	| TheProof.csv 	| form select | Text 	| form input 	| button | a |
	| CC0_pixel.jpg | form select | Image 	| form input 	| button | a |

@ok
Scenario: Making changes to metadata
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
	And I fill in the form with
	| File name 	| Data type | Description text 	|
	| TheProof.csv 	| Rich Text | foo bar 			| 
	| CC0_pixel.jpg | Image 	| hello world 		|
	Then I should see a "Complete and return to Your Uploaded Datasets page" button

@ok
Scenario: Saving metadata
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
	And I fill in the form with
	| File name 	| Data type | Description text 	|
	| TheProof.csv 	| Rich Text | foo bar 			| 
	| CC0_pixel.jpg | Image 	| hello world 		|
	When I press "Complete and return to Your Uploaded Datasets page"
	Then I should be on "/user/view_profile#submitted"
	And I should see "File uploading complete"
	And I should see "DataAvailableForReview"

# Scenario: Saving all metadata for all files
# 	Given I sign in as a user
# 	And I have uploaded a set of files to the drop box for dataset "100006"
# 	And I am on "/uploader/meta"
# 	When I fill in the "file-1-data-type" field with "Text"
# 	And I fill in the "file-1-description" field with "this is file description for file 1"
# 	And I fill in the "file-2-data-type" field with "Text"
# 	And I fill in the "file-2-description" field with "this is file description for file 2"
# 	And I fill in the "file-3-data-type" field with "Text"
# 	And I fill in the "file-3-description" field with "this is file description for file 3"
# 	And I press "Save Files Metadata"
# 	Then I should see a "File Metadata Saved for 3 out of 3 files" flash message
# 	And I should see a "Complete and return to Your Uploaded Datasets page" button

# Scenario: Saving all mandatory metadata for some files
# 	Given I sign in as a user
# 	And I have uploaded a set of files to the drop box for dataset "100006"
# 	And I am on "/uploader/meta"
# 	When I fill in the "file-1-data-type" field with "Text"
# 	And I fill in the "file-1-description" field with "this is file description for file 1"
# 	And I fill in the "file-2-data-type" field with "Text"
# 	And I fill in the "file-2-description" field with "this is file description for file 2"
# 	And I press "Save Files Metadata"
# 	Then I should see a "File Metadata Saved for 2 out of 3 files" flash message
# 	And I should see a "Complete and return to Your Uploaded Datasets page" inactive button

# Scenario: Saving some mandatory metadata for some files is not allowed
# 	Given I sign in as a user
# 	And I have uploaded a set of files to the drop box for dataset "100006"
# 	And I am on "/uploader/meta"
# 	When I fill in the "file-1-data-type" field with "Text"
# 	And I fill in the "file-1-description" field with "this is file description for file 1"
# 	And I fill in the "file-2-data-type" field with "Text"
# 	Then I should see a "Save Files Metadata" inactive button
# 	And I should see a "Complete and return to Your Uploaded Datasets page" inactive button

# Scenario: Completion: status set to DataAvailableForReview, email sent to editors, author taken to Your Uploaded Dataset page
# 	Given I sign in as a user
# 	And I have uploaded a set of files to the drop box for dataset "100006"
# 	And I am on "/uploader/meta"
# 	When I saved all mandatory file metadata for all files
# 	And I press "Complete and return to Your Uploaded Datasets page"
# 	Then the response should contain "Your Uploaded Datasets"
# 	And the response sould contain "10.5524/100006"
# 	And the response sould contain "DataAvailableForReview"
# 	And an email notification is sent to "editorial@gigasciencejournal.com"
# 	And I should be on "/user/view_profile#submitted"

# Scenario: Return to previous page (file upload page)
# 	Given I sign in as a user
# 	And I have uploaded a set of files to the drop box for dataset "100006"
# 	And I am on "/uploader/meta"
# 	When I press "Previous"
# 	Then I should be on "/uploader/files"
# 	And I should see "File Uploader for dataset 100006"
# 	And all the files should be shown as complete
# 	And I should see a "Choose file" button
# 	And I should see a "Next" button
