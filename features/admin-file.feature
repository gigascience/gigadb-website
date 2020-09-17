@admin-file @issue-457
  Feature: An admin user can edit and delete attribute in admin file page
    As an admin user
    I can add/edit/save/delete attribute

  Background:
    Given Gigadb web site is loaded with "gigadb_testdata.pgdmp" data
    And default admin user exists
    When I go to "/adminFile/update/"
    And I should see "Login"

  @ok @issue-457
  Scenario: Sign in as admin and visit admin file update page
    Given I sign in as an admin
    And I go to "/adminFile/update/id/88252"
    Then I should see a button "New Attribute"

  @wip @issue-457 @javascript
    Scenario: Sign in as admin to add new attribute and to see a delete button
      Given I sign in as an admin
      And I am on "/adminFile/update/id/88252"
      And I should see a button "New Attribute"
      And I take a screenshot named "test-3"
#      And I should see a button "Delete"
#      When I press "New Attribute"
#      And I fill in "Value" with "Hello world"
#      And I press "Add"
#      Then I should see a button "Delete"