Feature:
	As a Staff
	I want to transfer the curated files of submitted datasets to the GigaDB public ftp server
	So that the curated files of submitted datasets are available to the public

Scenario: there's a button to trigger file transfer for dataset with status Curation
	Given I am on the admin page
	And user has a dataset "100006" with status "Curation"
	When I click on "Dataset"
	Then I should see the button "Move files to public ftp for dataset 100006"


Scenario: there's no button to trigger file transfer for dataset if status not Curation
	Given I am on the admin page
	And user has a dataset "100006" with status "DataPending"
	When I click on "Dataset"
	Then I should not see the button "Move files to public ftp for dataset 100006"

Scenario: Clicking the move button create a move job in the backend
	Given I am on the admin datasets page
	And user has a dataset "100006" with status "Curation"
	When I click the "Move files to public ftp for dataset 100006"
	Then I should see "The files associated with dataset 100006 will be moved to the public ftp"
	And I should see "Close message"