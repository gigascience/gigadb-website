Feature:
	As a Staff
	I want to create a private mockup of dataset linked to the privately uploaded files
	So that I can share access to privately uploaded files of a submitted dataset with select curators

Scenario: The button appears in a dataset row from the admin list when status is Submitted
	Given I sign in as an admin
	And I am on "/site/admin"
	And the uploaded dataset has status "Submitted"
	When I press "Datasets"
	Then I should see a "Create mockup link for dataset 100006" button

Scenario: The button does not appear in a dataset row from the admin list when status is not Submitted
	Given I sign in as an admin
	And I am on "/site/admin"
	And the uploaded dataset has status "DataAvailableForReview"
	When I press "Datasets"
	Then I should not see a "Create mockup link for dataset 100006" button

Scenario: Create a unique link to private mockup dataset page showing the files in the drop box for curators
	Given I sign in as an admin
	And I am on "/site/admin"
	And the uploaded dataset has status "Submitted"
	When I press "Datasets"
	And I press "Create mockup link for dataset 100006"
	Then I should see "Created http://gigadb.dev/dataset/mockup/6ba413643 (copy link to share)" flash message
