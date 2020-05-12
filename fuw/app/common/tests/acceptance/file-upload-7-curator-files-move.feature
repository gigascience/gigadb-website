@move-files
Feature:
	As a Curator
	I want to transfer the curated files of submitted datasets to the GigaDB public ftp server
	So that the curated files of submitted datasets are available to the public

Background:
	Given there is a user "Artie" "Dodger"
	And a dataset with DOI "000007" owned by user "Artie" "Dodger" has status "Curation"
	And filedrop account for DOI "000007" does exist

@ok
Scenario: there's a button to trigger file transfer for dataset with status Curation
	Given I sign in as an admin
	When I go to "/adminDataset/admin"
	And I press "Update Dataset" for dataset "000007"
	Then I should see a "Move files to public ftp" link

@ok
Scenario: there's no button to trigger file transfer for dataset if status not Curation
	Given I sign in as an admin
	And a dataset with DOI "000008" owned by user "Artie" "Dodger" has status "Published"
	When I go to "/adminDataset/admin"
	And I press "Update Dataset" for dataset "000008"
	Then I should not see a "Move files to public ftp" link

@ok
Scenario: Clicking the move button create a job for the workers
	Given I sign in as an admin
	And file uploads with samples and attributes have been uploaded for DOI "000007"
	When I go to "/adminDataset/admin"
	And I press "Update Dataset" for dataset "000007"
	And I press "Move files to public ftp"
	Then I should see "2 files are being moved to public ftp. It may take a moment"


Scenario: The completion of moving all files triggers update of the file database table
	Given I am on "/monitor"
	And all files have be moved to the public ftp repository
	When I go to "/datasets/000007"
	Then I should see
	| name         | description | datatype |
    | TheProof.csv | first row | Script |
    | CC0_pixel.jpg| second row | Annotation |

Scenario: Curator set "AuthorReview" status after the files move, causing a curation log entry
	Given I sign in as an admin
	When I go to "/adminDataset/admin"
	And I press "Update Dataset" for dataset "000007"
	And change the status to "AuthorReview"
	And I go to "/adminDataset/admin"
	And I press "Update Dataset" for dataset "000007"
	Then I should see "Status changed to AuthorReview"

