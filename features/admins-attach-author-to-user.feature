@admins-attach-author-to-user @issue-56 @ok-docker
Feature: a curator can fill in user id in an author record
	As a curator,
	I want to connect a user identity to an author record
	So that I can enable gigadb users direct access to the dataset they have authored

Background:
	Given Gigadb web site is loaded with "gigadb_testdata.pgdmp" data
	And default admin user exists
	And dataset "100002" exists

@ok @admin-author-form-add-user
Scenario: populate user identity field when creating an author
	Given default admin user exists
	And I sign in as an admin
	And I am on "/adminAuthor/create"
	When I fill in "Surname" with "Tano"
	And I fill in "First Name" with "Ahsoka"
	And I fill in "Middle Name" with "Fulcrum"
	And I fill in "Gigadb User" with "345"
	And press "Create"
	Then the response should contain "Gigadb User"
	And the response should contain "345"
	And the response should contain "Tano AF"

@ok @admin-author-form-add-user
Scenario: populate user identity field when updating an author
	Given default admin user exists
	And I sign in as an admin
	And I am on "/adminAuthor/update/id/3791"
	And I fill in "Gigadb User" with "345"
	And press "Save"
	Then I should be on "/adminAuthor/view/id/3791"
	And I should see "Gigadb User"
	And I should see "345"

@ok @admin-author-form-add-user
Scenario: populate author form with a user id already used triggers error
	Given default admin user exists
	And I sign in as an admin
	And author "3794" is associated with user "345"
	And I am on "/adminAuthor/update/id/3791"
	And I fill in "Gigadb User" with "345"
	And press "Save"
	Then I should be on "/adminAuthor/update/id/3791"
	And I should see "Gigadb User \"345\" has already been taken"


@ok @javascript @admin-link-author-from-user
Scenario: On user list, there is a button to start the process for linking to an author
	Given default admin user exists
	And I sign in as an admin
	And I am on "/user/admin"
	When I click on the row for user id "345"
	And I wait "2" seconds
	Then I should see "Link this user to an author"

@ok @admin-link-author-from-user
Scenario: On user view, there is no  button to start the process for linking to an author
	Given default admin user exists
	And I sign in as an admin
	When I go to "/user/view/id/345"
	Then I should not see "Link this user to an author"

@ok @admin-link-author-from-user
Scenario: On user edit form, there is a button to start the process for linking to an author
	Given default admin user exists
	And I sign in as an admin
	When I go to "/user/update/id/345"
	Then I should see "Link this user to an author"


@ok @admin-link-author-from-user @user-view @linked
Scenario: On user view, if user is already attached to an author, show author name
	Given default admin user exists
 	And default user exists
 	Given author "3794" is associated with user "345"
 	And I sign in as an admin
	When I go to "/user/view/id/345"
	Then the response should not contain "Link this user to an author"
	And the response should not contain "This user has a pending claim. Click for details"
	And the response should contain "Pan S"

@ok @admin-link-author-from-user-edit-form @javascript
Scenario: From user edit-form, load the author list with the user specific controls to select author to link
	Given default admin user exists
	And I sign in as an admin
	And I am on "/user/update/id/345"
	When I follow "Link this user to an author"
	And I wait "2" seconds
	Then I should be on "/adminAuthor/admin"
	And I should see "Click on a row to proceed with linking that author with user John Smith"

@ok @admin-link-author-from-user-edit-form @javascript
Scenario: From user edit form, load the author list with the user specific controls to select author to link
	Given default admin user exists
	And I sign in as an admin
	And I am on "/user/update/id/345"
	When I follow "Link this user to an author"
	And I wait "2" seconds
	Then I should be on "/adminAuthor/admin"
	And I should see "Click on a row to proceed with linking that author with user John Smith"

@ok @admin-link-author-from-user-edit-form @linked
Scenario: On user edit form, if user is already attached to an author, show author name
	Given default admin user exists
 	And default user exists
 	Given author "3794" is associated with user "345"
 	And I sign in as an admin
	When I go to "/user/update/id/345"
	Then the response should not contain "Link this user to an author"
	And the response should not contain "This user has a pending claim. Click for details"
	And the response should contain "Pan S"

