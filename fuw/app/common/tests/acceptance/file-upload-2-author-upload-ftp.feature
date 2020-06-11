@author-fileupload @ftp
Feature:
	As an Author
	I want to upload files for my manuscript's dataset using FTP
	So that the dataset can be reviewed and made available online despite web access restriction

Background:
	Given there is a user "Artie" "Dodger"
	And a dataset with DOI "000007" owned by user "Artie" "Dodger" has status "UserUploadingData"
	And filedrop account for DOI "000007" does exist	

@ok
Scenario: ftp upload triggers new upload record saved in database
	Given I sign in as the user "Artie" "Dodger"
	And The user "Artie" "Dodger" is registered as authorised user in the API
	And there are files uploaded by ftp
	| File name 	| DOI 	 |
	| TheProof.csv 	| 000007 |  
	| CC0_pixel.jpg | 000007 |
	And I wait "30" seconds
	When I am on "/authorisedDataset/annotateFiles/id/000007"
	Then I should see form elements:
	| File name 	| Data type | Default 	| Description text | Tag action | Delete action|
	| TheProof.csv 	| form select | Text 	| form input 	| button | button |
	| CC0_pixel.jpg | form select | Image 	| form input 	| button | button |

