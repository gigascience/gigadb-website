@create-mockup @mockup
Feature:
	As a Curator
	I want to create a private mockup of dataset linked to the privately uploaded files
	So that I can share access to privately uploaded files of a submitted dataset with select curators

Background:
	Given there is a user "Artie" "Dodger"
	And a dataset with DOI "000007" owned by user "Artie" "Dodger" has status "DataAvailableForReview"
	And filedrop account for DOI "000007" does exist

@ok
Scenario: There is a button to generate mockup when status is Submitted
	Given I sign in as an admin
	And I go to "/adminDataset/admin"
	And I press "Update Dataset" for dataset "000007"
	And change the status to "Submitted"
	When I go to "/adminDataset/admin"
	And I press "Update Dataset" for dataset "000007"
	Then I should see a "Generate mockup for reviewers" link

@ok
Scenario: There is not a button to generate mockup when status is not Submitted
	Given I sign in as an admin
	When I go to "/adminDataset/admin"
	And I press "Update Dataset" for dataset "000007"
	Then I should not see a "Generate mockup for reviewers" link

@ok
Scenario: Generating a mockup when status is Submitted
	Given I sign in as an admin
	And I go to "/adminDataset/admin"
	And I press "Update Dataset" for dataset "000007"
	And change the status to "Submitted"
	When I go to "/adminDataset/admin"
	And I press "Update Dataset" for dataset "000007"
	And I press "Generate mockup for reviewers"
	And I wait "1" seconds
	And I fill in "revieweremail" with "reviewer@gigadb.org"
	And I select "3" for "monthsofvalidity"
	And I press "Generate mockup"
	Then I should be on "/adminDataset/admin"
	And I should see "Unique (reviewer@gigadb.org)"
	And I should see "time-limited (3 months) mockup url ready"
	And I should see "/dataset/mockup/uuid/"
