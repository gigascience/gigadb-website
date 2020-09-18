@admin-file @issue-457
  Feature: An admin user can edit and delete attribute in admin file page
    As an admin user
    I can add/edit/save/delete attribute

  Background:
    Given Gigadb web site is loaded with production-like data
    When I go to "/adminFile/update/"
    Then I should see "Login"

  @wip @issue-457 @javascript
  Scenario: Sign in as admin and visit admin file update page
    Given I sign in as an admin
    When I go to "/adminFile/update/id/88252"
    Then I should see a button "New Attribute"

  @test @issue-457 @javascript
    Scenario: Sign in as admin to add new attribute and to see a delete button
      Given I sign in as an admin
      And I am on "/adminFile/update/id/88252"
      And I should see a button "New Attribute"
      And I take a screenshot named "test"
