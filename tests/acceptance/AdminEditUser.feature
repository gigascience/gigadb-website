@ok-needs-secrets
Feature: AdminEditUser
  As a curator
  I want a form to edit user details
  So that I can edit a user to GigaDB database

  Background:
    Given I have signed in as admin

    @ok
  Scenario: I should see all users with pagination
    Given I am on "/adminUser/admin"
    Then I should see the table with the following rows:
      | 8  | test+8@gigasciencejournal.com | Guojie | Zhang | user | BGI |  |  |  |  | test+8@gigasciencejournal.com | No | Yes |
      | 14 | test+14@gigasciencejournal.com | Shifeng | Cheng | user | BGI |  |  |  |  | test+14@gigasciencejournal.com | No | Yes |
    And I should see "Manage Users"

    @ok
  Scenario: Form loading with all necessary fields
    When I am on "/adminUser/update/id/8"
    Then I should see "Email *"
    And I should see "First Name *"
    And I should see "Last Name *"
    And I should see "Role"
    And I should see "Affiliation *"
    And I should see "Preferred Link"
    And I should see "Mailing List"
    And I should see "Activate the user"
    And I should see a submit button "Save"
    And I should see a button "Link this user to an author" with author link
    And I should not see "Terms and Conditions *"
    And I should not see "Verify Code"

  @ok
  Scenario: Ensure I can edit a user without changing the password
    Given I am on "/adminUser/update/id/8"
    And I fill in the field of "id" "User_first_name" with "modified"
    And I fill in the field of "id" "User_email" with "test@test.fr"
    And I fill in the field of "id" "User_last_name" with "lastName modified"
    And I select "NCBI" from the field "User_preferred_link"
    And I select "admin" from the field "User_role"
    And I press the button "Save"
    Then I should see "View User #8"
    And I should see "modified"
    And I should see "test@test.fr"
    And I should see "lastName modified"
    And I should see "admin"

    @ok
  Scenario: Ensure I can activate an account
    Given I am on "/adminUser/update/id/8"
    And I should see "User_is_activated" checkbox is unchecked
    And I check "User_is_activated" checkbox
    And I press the button "Save"
    And I should see "View User #8"
    And I am on "/adminUser/update/id/8"
    And I should see "User_is_activated" checkbox is checked

  @ok
  Scenario: Ensure I can link and unlink an user to an author
    Given I am on "/adminUser/update/id/8"
    And I should see "Link this user to an author"
    And I press the button "Link this user to an author"
    And I should see "Click on a row or on the  button to proceed with linking that author with user Guojie Zhang"
    And I click on row "1" column "6" and icon "4"
    And I wait "3" seconds
    And I should see "Confirm linking this author to the user?"
    When I follow "Link user Guojie Zhang to that author"
    Then I should see "This user is linked to author: Wang J (14)"
    And I am on "/adminUser/update/id/8"
    Then I should see "This user is linked to author: Wang J (14)"
    And I should see "Unlink author"
    And I follow "Unlink author"
    Then I should not see "This user is linked to author: Wang J (14)"
    And I should see "Update User 8"

  @ok
  Scenario: Ensure a warning is given if an user is already linked to an author
    Given I am on "/adminUser/update/id/8"
    And I should see "Link this user to an author"
    And I press the button "Link this user to an author"
    And I should see "Click on a row or on the  button to proceed with linking that author with user Guojie Zhang"
    And I click on row "1" column "6" and icon "4"
    And I wait "3" seconds
    And I should see "Confirm linking this author to the user?"
    When I follow "Link user Guojie Zhang to that author"
    Then I should see "This user is linked to author: Wang J (14)"
    And I am on "/adminUser/admin"
    And I click on row "1" column "14" and icon "3"
    And I press the button "Link this user to an author"
    Then I should see "The user Guojie Zhang is already associated to author Wang J (14)"
