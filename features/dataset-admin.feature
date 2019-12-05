Feature: Dataset form loading
	As an admin
	I want to access a web form with all the dataset's property
	So that I can make the changes I need to the dataset

Background:
	Given Gigadb web site is loaded with "gigadb_testdata.pgdmp" data
	And default admin user exists

@ok @issue-381
Scenario: form loading with all necessary fields
	Given I sign in as an admin
	When I go to "/adminDataset/update/id/210"
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
	And I should not see a form element labelled "Publisher"

@ok @javascript
Scenario: Mint A DOI
	Given I sign in as an admin
	And I am on "/adminDataset/update/id/210"
	When I follow "Mint DOI"
	Then I should see "minting under way, please wait"
	And I should see element "#minting"'s content changing from "minting under way, please wait" to "new DOI successfully minted"

@ok
Scenario: Keywords
	Given I sign in as an admin
	And I am on "/adminDataset/update/id/210"
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


@ok @javascript @ci-js
Scenario: redirect
	Given I sign in as an admin
	And I am on "/adminDataset/update/id/210"
	When I fill in "urltoredirect" with "http://gigadb.dev/dataset/100002/token/banasdfsaf74hsfds"
	And I press "Save"
	And I go to "/dataset/100002/token/banasdfsaf74hsfds"
	Then the url should be "/dataset/100002/token/banasdfsaf74hsfds"
	# And I take a screenshot named "redirect notice page (before)"
	And I should see "Redirect notice"
	And I wait "10" seconds
	# And I take a screenshot named "redirect notice page (after)"
	And I should not see "Redirect notice"

@ok
Scenario: new dataset with mandatory fields filled in
	Given I sign in as an admin
	And I am on "/adminDataset/admin"
	When I follow "Create Dataset"
	And I select "user@gigadb.org" from "Submitter"
	And I fill in "Title" with "My dataset"
	And I fill in "Dataset Size" with "345345324235"
	And I fill in "Image Source" with "Wikimedia"
	And I fill in "Image License" with "CC0"
	And I fill in "Image Photographer" with "Anonymous"
	And I fill in "DOI" with "100900"
	And I fill in "Ftp Site" with "ftp.genomics.cn"
	And I press "Create"
	Then the response should contain "My Dataset"
	And the response should contain "10.5524/100900"
	And I should see a button "Your dataset?"
	And the url should match the pattern "/\/dataset\/view\/id\/100900\/token\//"
