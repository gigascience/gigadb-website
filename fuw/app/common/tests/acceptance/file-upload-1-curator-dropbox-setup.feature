Feature:
	As a curator
	I want to create a restricted file drop box area on GigaDB server
	So that authors can upload their files and select curators can access them

# Background:
# 	Given Gigadb web site is loaded with "gigadb_testdata.pgdmp" data
# 	And user "joy_fox" is loaded # Author

@first
Scenario: Accessing admin page's list of datasets to setup drop box for a dataset
	Given I sign in as an admin
	And a dataset has been uploaded with temporary DOI "100006" by user "joy_fox"
	And the uploaded dataset has status "AssigningFTPbox"
	And I go to "/site/admin"
	When I press "Datasets"
	Then the response sould contain "100006"
	And the response sould contain "AssigningFTPbox"
	And I should see a "Assign Drop box to dataset 100006" button


# Scenario: Triggering the creation of a drop box for a dataset with the appropriate status
# 	Given I sign in as an admin
# 	And a dataset has been entered with temporary DOI "100006"
# 	And the uploaded dataset has status "AssigningFTPbox"
# 	And I go to "/site/admin"
# 	When I press "Datasets"
# 	And I press "Assign Drop box to dataset 100006"
# 	Then I should see a form element labelled "Author name"
# 	And I should see a form element labelled "Author email address"
# 	And I should see a form element labelled "Message to the author"
# 	And I should see a checked checkbox labelled "Create Drop box"
# 	And I should see a button "Create drop box and email instructions to author"

# Scenario: Creating the drop box and emailing the author custom instructions
# 	Given I sign in as an admin
# 	And a dataset has been entered with temporary DOI "100006"
# 	And the uploaded dataset has status "AssigningFTPbox"
# 	And I am on "/site/admin"
# 	And I have pressed "Assign Drop box to dataset 100006"
# 	When I fill in "Message to the author" with "custom instructions"
# 	And I press "Create drop box and email instructions to author"
# 	Then I should see "A new drop box will be created for this dataset. It will take up to 5mn of minutes. Instructions will be sent to Joy Fox <joy_fox@gigadb.org>" flash message

# Scenario: Creating the drop box and emailing the author templated instructions
# 	Given I sign in as an admin
# 	And a dataset has been entered with temporary DOI "100006"
# 	And the uploaded dataset has status "AssigningFTPbox"
# 	And I am on "/site/admin"
# 	And I have pressed "Assign Drop box to dataset 100006"
# 	When I press "Create drop box and email instructions to author"
# 	Then I should see "A new drop box will be created for this dataset. It will take up to 5mn of minutes. Instructions will be sent to Joy Fox <joy_fox@gigadb.org>" flash message

# Scenario: Creating the drop box and emailing a different author
# 	Given I sign in as an admin
# 	And a dataset has been entered with temporary DOI "100006"
# 	And the uploaded dataset has status "AssigningFTPbox"
# 	And I am on "/site/admin"
# 	And I have pressed "Assign Drop box to dataset 100006"
# 	When I fill in "Author name" with "Terry Bone"
# 	And I fill in "Author email address" with "terry_bone@ecd.org"
# 	And I press "Create drop box and email instructions to author"
# 	Then I should see "A new drop box will be created for this dataset. It will take up to 5mn of minutes. Instructions will be sent to Terry Bone <terry_bone@ecd.org>" flash message

# Scenario: Emailing instructions without creating a drop box
# 	Given I sign in as an admin
# 	And a dataset has been entered with temporary DOI "100006"
# 	And the uploaded dataset has status "AssigningFTPbox"
# 	And I am on "/site/admin"
# 	And I have pressed "Assign Drop box to dataset 100006"
# 	When I fill in "Author name" with "Terry Bone"
# 	And I fill in "Author email address" with "terry_bone@ecd.org"
# 	And I uncheck option "Create drop box"
# 	And I press "Create drop box and email instructions to author"
# 	Then I should see "Instructions will be sent to Terry Bone <terry_bone@ecd.org>" flash message

# Scenario: Status is changed after the drop box is created and email sent
# 	Given I sign in as an admin
# 	And a dataset has been entered with temporary DOI "100006"
# 	And the uploaded dataset has status "AssigningFTPbox"
# 	And the creation of a drop box to dataset "100006" has been initiated
# 	When I wait "5" minutes
# 	And I go to "/site/admin"
# 	And I press "Datasets"
# 	Then the response sould contain "100006"
# 	And the response sould contain "UserUploadingData"
# 	And I should not see a "Assign Drop box to dataset 100006" button

# Scenario: The drop box access details and the author name and email saved in curation log comment
# 	Given I sign in as an admin
# 	And a dataset has been entered with temporary DOI "100006"
# 	And the uploaded dataset has status "AssigningFTPbox"
# 	And the creation of a drop box to dataset "100006" has been initiated
# 	And the status of the dataset has changed to "UserUploadingData"
# 	When I go to "/adminDataset/admin"
# 	And I press "Update Dataset 100006"
# 	Then I should see a new entry in curation log containing:
# 	| Creation Date | Created By | Action | Comments | Last Modified Date | Lat Modified By |
# 	| !calculated | !empty | Status changed to UserUploadingData | Joy Fox, joy_fox@gigadb.org, ftp://gigadb.dev/dropbox/6ba413643/, login: a43654, password: 46349684 | !empty | !empty |

