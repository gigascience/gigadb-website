Feature:
	As a Curator
	I want to transfer the curated files of submitted datasets to the GigaDB public ftp server
	So that the curated files of submitted datasets are available to the public

Scenario: there's a button to trigger file transfer for dataset with status Curation
	Given I sign in as an admin
	And I am on "/site/admin"
	And the uploaded dataset has status "Curation"
	When I press "Datasets"
	Then I should see a "Move files to public ftp for dataset 100006" button


Scenario: there's no button to trigger file transfer for dataset if status not Curation
	Given I sign in as an admin
	And I am on "/site/admin"
	And the uploaded dataset has status "DataPending"
	When I press "Datasets"
	Then I should not see a "Move files to public ftp for dataset 100006" button

Scenario: Clicking the move button create a move job in the backend
	Given I sign in as an admin
	And I am on "/site/admin"
	And the uploaded dataset has status "Curation"
	When I press "Datasets"
	And I press "Move files to public ftp for dataset 100006"
	Then I should see "The files associated with dataset 100006 will be moved to the public ftp" flash message

