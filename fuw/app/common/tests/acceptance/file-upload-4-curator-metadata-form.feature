Feature:
	As a Curator
	I want the retrieve the context of a dataset status change to "DataAvailableForReview"
	So that I can audit or troubleshoot the dataset files and metadata upload process

Background:
	Given there is a user "Artie" "Dodger"
	And a dataset with DOI "000007" owned by user "Artie" "Dodger" has status "UserUploadingData"
	And filedrop account for DOI "000007" does exist

@ok
Scenario: after status is changed to DataAvailableForReview, add entry in curation log
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
	And I fill in the form with
	| File name 	| Data type | Description text 	|
	| TheProof.csv 	| Script 	| foo bar 			| 
	| CC0_pixel.jpg | Annotation| hello world 		|
	When I press "Complete and return to Your Uploaded Datasets page"
	Then I should be on "/user/view_profile#submitted"
	And I should see "File uploading complete"
	And I should see "DataAvailableForReview"
	Given I sign in as an admin
	When I go to "/adminDataset/admin"
	And I press "Update Dataset" for dataset "000007"
	Then I should see "Status changed to DataAvailableForReview"
