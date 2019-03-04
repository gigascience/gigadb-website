Feature:
	As a Curator
	I want to create a restricted file drop box area on GigaDB server
	So that authors can upload their files and select reviewers can access them

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

Scenario: Information on admin's list of dataset updated after the drop box is created
	Given I am on the admin page
	And user has a dataset "100006" with status "userUploadingData"
	When I click the "Dataset" button
	Then the response sould contain "100006"
	And the response sould contain "userUploadingData"
	And I should not see a button "Assign Drop box to dataset 100006"
	And I should see a button "Create link for reviewers to access Drop box for dataset 100006"

Scenario: Creating a link to mockup dataset page showing the files in the drop box for reviewers
	Given I am on the admin datasets page
	And user has a dataset "100006" with status "userUploadingData"
	When I click the "Create link for reviewers to access Drop box for dataset 100006"
	Then I should see a unique link for reviewers to access the drop box files
	And I should see "Close message"