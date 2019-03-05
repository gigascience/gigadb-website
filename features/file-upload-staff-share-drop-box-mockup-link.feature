Feature:
	As a Staff
	I want to create a private mockup of dataset linked to the privately uploaded files
	So that I can share access to privately uploaded files of a submitted dataset with select curators

Scenario: The button appears in a dataset row from the admin list when status is Submitted
	Given I am on the admin page
	And user has a dataset "100006" with status "Submitted"
	When I click on "Dataset"
	Then I should see the button "Share drop box mockup link for dataset 100006"

Scenario: The button does not appear in a dataset row from the admin list when status is not Submitted
	Given I am on the admin page
	And user has a dataset "100006" with status "DataAvailableForReview"
	When I click on "Dataset"
	Then I should not see the button "Share drop box mockup link for dataset 100006"

Scenario: Create a unique link to private mockup dataset page showing the files in the drop box for curators
	Given I am on the admin datasets page
	And user has a dataset "100006" with status "Submitted"
	When I click the "Share drop box mockup link for dataset 100006"
	Then I should see a unique link to the private mockup page associated with drop box files
	And I should see "Close message"