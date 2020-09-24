@admin-file @issue-457
  Feature: An admin user can edit and delete attribute in admin file page
    As an admin user
    I can edit/delete attribute
    So that I can associate various attributes to files

  Background:
    Given Gigadb web site is loaded with production-like data


  @ok @issue-457 @javascript
  Scenario: Non admin user cannot visit admin file update page
    Given I am not logged in to Gigadb web site
    When I go to "/adminFile/update/"
    Then I should see "Login"

  @wip @issue-457 @javascript
  Scenario: Sign in as admin and visit admin file update page and see New Attribute, Edit, Delete buttons
    Given I sign in as an admin
    When I am on "/adminFile/update/id/13973"
    Then I should see a button "New Attribute"
    And I should see a button input "Edit"
    And I should see a button input "Delete"

  @wip @issue-457 @javascript
  Scenario: Sign in as admin to delete attribute
    Given I sign in as an admin
    And I am on "/adminFile/update/id/13973"
    And I should see a button input "Delete"
#    And I take a screenshot named "before_delete"
    When I press "Delete"
    And I wait "3" seconds
    Then I should not see a button "Delete"
#    And I take a screenshot named "after_delete"




