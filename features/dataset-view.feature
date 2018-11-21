@issue-125 @dataset-view
Feature: a user visit the dataset page
	As a gigadb user
	I want to see all the information pertaining to a dataset
	So that I can use it to further my research or education

	Background:
		Given Gigadb web site is loaded with production-like data

	@ok
	Scenario: Core information
		Given I have added the following keywords to dataset "101001"
		| Keywords |
		| duck |
		| quacking |
		And I am not logged in to Gigadb web site
		When I go to "/dataset/101001"
		Then I should see image "http://gigadb.org/images/data/cropped/101001_Duck.png" with title "Unknown License: Public domain Source: Unknown Photographer: Mallard duck"
		And the response should contain "Genome sequence of the duck (Anas platyrhynchos)"
		And the response should contain "Genomic"
		And the response should contain "January 23, 2014"
		And I should see links to "Authors"
		| Authors |
		| Burt DW |
		| Chen H |
		| Gan S |
		| Huang Y |
		| Kim H |
		| Li Y |
		| Qian W |
		| Zhang Y |
		| Li J |
		| Zhao Y |
		| Yi K |
		| Feng H |
		| Zhu P |
		| Li B |
		| Liu Q |
		| Fairley S |
		| Magor KE |
		| Du Z |
		| Hu X |
		| Goodman L |
		| Tafer H |
		| Vignal A |
		| Lee T |
		| Kim K |
		| Sheng Z |
		| An Y |
		| Searle S |
		| Herrero J |
		| Groenen MAM |
		| Crooijmans RPMA |
		| Faraut T |
		| Cai Q |
		| Webster RG |
		| Aldridge JR |
		| Warren WC |
		| Bartschat S |
		| Kehr S |
		| Marz M |
		| Stadler PF |
		| Smith J |
		| Kraus RHS |
		| Zhao Y |
		| Ren L |
		| Fei J |
		| Morisson M |
		| Kaiser P |
		| Griffin DK |
		| Rao M |
		| Pitel F |
		| Wang J |
		| Li N |
		And the response should contain "10.5524/101001"
		And the response should contain "Available here is the first draft genomic sequence of the duck"
		And I should see links to "Search results for keywords"
		| Search results for keywords |
		| duck |
		| quacking |
		And I should see links to "peer-reviewed publications"
		| peer-reviewed publications |
		| doi:10.1038/ng.2657 |
		| doi:10.1186/2047-217x-3-26 |
		| doi:10.1126/science.1251385 |
		| doi:10.1126/science.1253451 |
		And I should see links to "Accessions (data included in GigaDB)"
		| Accessions (data included in GigaDB) |
		| PRJNA46621 |
		And I should see links to "Accessions (data not in GigaDB)"
		| Accessions (data not in GigaDB) |
		| PRJNA194464 |
		| GSE22967 |
		And I should see links to "Projects"
		| Projects |
		| Go to Genome 10K website |
		| Go to The Avian Phylogenomic Project website |

	@ok
	Scenario: Semantic Links
		Given I am not logged in to Gigadb web site
		When I go to "/dataset/101001"
		Then I should see a "IsSupplementTo" related links to "10.5524/101000"
		And I should see image "/images/google_scholar.png" linking to "http://scholar.google.com/scholar?q=10.5524/101001"
		And I should see image "/images/ePMC.jpg" linking to "http://europepmc.org/search?scope=fulltext&query=(REF:'10.5524/101001')"


	@ok
	Scenario: Non-Tabbed External Links (e.g: Genome Browser)
		Given I am not logged in to Gigadb web site
		When I go to "/dataset/view/id/101001"
		Then I should see a link "http://asia.ensembl.org/Anas_platyrhynchos/Info/Index" to "http://asia.ensembl.org/Anas_platyrhynchos/Info/Index" with title "Genome browser for dataset 101001"

	@ok
	Scenario: Call To Actions - logged in
		Given user "joy_fox" is loaded
		And I sign in as a user
		When I go to "/dataset/101001"
		Then I should see a button "Contact Submitter" linking to submitter's email
		And I should see a button "Your dataset?"

	@ok
	Scenario: Call To Actions - not logged in
		Given I am not logged in to Gigadb web site
		When I go to "/dataset/101001"
		Then I should see a button "Contact Submitter" with no link
		And I should not see a button "Your dataset?"

	@ok
	Scenario:  History
		Given I am not logged in to Gigadb web site
		When I go to "/dataset/101001"
		Then I should see "History" tab with text "File Anas_platyrhynchos.cds updated"

	@ok
	Scenario:  Funding
		Given I have added awardee "Matthew W Hahn" to dataset "100195"
		And I am not logged in to Gigadb web site
		When I go to "/dataset/100195"
		Then I should see "Funding" tab with table
		| Funding body 					| Awardee 			| Award ID 		| Comments |
		| National Science Foundation 	| Matthew W. Hahn	| DEB-1249633	| Matthew W Hahn |

	@ok
	Scenario:  3D Viewer
		Given I am not logged in to Gigadb web site
		And I have added "3D Viewer" link "https://sketchfab.com/models/ea49d0dd500647cbb4b61ad5ca9e659a" to dataset "101001"
		When I go to "/dataset/101001"
		Then I should see "3D Viewer" tab with text "3D Models:"

	@ok
	Scenario:  Protocols.io
		Given I am not logged in to Gigadb web site
		When I go to "/dataset/view/id/100198"
		Then I should see "Protocols.io" tab with text "Protocols.io:"

	@ok
	Scenario:  Code Ocean
		Given I am not logged in to Gigadb web site
		And I have added "Code Ocean" link '<script src="https://codeocean.com/widget.js?id=fceb0521-a26d-441f-9fe0-bccc6a250fc9" async></script>' to dataset "101001"
		When I go to "/dataset/101001"
		Then I should see "Code Ocean" tab with text "Code Ocean:"

	@ok
	Scenario: JBrowse
		Given I am not logged in to Gigadb web site
		And "JBrowse" external link is attached to dataset "101001"
		When I go to "/dataset/101001"
		Then I should see "JBrowse" tab with text "Open the JBrowse"

	@ok
	Scenario: Files
		Given I am not logged in to Gigadb web site
		When I go to "/dataset/101001"
		Then I should see "Files" tab with table
		| File name              							| Sample ID  	| Data Type       	| File Format 	| Size  		| Release date| link |
		| Anas_platyrhynchos.cds 							| Pekin duck 	| Coding sequence  	| FASTA 	   	| 21.50 MiB     | 2015-08-03  | ftp://climb.genomics.cn/pub/10.5524/101001_102000/101001/Anas_platyrhynchos.cds |
	 	| Anas_platyrhynchos.gff 							| Pekin duck 	| Annotation 		| GFF        	| 10.10 MiB 	| 2015-08-03  | ftp://climb.genomics.cn/pub/10.5524/101001_102000/101001/Anas_platyrhynchos.gff |
		| Anas_platyrhynchos.pep 							| Pekin duck 	| Protein sequence 	| FASTA      	| 7.80 MiB  	| 2015-08-03  | ftp://climb.genomics.cn/pub/10.5524/101001_102000/101001/Anas_platyrhynchos.pep |
		| Anas_platyrhynchos_domestica.RepeatMasker.out.gz 	| Pekin duck 	| Other 			| UNKNOWN    	| 7.79 MiB  	| 2015-03-23  | ftp://climb.genomics.cn/pub/10.5524/101001_102000/101001/Anas_platyrhynchos_domestica.RepeatMasker.out.gz |
		| duck.scafSeq.gapFilled.noMito 					| Pekin duck 	| Sequence assembly	| FASTA 		| 1.03 GiB 		| 2013-01-23  | ftp://climb.genomics.cn/pub/10.5524/101001_102000/101001/duck.scafSeq.gapFilled.noMito |
		| pre_03AUG2015_update 								|				| Directory 		| UNKNOWN 		| 50.00 MiB 	| 2015-08-03  | ftp://climb.genomics.cn/pub/10.5524/101001_102000/101001/pre_03AUG2015_update |
		| readme.txt 										|				| Readme 			| TEXT 			| 337 B 		| 2013-01-23  | ftp://climb.genomics.cn/pub/10.5524/101001_102000/101001/readme.txt |

	@ok
	Scenario: Files Tab Call to Actions
		Given I am not logged in to Gigadb web site
		When I go to "/dataset/101001"
		And I follow "Files"
		Then I should see a link "(FTP site)" to "ftp://climb.genomics.cn/pub/10.5524/101001_102000/101001/" with title "FTP site"
		Then I should see a button input "Table Settings"

	@ok @javascript
	Scenario: Files - Table settings controls
		Given I am not logged in to Gigadb web site
		When I go to "/dataset/101001"
		And I follow "Files"
		And I follow "Table Settings"
		And I wait "1" seconds
		Then I should see "Items per page:"
		And I should see an "select.selectPageSize" element
		And I should see "Columns:"
		And I should see "File Description"
		And the "description" checkbox is unchecked
		And I should see "Sample ID"
		And the "sample_id" checkbox is checked
		And I should see "Data Type"
		And the "type_id" checkbox is checked
		And I should see "File Format"
		And the "format_id" checkbox is checked
		And I should see "Size"
		And the "size" checkbox is checked
		And I should see "Release Date"
		And the "date_stamp" checkbox is checked
		And I should see "Download Link"
		And the "location" checkbox is checked
		And I should see "File Attributes"
		And the "attribute" checkbox is unchecked
		And I should see a button "Save changes" with no link
		And I should see a button "Close" with no link

	@ok @javascript
	Scenario: Files - Items per page
		Given I am not logged in to Gigadb web site
		And  I am on "/dataset/101001"
		When I follow "Files"
		And I follow "Table Settings"
		And I wait "1" seconds
		And I select "5" from "pageSize"
		And I follow "Save changes"
		Then I should see "Files" tab with table
		| File name              							| Sample ID  	| Data Type       	| File Format 	| Size  		| Release date| link |
		| Anas_platyrhynchos.cds 							| Pekin duck 	| Coding sequence  	| FASTA 	   	| 21.50 MiB     | 2015-08-03  | ftp://climb.genomics.cn/pub/10.5524/101001_102000/101001/Anas_platyrhynchos.cds |
	 	| Anas_platyrhynchos.gff 							| Pekin duck 	| Annotation 		| GFF        	| 10.10 MiB 	| 2015-08-03  | ftp://climb.genomics.cn/pub/10.5524/101001_102000/101001/Anas_platyrhynchos.gff |
		| Anas_platyrhynchos.pep 							| Pekin duck 	| Protein sequence 	| FASTA      	| 7.80 MiB  	| 2015-08-03  | ftp://climb.genomics.cn/pub/10.5524/101001_102000/101001/Anas_platyrhynchos.pep |
		| Anas_platyrhynchos_domestica.RepeatMasker.out.gz 	| Pekin duck 	| Other 			| UNKNOWN    	| 7.79 MiB  	| 2015-03-23  | ftp://climb.genomics.cn/pub/10.5524/101001_102000/101001/Anas_platyrhynchos_domestica.RepeatMasker.out.gz |
		| duck.scafSeq.gapFilled.noMito 					| Pekin duck 	| Sequence assembly	| FASTA 		| 1.03 GiB 		| 2013-01-23  | ftp://climb.genomics.cn/pub/10.5524/101001_102000/101001/duck.scafSeq.gapFilled.noMito |
		And I sould not see "Files" tab with table
		| File name |
		| pre_03AUG2015_update |
		| readme.txt |

	Scenario: Files Pagination

	Scenario: Samples
		Given I am not logged in to Gigadb web site
		When I go to "/dataset/101001"
		Then I should see a Samples tab with
		| Sample ID 	| Common Name 	| Scientific Name 			| Sample Attributes | Taxonomic ID | Genbank Name |
		| Pekin duck	| Mallard duck 	| Anas Anas_platyrhynchos 	| Estimated genome size:1.4 Funding source:China Agriculctural University Geographic location (country and/or sea,region):Ch... ... + |	8839  |	mallard |


	Scenario: Samples Settings - row
	Scenario: Samples Settings - column
	Scenario: Samples Pagination


