Feature:
	As a Curator
	I want to access the privately uploaded files of a submitted dataset
	So that I can download and audit the files and their metadata

Background:
	Given Gigadb web site is loaded with "gigadb_testdata.pgdmp" data
	And user "joy_fox" is loaded # Author
	And a drop box has been created for the submitted dataset

Scenario: private and unique link allow me to access files and metadata for a dataset with status Submitted
	Given I have a received a link "/dataset/mockup/6ba41e9f81baf4ba2bb6d5ecc3e858b0"
	And a set of files has been uploaded to the drop box
	And user has filled in metadata for all the files
	And the uploaded dataset has status "Submitted"
	When I go to "/dataset/mockup/6ba41e9f81baf4ba2bb6d5ecc3e858b0"
	Then I should see the locations of files in drop box
	And I should see the metadata associated to the files

Scenario: I can download the drop box file locations from the private mockup dataset page
	Given I have a received a link "/dataset/mockup/6ba41e9f81baf4ba2bb6d5ecc3e858b0"
	And a set of files has been uploaded to the drop box
	And user has filled in metadata for all the files
	And the uploaded dataset has status "Submitted"
	And I am on "/dataset/mockup/6ba41e9f81baf4ba2bb6d5ecc3e858b0"
	When I follow "file1.txt"
	Then The "file1.txt" file should be downloaded