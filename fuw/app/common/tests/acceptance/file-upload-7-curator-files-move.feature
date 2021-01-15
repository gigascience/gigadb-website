@move-files
Feature:
	As a Curator
	I want to transfer the curated files of submitted datasets to the GigaDB public ftp server
	So that the curated files of submitted datasets are available to the public

Background:
	Given there is "user" "Artie" "Dodger"
	And there is "admin" "Ben" "Hur"
	And The user "Ben" "Hur" is registered as authorised user in the API	
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
	And I wait "1" seconds
	When I go to "/adminDataset/admin"
	And I press "Update Dataset" for dataset "000007"
	And I press "Move files to public ftp"
	Then I should see "2 files are being moved to public ftp. It may take a moment"

@ok @no-ci
Scenario: The files are copied to the new location when the workers complete the job
	Given I sign in as an admin
	And file uploads with samples and attributes have been uploaded for DOI "000007"
	And I wait "1" seconds
	And I go to "/adminDataset/admin"
	And I press "Update Dataset" for dataset "000007"
	And I press "Move files to public ftp"
	And I wait "1" seconds
	When all files have been moved to the public ftp repository
	Then files exist at new location "ftp/public"
	| doi | file name |
	| 000007 | seq1.fa |
	| 000007 | Specimen.pdf |

@ok @no-ci
Scenario: Files that have been moved are marked as such in File Upload Wizard API
	Given I sign in as an admin
	And file uploads with samples and attributes have been uploaded for DOI "000007"
	And I wait "1" seconds	
	And I go to "/adminDataset/admin"
	And I press "Update Dataset" for dataset "000007"
	And I press "Move files to public ftp"
	And I wait "1" seconds
	When all files have been moved to the public ftp repository
	Then uploads are flagged as "synchronized"
	| doi | file name |
	| 000007 | seq1.fa  |
	| 000007 | Specimen.pdf  |

@ok @no-ci
Scenario: Completion of moving files triggers update of the file database table
	Given I sign in as an admin
	And file uploads with samples and attributes have been uploaded for DOI "000007"
	And I wait "1" seconds	
	And I go to "/adminDataset/admin"
	And I press "Update Dataset" for dataset "000007"
	And I press "Move files to public ftp"
	And I wait "1" seconds
	When all files have been moved to the public ftp repository
	And I browse to the dataset page for "000007"
	Then I should see the files
	| File Name | Data Type | File Format | Size |
	| seq1.fa |  Sequence assembly | FASTA | 23.43 MiB |
	| Specimen.pdf | Annotation | PDF | 19.11 KiB |
	And there is a download link for each file associated with DOI "000007"
	| File Name |
	| seq1.fa | 
	| Specimen.pdf |

@ok @no-ci
Scenario: Completion of moving files triggers update of the file, attributes tables
	Given I sign in as an admin
	And file uploads with samples and attributes have been uploaded for DOI "000007"
	And I wait "1" seconds
	And reference data for Attribute for Unit is created for
	| Table | Name | Id |
	| attribute | growth temperature ||
	| attribute | MD5 checksum ||
	| unit | degree celsius | UO:0000027 |	
	And I go to "/adminDataset/admin"
	And I press "Update Dataset" for dataset "000007"
	And I press "Move files to public ftp"
	And I wait "1" seconds
	When all files have been moved to the public ftp repository
	And I browse to the dataset page for "000007"
	Then I should see the files
	| File Name | Data Type | File Format | Size | File Attributes (1st) | File Attributes (2nd) |
	| seq1.fa | Sequence assembly | FASTA | 23.43 MiB | growth temperature: 45 | MD5 checksum: 75 |
	| Specimen.pdf | Annotation | PDF | 19.11 KiB | growth temperature: 51 | MD5 checksum: 90 |
	And there is a download link for each file associated with DOI "000007"
	| File Name |
	| seq1.fa | 
	| Specimen.pdf |

@ok @no-ci
Scenario: Completion of moving files triggers update of the file, attributes and samples tables
	Given I sign in as an admin
	And file uploads with samples and attributes have been uploaded for DOI "000007"
	And I wait "1" seconds
	And reference data for Attribute for Unit is created for
	| Table | Name | Id |
	| attribute | growth temperature ||
	| attribute | MD5 checksum ||
	| unit | degree celsius | UO:0000027 |		
	And I go to "/adminDataset/admin"
	And I press "Update Dataset" for dataset "000007"
	And I press "Move files to public ftp"
	And I wait "1" seconds
	When all files have been moved to the public ftp repository
	And I browse to the dataset page for "000007"
	Then I should see the files
	| File Name | Sample ID | Data Type | File Format | Size | File Attributes (1st) | File Attributes (2nd) |
	| seq1.fa | Sample A, Sample Z | Sequence assembly | FASTA | 23.43 MiB | growth temperature: 45 | MD5 checksum: 75 |
	| Specimen.pdf | Sample E | Annotation | PDF | 19.11 KiB | growth temperature: 51 | MD5 checksum: 90 |
	And there is a download link for each file associated with DOI "000007"
	| File Name |
	| seq1.fa | 
	| Specimen.pdf |

@not-yet
Scenario: Completion of moving files triggers notification to curators
	Given I sign in as an admin
	And file uploads with samples and attributes have been uploaded for DOI "000007"
	And I go to "/adminDataset/admin"
	And I press "Update Dataset" for dataset "000007"
	And I press "Move files to public ftp"
	And I wait "1" seconds
	When all files have been moved to the public ftp repository
	And I wait "5" seconds
	Then An email is sent to "Curators"

Scenario: Curator set "AuthorReview" status after the files move, causing a curation log entry
	Given I sign in as an admin
	When I go to "/adminDataset/admin"
	And I press "Update Dataset" for dataset "000007"
	And change the status to "AuthorReview"
	And I go to "/adminDataset/admin"
	And I press "Update Dataset" for dataset "000007"
	Then I should see "Status changed to AuthorReview"

