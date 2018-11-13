@issue-125 @dataset-view
Feature: a user visit the dataset page
As a gigadb user
I want to see all the information pertaining to a dataset
So that I can use it to further my research or education

Background:
	Given Gigadb web site is loaded with production-like data

Scenario: Core information
	Given I am not logged in to Gigadb web site
	When I go to "/dataset/101001"
	Then I should see the title "Genome sequence of the duck (Anas platyrhynchos)"
	Then I should see the category "Genomic"
	Then I should see the release date "January 23, 2014"
	And I should see the authors "Burt DW; Chen H; Gan S; Huang Y; Kim H; Li Y; Qian W; Zhang Y; Li J; Zhao Y; Yi K; Feng H; Zhu P; Li B; Liu Q; Fairley S; Magor KE; Du Z; Hu X; Goodman L; Tafer H; Vignal A; Lee T; Kim K; Sheng Z; An Y; Searle S; Herrero J; Groenen MAM; Crooijmans RPMA; Faraut T; Cai Q; Webster RG; Aldridge JR; Warren WC; Bartschat S; Kehr S; Marz M; Stadler PF; Smith J; Kraus RHS; Zhao Y; Ren L; Fei J; Morisson M; Kaiser P; Griffin DK; Rao M; Pitel F; Wang J; Li N"
	And I should see the DOI identifier "10.5524/101001"
	And I should see the description containing "Available here is the first draft genomic sequence of the duck"
	And I should see the link to publication "https://doi.org/10.1038/ng.2657"
	And I should see the link to publication "https://doi.org/10.1186/2047-217x-3-26"
	And I should see the link to publication "https://doi.org/10.1126/science.1251385"
	And I should see the link to publication "https://doi.org/10.1126/science.1253451"
	And I should see the project link "http://www.genome10k.org/"
	And I should see the project link "http://avian.genomics.cn/en/index.html"

Scenario: Semantic Links
	Given I have added keywords "duck, quack" to dataset "101001"
	And I am not logged in to Gigadb web site
	When I go to "/dataset/101001"
	Then I should see the related link "isSupplementTo" to "10.5524/101000"
	And I should see the link to datasets with keyword "duck"
	And I should see the link to datasets with keyword "quack"
	And I should see the link to Google scholar
	And I should see the link to Europe PMC

Scenario: Files
	Given I am not logged in to Gigadb web site
	When I go to "/dataset/101001"
	Then I should see a Files tab with
	| File name              							| Sample ID  	| Data Type       	| File Format 	| Size  		| Release date| link |
	| Anas_platyrhynchos.cds 							| Pekin duck 	| Coding sequence  	| FASTA 	   	| 21.50 MiB     | 2015-08-03  | link |
 	| Anas_platyrhynchos.gff 							| Pekin duck 	| Annotation 		| GFF        	| 10.10 MiB 	| 2015-08-03  | link |
	| Anas_platyrhynchos.pep 							| Pekin duck 	| Protein sequence 	| FASTA      	| 7.80 MiB  	| 2015-08-03  | link |
	| Anas_platyrhynchos_domestica.RepeatMasker.out.gz 	| Pekin duck 	| Other 			| UNKNOWN    	| 7.79 MiB  	| 2015-03-23  | link |
	| duck.scafSeq.gapFilled.noMito 					| Pekin duck 	| Sequence assembly	| FASTA 		| 1.03 GiB 		| 2013-01-23  | link |
	| pre_03AUG2015_update 								|				| Directory 		| UNKNOWN 		| 50.00 MiB 	| 2015-08-03  | link |
	| readme.txt 										|				| Readme 			| TEXT 			| 337 B 		| 2013-01-23  | link |

Scenario: Samples
	Given I am not logged in to Gigadb web site
	When I go to "/dataset/101001"
	Then I should see a Samples tab with
	| Sample ID 	| Common Name 	| Scientific Name 			| Sample Attributes | Taxonomic ID | Genbank Name |
	| Pekin duck	| Mallard duck 	| Anas Anas_platyrhynchos 	| Estimated genome size:1.4 Funding source:China Agriculctural University Geographic location (country and/or sea,region):Ch... ... + |	8839  |	mallard |

Scenario:  History
	Given I am not logged in to Gigadb web site
	When I go to "/dataset/101001"
	Then I should see a History tab with
	| Date 				| Action |
	| October 23, 2015 	| File Anas_platyrhynchos.cds updated |

Scenario:  Funding
	Given I have added awardee "Matthew W Hahn" to dataset "100195"
	And I am not logged in to Gigadb web site
	When I go to "/dataset/100195"
	Then I should see a Funding tab with
	| Funding body 					| Awardee 			| Award ID 		| Comments |
	| National Science Foundation 	| Matthew W Hahn	| DEB-1249633	| Matthew W Hahn |


Scenario:  3D Viewer
	Given I am not logged in to Gigadb web site
	And I have added 3D Viewer link "https://sketchfab.com/models/ea49d0dd500647cbb4b61ad5ca9e659a" to dataset "101001"
	When I go to "/dataset/101001"
	Then I should see 3D Viewer tab with model "ea49d0dd500647cbb4b61ad5ca9e659a"

Scenario:  Protocols.io
	Given I am not logged in to Gigadb web site
	When I go to "/dataset/view/id/100198"
	Then I should see a Protocolsio tab with text "7 steps"

Scenario:  Genome Browser
	Given I am not logged in to Gigadb web site
	When I go to "/dataset/view/id/100127"
	Then I should see a link to "http://crocgenome.hpc.msstate.edu/gb2/gbrowse/croc/"

Scenario:  Code Ocean
	Given I am not logged in to Gigadb web site
	And I have added Code Ocean  capsule '<script src="https://codeocean.com/widget.js?id=fceb0521-a26d-441f-9fe0-bccc6a250fc9" async></script>' to dataset "101001"
	When I go to "/dataset/101001"
	Then I should see Code Ocean tab with text "Capsule"


Scenario: Call To Actions
	Given I am logged in as a user
	When I go to "/dataset/101001"
	Then I should see an active button "Contact Submitter"
	And I should see an active button "Your dataset?"







