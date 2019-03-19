Feature:
	As a curator,
	I want to signal in the dataset status when an author has given the final green light
	So that I can proceed to publish the dataset

Scenario: Curator set the status to "Private" when the author has done final review, causing a curation log entry
	Given I sign in as an admin
	And the uploaded dataset has status "AuthorReview"
	When I go to "/adminDataset/admin"
	And I press "Update Dataset 100006"
	And I changed the status to "Private"
	And I press "Save"
	Then I should see a new entry in curation log containing:
	| Creation Date | Created By | Action | Comments | Last Modified Date | Lat Modified By |
	| !calculated | Joe Bloggs | Status changed to Private | !empty | !empty | !empty |