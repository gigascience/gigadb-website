Feature:
	As a curator,
	I want to know what datasets can go through curation process
	So that my time is only used on vetted manuscripts and not on rejected manuscripts

Scenario: Editor set the status to "Rejected" causing a curation log entry
	Given I sign in as an admin
	And the uploaded dataset has status "DataAvailableForReview"
	When the status of the dataset has changed to "Rejected"
	And I go to "/adminDataset/admin"
	And I press "Update Dataset 100006"
	Then I should see a new entry in curation log containing:
	| Creation Date | Created By | Action | Comments | Last Modified Date | Lat Modified By |
	| !calculated | Some Editor | Status changed to Rejected | !empty | !empty | !empty |


Scenario: Editor set the status to "Submitted" causing a curation log entry and an email notification
	Given I sign in as an admin
	And the uploaded dataset has status "DataAvailableForReview"
	When the status of the dataset has changed to "Submitted"
	And I go to "/adminDataset/admin"
	And I press "Update Dataset 100006"
	Then I should see a new entry in curation log containing:
	| Creation Date | Created By | Action | Comments | Last Modified Date | Lat Modified By |
	| !calculated | Some Editor | Status changed to Submitted | !empty | !empty | !empty |
	And an email notification should be sent to "database@gigasciencejournal.com"