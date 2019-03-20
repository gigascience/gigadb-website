Feature:
	As a Curator,
	I want to represent as a dataset status whether the dataset files and metadata are complete or not
	So that we avoid missing information or files once the dataset is public

Background:
	Given Gigadb web site is loaded with "gigadb_testdata.pgdmp" data
	And user "joy_fox" is loaded # Author

Scenario: Curator set the status to "DataPending" if something is missing, causing a curation log entry, and email notification
	Given I sign in as an admin
	When I go to "/adminDataset/admin"
	And I press "Update Dataset 100006"
	And I changed the status to "DataPending"
	And I press "Save"
	Then I should see a new entry in curation log containing:
	| Creation Date | Created By | Action | Comments | Last Modified Date | Lat Modified By |
	| !calculated | Joe Bloggs | Status changed to DataPending | !empty | !empty | !empty |
	And an email notification should be sent to "joy_fox@gigadb.org"

Scenario: Curator set the status to "Curation" when files and metadata are complete, causing a curation log entry
	Given I sign in as an admin
	When I go to "/adminDataset/admin"
	And I press "Update Dataset 100006"
	And I changed the status to "Curation"
	And I press "Save"
	Then I should see a new entry in curation log containing:
	| Creation Date | Created By | Action | Comments | Last Modified Date | Lat Modified By |
	| !calculated | Joe Bloggs | Status changed to Curation | !empty | !empty | !empty |