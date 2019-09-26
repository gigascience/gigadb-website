Feature:
	As a Curator,
	I want the retrieve the context of a dataset status change to "DataAvailableForReview"
	So that I can audit or troubleshoot the dataset files and metadata upload process


Scenario: after status is changed to DataAvailableForReview, add entry in curation log
	Given I sign in as a admin
	And the status of the dataset has changed to "DataAvailableForReview"
	When I go to "/adminDataset/admin"
	And I press "Update Dataset 100006"
	Then I should see a new entry in curation log containing:
	| Creation Date | Created By | Action | Comments | Last Modified Date | Lat Modified By |
	| !calculated | Joy Fox | Status changed to DataAvailableForReview | !empty | !empty | !empty |
