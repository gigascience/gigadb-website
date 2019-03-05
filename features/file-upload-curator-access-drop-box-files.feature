Feature:
	As a Curator
	I want to access the privately uploaded files of a dataset
	So that I can download and audit the files and their metadata

Scenario: private and unique link allow me to access files and metadata for a dataset with status DataAVailabeForReview
	Given I have a private and unique link to dataset "100006"
	And a drop box to dataset "100006" has been created for the user
	And user has uploaded a set of files to the drop box for dataset "100006"
	And user has filled in metadata for all the files
	And user has a dataset "100006" with status "DataAvailableForReview"
	When I visit that link
	Then I should see the locations of files in drop box
	And I should see the metadata associated to the files

Scenario: I can download the drop box file locations from the private mockup dataset page
	Given I have a private and unique link to dataset "100006"
	And a drop box to dataset "100006" has been created for the user
	And user has uploaded a set of files to the drop box for dataset "100006"
	And user has filled in metadata for all the files
	And user has a dataset "100006" with status "DataAvailableForReview"
	And I visit that link
	When I click on file "file1.txt"
	Then the file should be downloaded