@ok @javascript @admin-link-author-from-user-edit-form @pending
Scenario: On user edit form, if user has pending claim, link to pending claims
 	Given default admin user exists
 	And default user exists
 	And a user has a pending claim for author "3791"
	And I sign in as an admin
	When I go to "/user/update/id/345"
	Then the response should not contain "Link this user to an author"
	And the response should contain "This user has a pending claim"

@ok @admin-link-author-from-user @javascript
Scenario: From user list, load the author list with the user specific controls to select author to link
	Given default admin user exists
	And I sign in as an admin
	And I am on "/user/admin/"
	When I click on the row for user id "345"
	And I follow "Link this user to an author"
	And I wait "2" seconds
	Then I should be on "/adminAuthor/admin"
	And I should see "Click on a row to proceed with linking that author with user John Smith"

@ok @admin-link-author-from-user
Scenario: loading the author list directly doesn't show the user specific controls for selecting author to link
	Given default admin user exists
	And I sign in as an admin
	When I go to "/adminAuthor/admin"
	Then the response should not contain "Click on a row to proceed with linking that author with user"
	And the response should contain "Manage Authors"

@ok @admin-link-author-from-user @javascript
Scenario: From author list with the user specific controls, find and link an author
	Given default admin user exists
	And I sign in as an admin
	And I have initiated the search of an author for Gigadb User with ID "345"
	When I click on the row for author id "3791"
	And I wait "2" seconds
	And I should see "User to link to author"
	And I should see "Author to be linked to user"
	And I should see "ID:"
	And I should see "Surname:"
	And I should see "First name:"
	And I should see "Middle name:"
	And I should see "Orcid:"
	And I should see "Already merged with:"
	And I should see "345"
	And I should see "3791"
	And I should see "Smith"
	And I should see "John"
	And I should see "Guojie"
	And I should see "Guojie"
	And I follow "Link user John Smith to that author"
	And I wait "2" seconds
	Then I should be on "/user/view/id/345"
	And I should see "Zhang G"

@ok @admin-link-author-from-user @javascript
Scenario: From author list with the user specific controls, click an author row, then abort the linking
	Given default admin user exists
	And I sign in as an admin
	And I have initiated the search of an author for Gigadb User with ID "345"
	When I click on the row for author id "3791"
	And I wait "2" seconds
	And I follow "Abort and clear selected user"
	And I wait "1" seconds
	And the response should contain "View User #345"
	And the response should not contain "Zhang G"

@ok @admin-link-author-from-user @javascript
Scenario: After a user has been linked to an author, ensure that the session is cleaned up
	Given default admin user exists
	And I sign in as an admin
	And I have initiated the search of an author for Gigadb User with ID "345"
	And I have linked user "John Smith" of id "345" to author "3791"
	When I go to "/adminAuthor/admin"
	Then the response should not contain "Click on a row to proceed with linking that author with user"


@ok @admin-link-author-from-user @javascript
Scenario: From user list, if a user is already linked to an author, show a message rather than a linking button
	Given default admin user exists
	And author "3794" is associated with user "345"
	And I sign in as an admin
	And I am on "/user/admin/"
	When I click on the row for author id "3791"
	And I wait "2" seconds
	And I follow "Link this user to an author"
	And I wait "2" seconds
	Then I should be on "/adminAuthor/admin"
	# And I should see "The user John Smith is already associated to author Pan S (3794)"
	And I should not see "Click on a row to proceed with linking that author with user"

@ok
Scenario: on the user edit form, there is an unlink button to dettach a user from an author
	Given default admin user exists
 	And default user exists
 	Given author "3794" is associated with user "345"
 	And I sign in as an admin
	When I go to "/user/update/id/345"
	Then the response should contain "Unlink author"

@ok
Scenario: when admin click unlink button on user edit form, the user is unlinked from the author
	Given default admin user exists
 	And default user exists
 	Given author "3794" is associated with user "345"
 	And I sign in as an admin
 	When I go to "/user/update/id/345"
 	And I follow "Unlink author"
 	Then I should be on "/user/update/id/345"
 	And I should not see "This user is linked to author:"


# TODO
# @wip
# Scenario: From author list with the user specific controls, click an alert close button, will also clear the user from session

