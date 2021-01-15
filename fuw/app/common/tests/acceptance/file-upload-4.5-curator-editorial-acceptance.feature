@editor-vetting
Feature:
	As a Curator
	I want to know what datasets can go through curation process
	So that my time is only used on vetted manuscripts and not on rejected manuscripts

Background:
	Given there is "user" "Artie" "Dodger"
	And there is "admin" "Ben" "Hur"
	And The user "Ben" "Hur" is registered as authorised user in the API	
	And a dataset with DOI "000007" owned by user "Artie" "Dodger" has status "DataAvailableForReview"
	And filedrop account for DOI "000007" does exist

@ok
Scenario: Editor set the status to "Rejected" causing a curation log entry
	Given I sign in as an admin
	When I go to "/adminDataset/admin"
	And I press "Update Dataset" for dataset "000007"
	And change the status to "Rejected"
	And I go to "/adminDataset/admin"
	And I press "Update Dataset" for dataset "000007"
	Then I should see "Status changed to Rejected"

@ok
Scenario: Editor set the status to "Submitted" causing a curation log entry and an email notification
	Given I sign in as an admin
	When I go to "/adminDataset/admin"
	And I press "Update Dataset" for dataset "000007"
	And change the status to "Submitted"
	And I go to "/adminDataset/admin"
	And I press "Update Dataset" for dataset "000007"
	Then I should see "Status changed to Submitted"
	And An email is sent to "Curators"