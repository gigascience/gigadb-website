@view-mockup @mockup
Feature:
	As a Reviewer
	I want to access the privately uploaded files of a submitted dataset
	So that I can download and audit the files and their metadata

Background:
	Given there is a user "Artie" "Dodger"
	And a dataset with DOI "000007" owned by user "Artie" "Dodger" has status "Submitted"
	And filedrop account for DOI "000007" does exist

@ok
Scenario: Can access unique and time-limed url of dataset page showing uploaded files
	Given a mockup url has been created for reviewer "artie_dodger@foobar.com" and dataset with DOI "000007"
	When I browse to the mockup url
	Then I should see "Mockup created for artie_dodger@foobar.com, valid for 1 month"


@ok
Scenario: The page at the unique and time-limed url show dataset info
	Given a mockup url has been created for reviewer "artie_dodger@foobar.com" and dataset with DOI "000007"
	When I browse to the mockup url
	Then I should see "Dataset Fantastic"
	And I should see "Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo"


@ok
Scenario: The page at the unique and time-limed url show uploaded files, attributes, samples and download links
	Given file uploads with samples and attributes have been uploaded for DOI "000007"
	And a mockup url has been created for reviewer "artie_dodger@foobar.com" and dataset with DOI "000007"
	When I browse to the mockup url
	Then I should see the files
	| File Name | Sample ID | Data Type | File Format | Size | File Attributes (1st) | File Attributes (2nd) |
	| seq1.fa | Sample A, Sample Z | Sequence assembly | FASTA | 23.43 MiB | Temperature: 45 Celsius | Humidity: 75 |
	| Specimen.pdf | Sample E | Annotation | PDF | 19.11 KiB | Temperature: 51 Celsius | Humidity: 90 |
	And there is a download link for each file associated with DOI "000007"
	| File Name |
	| seq1.fa | 
	| Specimen.pdf | 

