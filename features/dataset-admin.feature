Feature: Dataset form loading
	As an admin
	I want to access a web form with all the dataset's property
	So that I can make the changes I need to the dataset

Background:
	Given Gigadb web site is loaded with "gigadb_testdata.pgdmp" data
	And default admin user exists

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
	When I fill in "urltoredirect" with "http://gigadb.test/dataset/100002/token/banasdfsaf74hsfds"
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
	And I fill in "Source" with "Wikimedia"
	And I fill in "License" with "CC0"
	And I fill in "Photographer" with "Anonymous"
	And I fill in "DOI" with "100900"
	And I fill in "Ftp Site" with "ftp.genomics.cn"
	And I press "Create"
	Then the response should contain "My Dataset"
	And the response should contain "10.5524/100900"
	And I should see a button "Your dataset?"
	And the url should match the pattern "/\/dataset\/100900\/token\//"
