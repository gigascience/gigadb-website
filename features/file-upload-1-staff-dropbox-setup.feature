Feature:
	As a Staff
	I want to create a restricted file drop box area on GigaDB server
	So that authors can upload their files and select curators can access them

Background:
	Given Gigadb web site is loaded with "gigadb_testdata.pgdmp" data
	And user "joy_fox" is loaded # Author

Scenario: Accessing admin page's list of datasets to setup drop box for a dataset
	Given I sign in as an admin
	And a dataset has been uploaded with temporary DOI "100006" by user "joy_fox"
	And the uploaded dataset has status "AssigningFTPbox"
	And I go to "/site/admin"
	When I press "Datasets"
	Then the response sould contain "100006"
	And the response sould contain "AssigningFTPbox"
	And I should see a "Assign Drop box to dataset 100006" button


Scenario: Triggering the creation of a drop box for a dataset with the appropriate status
	Given I sign in as an admin
	And a dataset has been entered with temporary DOI "100006"
	And the uploaded dataset has status "AssigningFTPbox"
	And I go to "/site/admin"
	When I press "Datasets"
	And I press "Assign Drop box to dataset 100006"
	Then I should see "A drop box associated with dataset 100006 will be created for user Joy Fox" flash message

Scenario: Status is changed after the drop box is created and email sent
	Given I sign in as an admin
	And a dataset has been entered with temporary DOI "100006"
	And the uploaded dataset has status "AssigningFTPbox"
	And a drop box to dataset "100006" has been created for the user
	And upload instructions have been emailed to the user
	And I go to "/site/admin"
	When I press "Datasets"
	Then the response sould contain "100006"
	And the response sould contain "UserUploadingData"
	And I should not see a "Assign Drop box to dataset 100006" button