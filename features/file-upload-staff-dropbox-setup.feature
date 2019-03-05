Feature:
	As a Staff
	I want to create a restricted file drop box area on GigaDB server
	So that authors can upload their files and select curators can access them

Scenario: Accessing admin page's list of datasets to setup drop box for a dataset
	Given I am on the admin page
	And user has a dataset "100006" with status "AssigningFTPbox"
	When I click the "Dataset" button
	Then the response sould contain "100006"
	And the response sould contain "AssigningFTPbox"
	And I should see a button "Assign Drop box to dataset 100006"


Scenario: Triggering the creation of a drop box for a dataset with the appropriate status
	Given I am on the admin datasets page
	And user has a dataset "100006" with status "AssigningFTPbox"
	When I click the "Assign Drop box to dataset 100006"
	Then I should see "A drop box associated with dataset 100006 will be created for user John Smith"
	And I should see "Close message"
	And I should not see a button "Share drop box mockup link for dataset 100006"

Scenario: Status is changed after the drop box is created and email sent
	Given I am on the admin page
	And user has a dataset "100006" with status "AssigningFTPbox"
	And a drop box to dataset "100006" has been created for the user
	And upload instructions have been emailed to the user
	When I click the "Dataset" button
	Then the response sould contain "100006"
	And the response sould contain "UserUploadingData"
	And I should not see a button "Assign Drop box to dataset 100006"
	And I should see a button "Share drop box mockup link for dataset 100006"

Scenario: Creating a link to mockup dataset page showing the files in the drop box for curators
	Given I am on the admin datasets page
	And user has a dataset "100006" with status "userUploadingData"
	When I click the "Share drop box mockup link for dataset 100006"
	Then I should see a unique link to the mockup page associated with drop box files
	And I should see "Close message"