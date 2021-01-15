@data-validation
Feature:
	As a Curator
	I want to represent as a dataset status whether the dataset files and metadata are complete or not
	So that we avoid missing information or files once the dataset is public

Background:
	Given there is "user" "Artie" "Dodger"
	And there is "admin" "Ben" "Hur"
	And The user "Ben" "Hur" is registered as authorised user in the API	
	And a dataset with DOI "000007" owned by user "Artie" "Dodger" has status "Submitted"
	And filedrop account for DOI "000007" does exist


@ok
Scenario: Curator set the status to "DataPending" if something is missing, causing a curation log entry, and email notification
	Given I sign in as an admin
	When I go to "/adminDataset/admin"
	And I press "Update Dataset" for dataset "000007"
	And change the status to "DataPending"
	And I go to "/adminDataset/admin"
	And I press "Update Dataset" for dataset "000007"
	Then I should see "Status changed to DataPending"
	And I am on "/adminDataset/admin"
	And An email is sent to "artie_dodger@gigadb.org"

@ok
Scenario: Curator set the status to "Curation" when files and metadata are complete, causing a curation log entry
	Given I sign in as an admin
	When I go to "/adminDataset/admin"
	And I press "Update Dataset" for dataset "000007"
	And change the status to "Curation"
	And I go to "/adminDataset/admin"
	And I press "Update Dataset" for dataset "000007"
	Then I should see "Status changed to Curation"