@admins-attach-author-to-user @issue-56 @ok-docker
Feature: a curator can fill in user id in an author record
  As an curator
  I want to connect a user identity to an author record
  So that I can enable gigadb users direct access to the dataset they have authored

  Background:
    Given I have signed in as admin

  @ok @admin-author-form-add-user
  Scenario: populate user identity field when creating an author
    Given I am on "/adminAuthor/create"
    And I fill in the field of "id" "Author_surname" with "Tano"
    And I fill in the field of "id" "Author_first_name" with "Ahsoka"
    And I fill in the field of "id" "Author_middle_name" with "Fulcrum"
    And I fill in the field of "id" "Author_gigadb_user_id" with "345"
    And I press the button "Create"
    Then I should see "Gigadb User"
    And  I should see "345"
    And  I should see "Tano AF"

  @ok @admin-author-form-add-user
  Scenario: populate user identity field when updating an author
    Given I am on "/adminAuthor/update/id/14"
    And I fill in the field of "id" "Author_gigadb_user_id" with "345"
    And I press the button "Save"
    Then I should be on "/adminAuthor/view/id/14"
    And I should see "Gigadb User"
    And I should see "345"

  @ok @admin-author-form-add-user
  Scenario: populate author form with a user id already used triggers error
    Given I am on "/adminAuthor/update/id/14"
    And I fill in the field of "id" "Author_gigadb_user_id" with "345"
    And I press the button "Save"
    And I am on "/adminAuthor/update/id/651"
    And I fill in the field of "id" "Author_gigadb_user_id" with "345"
    And I press the button "Save"
    Then I should see "has already been taken"

  @ok @admin-link-author-from-user
  Scenario: loading the author list directly doesn't show the user specific controls for selecting author to link
    Given I am on "/adminAuthor/admin"
    Then I should not see "Click on a row to proceed with linking that author with user"
    And I should see "Manage Authors"
