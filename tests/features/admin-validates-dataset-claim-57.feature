@issue-57 @admin-manages-dataset-claim @javascript
Feature: Upon notification of a dataset claim, an admin to validate the claim
	As a an admin
	I want to receive an notification when another gigadb user claim authorship on a dataset
	So that I can confirm or invalidate the claim

Background:
	Given Gigadb web site is loaded with "gigadb_testdata.sql" data
	And default admin user exists
	And user "joy_fox" is loaded

@ok
Scenario: On user edit form, admin sees a message with validate/reject button after user submit a claim
	Given a user has a pending claim for author "3791"
	And I sign in as an admin
	When I go to "/user/update/id/346"
	Then the response should contain "This user has a pending claim on author Zhang G"
	And the response should contain "Validate"
	And the response should contain "Reject"
	And the response should contain "Author info"


@ok
Scenario: When admin validates, user view is shown with a messaging indicating the user is linked to an author
	Given a user has a pending claim for author "3791"
	And I sign in as an admin
	And I am on "/user/update/id/346"
	When I follow "Validate"
	Then the response should contain "This user is linked to author: Zhang G (3791)"
	And the response should not contain "Validate"
	And the response should not contain "Reject"
	And the response should not contain "Author info"


# @ok @to-remove
# Scenario: Admin can access pending jobs from the administration page
# 	Given I sign in as an admin
# 	When I go to "/site/admin/"
# 	Then I should see "Users Claims"

# @ok @to-remove
# Scenario: Admin can see claims on dataset authorship
# 	Given a user has a pending claim for author "3791"
# 	And I sign in as an admin
# 	When I go to "/adminUserCommand/admin"
# 	And the response should contain "claim_author"
# 	Then the response should contain "Joy Fox (346)"
# 	And the response should contain "Zhang G (Author 3791)"
# 	And the response should contain "pending"

# @ok @to-remove
# Scenario: Admin can validate claims on dataset authorship
# 	Given a user has a pending claim for author "3791"
# 	And I sign in as an admin
# 	When I go to "/adminUserCommand/admin"
# 	And I click "validate" in the row for claim from "Joy Fox (346)"
# 	And I wait "2" seconds
# 	Then the response should contain "Joe Bloggs"
# 	Then the response should contain "linked"

# @ok @to-remove
# Scenario: Admin can invalidate claims on dataset authorship
# 	Given a user has a pending claim for author "3791"
# 	And I sign in as an admin
# 	When I go to "/adminUserCommand/admin"
# 	And I click "reject" in the row for claim from "Joy Fox (346)"
# 	And I wait "2" seconds
# 	Then the response should contain "rejected"
# 	Then the response should contain "Joe Bloggs"


# @ok @to-remove
# Scenario: Admin can click on claimant to view more info
# 	Given a user has a pending claim for author "3791"
# 	And I sign in as an admin
# 	When I go to "/adminUserCommand/admin"
# 	And I follow "Joy Fox (346)"
# 	Then I should be on "/User/view/id/346"

# @ok @to-remove
# Scenario: Admin can click on author to view more info
# 	Given a user has a pending claim for author "3791"
# 	And I sign in as an admin
# 	When I go to "/adminUserCommand/admin"
# 	And I follow "Zhang G (Author 3791)"
# 	Then I should be on "/AdminAuthor/view/id/3791"


# @ok @to-remove
# Scenario: if a claim is rejected after being approved, the user is unlinked from author
# 	Given a user has a pending claim for author "3791"
# 	And an admin approved the claim for author "3791"
# 	And an admin rejected the claim for author "3791"
# 	When I go to "/AdminAuthor/view/id/3791"
# 	Then I should not see "346"

