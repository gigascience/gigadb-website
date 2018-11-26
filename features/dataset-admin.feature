Feature: Dataset form loading
	As an admin
	I want to access a web form with all the dataset's property
	So that I can make the changes I need to the dataset

Background:
	Given Gigadb web site is loaded with "gigadb_testdata.pgdmp" data
	And default admin user exists

@ok
Scenario: form loading with all necessary fields
	Given I sign in as an admin
	When I go to "/dataset/update/id/210"
	Then I should see a form element labelled "Submitter *"
	And I should see a form element labelled "Curator Id"
	And I should see a form element labelled "Manuscript Id"
	And I should see a form element labelled "Upload Status"
	And I should see a form element labelled "Workflow"
	And I should see a form element labelled "Epigenomic"
	And I should see a form element labelled "Metagenomic"
	And I should see a form element labelled "Transcriptomic"
	And I should see a form element labelled "Software"
	And I should see a form element labelled "Imaging"
	And I should see a form element labelled "Metabolomic"
	And I should see a form element labelled "Proteomic"
	And I should see a form element labelled "Genomic"
	And I should see a form element labelled "Metadata"
	And I should see a form element labelled "Dataset Size *"
	And I should see a form element labelled "Image Upload"
	And I should see a form element labelled "Image URL"
	And I should see a form element labelled "Image Source *"
	And I should see a form element labelled "Image Tag"
	And I should see a form element labelled "Image License *"
	And I should see a form element labelled "Image Photographer *"
	And I should see a form element labelled "DOI *"
	And I should see a form element labelled "Ftp Site *"
	And I should see a form element labelled "Publisher"
	And I should see a form element labelled "Fair Use Policy"
	And I should see a form element labelled "Publication Date"
	And I should see a form element labelled "Modification Date"
	And I should see a form element labelled "Dataset Size *"
	And I should see a form element labelled "Title *"
	And I should see a form element labelled "Description"
	And I should see a form element labelled "Keywords"
	And I should see a form element labelled "URL to redirect"
	And I should see a button input "Save"
	And I should see a button "Create New Log"

@ok @javascript
Scenario: Mint A DOI
	Given I sign in as an admin
	And I am on "/dataset/update/id/210"
	When I follow "Mint DOI"
	Then I should see "minting under way, please wait"
	And I should see element "#minting"'s content changing from "minting under way, please wait" to "new DOI successfully minted"

@ok
Scenario: Keywords
	Given I sign in as an admin
	And I am on "/dataset/update/id/210"
	When I fill in the "keywords" field with "abcd, a four part keyword, my_keyword, my-keyword, my dodgy tag<script>alert('xss!');</script>"
	And I follow "Save"
	Then I should see links to "Keyword search"
	| Keyword search |
	| abcd |
	| a four part keyword |
	| my_keyword |
	| my-keyword |
	And I should not see links to "Keyword search"
	| Keyword search |
	| my dodgy tag<script>alert('xss!');</script> |
	| my dodgy tag |

