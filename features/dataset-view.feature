@issue-125 @dataset-view @timeout-prone
Feature: a user visit the dataset page
	As a gigadb user
	I want to see all the information pertaining to a dataset
	So that I can use it to further my research or education

	Background:
		Given Gigadb web site is loaded with production-like data

	@ok @keywords @timeout-prone
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
		And the response should contain "10.5072/101001"
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
		Then I should see a "IsSupplementTo" related links to "10.5072/101000"
		And I should see image "/images/google_scholar.png" linking to "http://scholar.google.com/scholar?q=10.5072/101001"
		And I should see image "/images/ePMC.jpg" linking to "http://europepmc.org/search?scope=fulltext&query=(REF:'10.5072/101001')"

	@ok
	Scenario: IsPreviousVersionOf relation should show an alert warning of old version with link to new version
		Given I am not logged in to Gigadb web site
		When I go to "/dataset/100044"
		Then I should see a "Compiles" related links to "10.5072/100038"
		And I should see a "IsPreviousVersionOf" related links to "10.5072/100148"
		And I should see "New Version Alert"
		And I should see "There is a new version of this dataset available at DOI 10.5072/100148"
		And I should see a button "View new version" with link "/dataset/100148"
		And I should see a button "Continue to view old version" with link "/dataset/100044"
		And I should see a button "Close"
		# Then I take a screenshot named "Dataset view IsPreviousVersionOf"

	@ok
	Scenario: Non-Tabbed External Links (e.g: Genome Browser)
		Given I am not logged in to Gigadb web site
		When I go to "/dataset/101001"
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
		Then I should see "3D Models" tab with text "3D Models:"

	@ok
	Scenario:  Protocols.io
		Given I am not logged in to Gigadb web site
		When I go to "/dataset/100198"
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

	@ok @files @pr464
	Scenario: Files
		Given I am not logged in to Gigadb web site
		When I go to "/dataset/101001"
		Then I should see "Files" tab with table
		| File Name              							| Description  	                                                                    | Data Type       	| Size  		| File Attributes | link |
		| Anas_platyrhynchos.cds 							| predicted coding sequences from draft genome, confirmed with RNAseq data.	        | Coding sequence  	| 21.50 MiB     |                 | ftp://climb.genomics.cn/pub/10.5524/101001_102000/101001/Anas_platyrhynchos.cds |
		| Anas_platyrhynchos.gff 							| genome annotations	                                                            | Annotation 		| 10.10 MiB 	|                 | ftp://climb.genomics.cn/pub/10.5524/101001_102000/101001/Anas_platyrhynchos.gff |
		| Anas_platyrhynchos.pep 							| amino acid translations of coding sequences                                       | Protein sequence 	| 7.80 MiB  	|                 | ftp://climb.genomics.cn/pub/10.5524/101001_102000/101001/Anas_platyrhynchos.pep |
		| Anas_platyrhynchos_domestica.RepeatMasker.out.gz 	| repeat masker output 	                                                            | Other 			| 7.79 MiB  	|                 | ftp://climb.genomics.cn/pub/10.5524/101001_102000/101001/Anas_platyrhynchos_domestica.RepeatMasker.out.gz |
		| duck.scafSeq.gapFilled.noMito 					| draft genome assembly                                                             | Sequence assembly	| 1.03 GiB 		|                 | ftp://climb.genomics.cn/pub/10.5524/101001_102000/101001/duck.scafSeq.gapFilled.noMito |
		| pre_03AUG2015_update 								| folder containing originally submitted data files, prior to update Aug 3rd 2015.	| Directory 		| 50.00 MiB 	|                 | ftp://climb.genomics.cn/pub/10.5524/101001_102000/101001/pre_03AUG2015_update |
		| readme.txt 										|				                                                                    | Readme 			| 337 B 		|                 | ftp://climb.genomics.cn/pub/10.5524/101001_102000/101001/readme.txt |

	@ok @files @pr464
	Scenario: Files - Call to Actions
		Given I am not logged in to Gigadb web site
		When I go to "/dataset/101001"
		And I follow "Files"
		Then I should see a link "(FTP site)" to "ftp://climb.genomics.cn/pub/10.5524/101001_102000/101001/" with title "FTP site"
		Then I should see a button "Table Settings"

	@ok @files @pr464
	Scenario: Files - Table settings controls
		Given I am not logged in to Gigadb web site
		When I go to "/dataset/101001"
		And I follow "Files"
		And I follow "files_table_settings"
		And I wait "1" seconds
		Then I should see "Items per page:"
		And I should see an "select.selectPageSize" element
		And I should see "Columns:"
		And I should see "File Description"
		And the "description" checkbox is checked
		And I should see "Sample ID"
		And the "sample_id" checkbox is unchecked
		And I should see "Data Type"
		And the "type_id" checkbox is checked
		And I should see "File Format"
		And the "format_id" checkbox is unchecked
		And I should see "Size"
		And the "size" checkbox is checked
		And I should see "Release Date"
		And the "date_stamp" checkbox is unchecked
		And I should see "Download Link"
		And the "location" checkbox is checked
		And I should see "File Attributes"
		And the "attribute" checkbox is checked
		And I should see a button "Save changes" with no link
		And I should see a button "Close" with no link

	@ok @pageSize @files @javascript @pr464
	Scenario: Files - Items per page
		Given I am not logged in to Gigadb web site
		And I am on "/dataset/101001"
		When I follow "Files"
		And I have set pageSize to "5" on "files_table_settings"
		Then I should see "Files" tab with table
		| File Name              							| Description  	                                                                    | Data Type       	| Size  		| File Attributes | link |
		| Anas_platyrhynchos.cds 							| predicted coding sequences from draft genome, confirmed with RNAseq data.	        | Coding sequence  	| 21.50 MiB     |                 | ftp://climb.genomics.cn/pub/10.5524/101001_102000/101001/Anas_platyrhynchos.cds |
		| Anas_platyrhynchos.gff 							| genome annotations	                                                            | Annotation 		| 10.10 MiB 	|                 | ftp://climb.genomics.cn/pub/10.5524/101001_102000/101001/Anas_platyrhynchos.gff |
		| Anas_platyrhynchos.pep 							| amino acid translations of coding sequences                                       | Protein sequence 	| 7.80 MiB  	|                 | ftp://climb.genomics.cn/pub/10.5524/101001_102000/101001/Anas_platyrhynchos.pep |
		| Anas_platyrhynchos_domestica.RepeatMasker.out.gz 	| repeat masker output 	                                                            | Other 			| 7.79 MiB  	|                 | ftp://climb.genomics.cn/pub/10.5524/101001_102000/101001/Anas_platyrhynchos_domestica.RepeatMasker.out.gz |
		| duck.scafSeq.gapFilled.noMito 					| draft genome assembly                                                             | Sequence assembly	| 1.03 GiB 		|                 | ftp://climb.genomics.cn/pub/10.5524/101001_102000/101001/duck.scafSeq.gapFilled.noMito |
		And I should not see "Files" tab with table
		| File Name |
		| pre_03AUG2015_update |
		| readme.txt |

	@ok @files @pr464
	Scenario: Files - Columns
		Given I am not logged in to Gigadb web site
		And I am on "/dataset/101001"
		When I follow "Files"
		And I follow "files_table_settings"
		And I wait "1" seconds
		And I check "description"
		And I uncheck "location"
		And I follow "save-files-settings"
		Then I should see "Files" tab with table
		| File Name | Description |  Data Type    | Size  		| File Attributes|
		| Anas_platyrhynchos.cds | predicted coding sequences from draft genome, confirmed with RNAseq data. | Coding sequence  	| 21.50 MiB     |  |

	@ok @files @javascript @pr464
	Scenario: Files - Pagination
		Given I am not logged in to Gigadb web site
		And I am on "/dataset/101001"
		And I follow "Files"
		And I have set pageSize to "5" on "files_table_settings"
		When I follow "Files"
		# And I take a screenshot named "Files tab before clicking pager"
		And I follow "2"
		Then I should see "Files" tab with table
		| File Name              							| Description  	                                                                    | Data Type       	| Size  		| File Attributes | link |
		| pre_03AUG2015_update 								| folder containing originally submitted data files, prior to update Aug 3rd 2015.	| Directory 		| 50.00 MiB 	|                 | ftp://climb.genomics.cn/pub/10.5524/101001_102000/101001/pre_03AUG2015_update |
		| readme.txt 										|				                                                                    | Readme 			| 337 B 		|                 | ftp://climb.genomics.cn/pub/10.5524/101001_102000/101001/readme.txt |

	@ok @files @javascript @pr437
	Scenario: Files - Pagination
		Given I am not logged in to Gigadb web site
		And I am on "/dataset/101001"
		And I follow "Files"
		Then I should see a button input "Go to page"

	@ok @files @javascript @pr437
	Scenario: Files - Pagination
		Given I am not logged in to Gigadb web site
		And I am on "/dataset/101001"
		And I follow "Files"
		And I have set pageSize to "5" on "files_table_settings"
		When I fill in "pageNumber" with "2"
		And I press "Go to page"
		Then I should be on "/dataset/view/id/101001/Files_page/2"
		And I should see "Files" tab with table
		| File Name              							| Description  	                                                                    | Data Type       	| Size  		| File Attributes | link |
		| pre_03AUG2015_update 								| folder containing originally submitted data files, prior to update Aug 3rd 2015.	| Directory 		| 50.00 MiB 	|                 | ftp://climb.genomics.cn/pub/10.5524/101001_102000/101001/pre_03AUG2015_update |
		| readme.txt 										|				                                                                    | Readme 			| 337 B 		|                 | ftp://climb.genomics.cn/pub/10.5524/101001_102000/101001/readme.txt |


	@ok @files @javascript @pr437
	Scenario: Files - Pagination
		Given I am not logged in to Gigadb web site
		And I am on "/dataset/101001"
		And I follow "Files"
		And I have set pageSize to "5" on "files_table_settings"
		And I fill in "pageNumber" with "2"
		And I press "Go to page"
		When I follow "1"
		Then I should see "Files" tab with table
		| File Name              							| Description  	                                                                    | Data Type       	| Size  		| File Attributes | link |
		| Anas_platyrhynchos.cds 							| predicted coding sequences from draft genome, confirmed with RNAseq data.	        | Coding sequence  	| 21.50 MiB     |                 | ftp://climb.genomics.cn/pub/10.5524/101001_102000/101001/Anas_platyrhynchos.cds |
		| Anas_platyrhynchos.gff 							| genome annotations	                                                            | Annotation 		| 10.10 MiB 	|                 | ftp://climb.genomics.cn/pub/10.5524/101001_102000/101001/Anas_platyrhynchos.gff |
		| Anas_platyrhynchos.pep 							| amino acid translations of coding sequences                                       | Protein sequence 	| 7.80 MiB  	|                 | ftp://climb.genomics.cn/pub/10.5524/101001_102000/101001/Anas_platyrhynchos.pep |
		| Anas_platyrhynchos_domestica.RepeatMasker.out.gz 	| repeat masker output 	                                                            | Other 			| 7.79 MiB  	|                 | ftp://climb.genomics.cn/pub/10.5524/101001_102000/101001/Anas_platyrhynchos_domestica.RepeatMasker.out.gz |
		| duck.scafSeq.gapFilled.noMito 					| draft genome assembly                                                             | Sequence assembly	| 1.03 GiB 		|                 | ftp://climb.genomics.cn/pub/10.5524/101001_102000/101001/duck.scafSeq.gapFilled.noMito |

	@ok @files @javascript @pr437
	Scenario: Files - Pagination
		Given I am not logged in to Gigadb web site
		And I am on "/dataset/101001"
		And I follow "Files"
		And I have set pageSize to "5" on "files_table_settings"
		When I fill in "pageNumber" with "2"
		And I hit return
		Then I should be on "/dataset/view/id/101001/Files_page/2"
		And I should see "Files" tab with table
		| File Name              							| Description  	                                                                    | Data Type       	| Size  		| File Attributes | link |
		| pre_03AUG2015_update 								| folder containing originally submitted data files, prior to update Aug 3rd 2015.	| Directory 		| 50.00 MiB 	|                 | ftp://climb.genomics.cn/pub/10.5524/101001_102000/101001/pre_03AUG2015_update |
		| readme.txt 										|				                                                                    | Readme 			| 337 B 		|                 | ftp://climb.genomics.cn/pub/10.5524/101001_102000/101001/readme.txt |

	@ok @javascript @citation-box
	Scenario: To see the citation box and and the citation source
		Given I am not logged in to Gigadb web site
		And I am on "/dataset/101001"
		When I press "Cite Dataset"
		Then I should see "Text"
		And I should see "RIS"
		And I should see "BibTeX"

	@ok @javascript @citation-box
	Scenario: To show the citation text from CrossCite after clicking the Formatted Text button in the citation box
		Given I am not logged in to Gigadb web site
		And I am on "/dataset/101001"
		And I press "Cite Dataset"
		When I click "Text"
		Then I go to new tab and should see "Huang, Y., Li, Y., Burt, D. W., Chen, H., Zhang, Y., Qian, W., Kim, H., Gan, S., Zhao, Y., Li, J., Yi, K., Feng, H., Zhu, P., Li, B., Liu, Q., Fairley, S., Magor, K. E., Du, Z., Hu, X., … Li, N. (2013). Genome sequence of the duck (Anas platyrhynchos) [Data set]. GigaScience Database. https://doi.org/10.5524/101001"

	@ok @javascript @citation-box
	Scenario: To get RIS file after clicking the RIS in the citation box
		Given I am not logged in to Gigadb web site
		And I am on "/dataset/101001"
		And I press "Cite Dataset"
		When I click on the "RIS" button
		Then the response should contain "101001"
		# 200: the request completed successfully
		And the response should contain "200"

	@ok @javascript @citation-box
	Scenario: To get BibTeX file after clicking the BibTeX in the citation box
		Given I am not logged in to Gigadb web site
		And I am on "/dataset/101001"
		And I press "Cite Dataset"
		When I click on the "BibTeX" button
		Then the response should contain "101001"
		# 200: the request completed successfully
		And the response should contain "200"


	@ok @samples
	Scenario: Samples
		Given I am not logged in to Gigadb web site
		When I go to "/dataset/101001"
		Then I should see "Sample" tab with table
		| Sample ID 	| Common Name 	| Scientific Name 			| Sample Attributes | Taxonomic ID | Genbank Name |
		| Pekin duck	| Mallard duck 	| Anas platyrhynchos 	| Estimated genome size:1.4 | 8839  |	mallard |

	@ok @samples
	Scenario: Samples - Call to Actions
		Given I am not logged in to Gigadb web site
		When I go to "/dataset/101001"
		And I follow "Sample"
		Then I should see a button "Table Settings"

	@ok @samples
	Scenario: Samples - Table settings controls
		Given I am not logged in to Gigadb web site
		When I go to "/dataset/101001"
		And I follow "Sample"
		And I follow "samples_table_settings"
		And I wait "1" seconds
		Then I should see "Items per page:"
		And I should see an "select.selectPageSize" element
		And I should see "Common Name"
		And the "common_name" checkbox is checked
		And I should see "Scientific Name"
		And the "scientific_name" checkbox is checked
		And I should see "Sample Attributes"
		And the "sample_attribute" checkbox is checked
		And I should see "Taxonomic ID"
		And the "taxonomic_id" checkbox is checked
		And I should see "Genbank Name"
		And the "genbank_name" checkbox is checked
		And I should see a button "Save changes" with no link
		And I should see a button "Close" with no link

	@ok @samples @javascript @pr464
	Scenario: Samples - Items per page
		Given I am not logged in to Gigadb web site
		And I am on "/dataset/100197"
		When I follow "Sample"
		And I have set pageSize to "5" on "samples_table_settings"
		Then I should see "Sample" tab with table
		| Sample ID 	| Common Name 	| Scientific Name | Sample Attributes | Taxonomic ID | Genbank Name |
		| Ssol. cltw.NI.13 	| |	Schistocephalus solidus |	Description:short PE reads| 70667 | |
		| Ssol.cltw.A.03 	| |	Schistocephalus solidus |	Description:short PE reads| 70667 | |
		| Ssol.cltw.A.07 	| |	Schistocephalus solidus |	Description:short PE reads| 70667 | |
		| Ssol.cltw.A.12 	| |	Schistocephalus solidus |	Description:long PE reads | 70667 | |
		| Ssol.cltw.I.01 	| |	Schistocephalus solidus |	Description:short PE reads| 70667 | |
		And I should not see "Sample" tab with table
		| Sample ID 	 |
		| Ssol.cltw.I.67 |

	@ok @samples @javascript @pr464
	Scenario: Samples - Columns
		Given I am not logged in to Gigadb web site
		And I am on "/dataset/101001"
		When I follow "Sample"
		And I follow "samples_table_settings"
		And I wait "1" seconds
		And I uncheck "common_name"
		And I follow "save-samples-settings"
		Then I should see "Sample" tab with table
		| Sample ID  	| Scientific Name 			| Sample Attributes | Taxonomic ID | Genbank Name |
		| Pekin duck 	| Anas platyrhynchos 	| Estimated genome size:1.4 | 8839  |	mallard |
		And I should not see "Sample" tab with table
		| Common Name 	 |
		| Mallard duck |

	@ok @samples @javascript
	Scenario: Samples - Pagination
		Given I am not logged in to Gigadb web site
		And I am on "/dataset/100197"
		And I follow "Sample"
		And I have set pageSize to "5" on "samples_table_settings"
		When I follow "Sample"
		# And I take a screenshot named "Files tab before clicking pager"
		And I follow "2"
		Then I should see "Sample" tab with table
		| Sample ID 	| Common Name 	| Scientific Name | Sample Attributes | Taxonomic ID | Genbank Name |
		| Ssol.cltw.I.67 	| |	Schistocephalus solidus |	Description:short PE reads | 70667 | |
		| Ssol.cltw.I.98.1 	| |	Schistocephalus solidus |	Description:short PE reads | 70667 | |

