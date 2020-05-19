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

@ok
Scenario: The files are copied to the new location when the workers complete the job
	Given I sign in as an admin
	And file uploads with samples and attributes have been uploaded for DOI "000007"
	And I go to "/adminDataset/admin"
	And I press "Update Dataset" for dataset "000007"
	And I press "Move files to public ftp"
	And I wait "1" seconds
	When all files have been moved to the public ftp repository
	Then files exist at new location "ftp/public"
	| doi | file name |
	| 000007 | seq1.fa |
	| 000007 | Specimen.pdf |

@ok
Scenario: Files that have been moved are marked as such in File Upload Wizard API
	Given I sign in as an admin
	And file uploads with samples and attributes have been uploaded for DOI "000007"
	And I go to "/adminDataset/admin"
	And I press "Update Dataset" for dataset "000007"
	And I press "Move files to public ftp"
	And I wait "1" seconds
	When all files have been moved to the public ftp repository
	Then uploads are flagged as "synchronized"
	| doi | file name |
	| 000007 | seq1.fa  |
	| 000007 | Specimen.pdf  |

# @not-yet
# Scenario: The completion of moving all files triggers update of the file database table
# 	Given I sign in as an admin
# 	And file uploads with samples and attributes have been uploaded for DOI "000007"
# 	And I go to "/adminDataset/admin"
# 	And I press "Update Dataset" for dataset "000007"
# 	And I press "Move files to public ftp"
# 	And I wait "1" seconds
# 	When all files have been moved to the public ftp repository
# 	Then I should see metadata jobs in the queue

Scenario: Curator set "AuthorReview" status after the files move, causing a curation log entry
	Given I sign in as an admin
	When I go to "/adminDataset/admin"
	And I press "Update Dataset" for dataset "000007"
	And change the status to "AuthorReview"
	And I go to "/adminDataset/admin"
	And I press "Update Dataset" for dataset "000007"
	Then I should see "Status changed to AuthorReview"

