@move-files
Feature:
	As a Curator
	I want to transfer the curated files of submitted datasets to the GigaDB public ftp server
	So that the curated files of submitted datasets are available to the public

Background:
	Given there is a user "Artie" "Dodger"
	And a dataset with DOI "000007" owned by user "Artie" "Dodger" has status "Curation"
	And filedrop account for DOI "000007" does exist
	And file uploads with samples and attributes have been uploaded for DOI "000007"

@wip
Scenario: there's a button to trigger file transfer for dataset with status Curation
	Given I sign in as an admin
	When I go to "/adminDataset/admin"
	And I press "Update Dataset" for dataset "000007"
	Then I should see a "Move files to public ftp" link


Scenario: there's no button to trigger file transfer for dataset if status not Curation
	Given a dataset with DOI "000007" owned by user "Artie" "Dodger" has status "DataPending"
	And I sign in as an admin
	And I am on "/site/admin"
	When I press "Datasets"
	Then I should not see a "Move files to public ftp" link

Scenario: Clicking the move button create a job for the workers
	Given I sign in as an admin
	And I am on "/site/admin"
	When I press "Datasets"
	And I press "Move files to public ftp"
	Then I should see "The files are being moved to public ftp"

Scenario: The completion of moving all files triggers update of the file database table
	Given all files have be moved to the public ftp repository
	And I am on "/datasets/000007"
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

