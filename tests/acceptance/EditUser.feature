Feature: EditUser
  As a curator
  I want a form to edit user details
  So that I can edit a user to GigaDB database

  Background:
    Given I have signed in as user

  @ok
  Scenario: Ensure can't access a specific admin user page
    When I am on "/adminUser/update/id/8"
    Then I should see "Error 403"

  @ok
  Scenario: Ensure can't access a admin user page
    When I am on "/adminUser/admin"
    Then I should see "Error 403"
    
    @ok
  Scenario: Form loading with all necessary fields
    When I am on "/user/view_profile"
    And I press the button "Edit Profile"
    Then I should see "Email *"
    And I should see "First Name *"
    And I should see "Last Name *"
    And I should not see "Role"
    And I should see "Affiliation *"
    And I should see "Link out preference"
    And I should see "Mailing list subscriber"
    And I should see "Change Password"
    And I should see "Submit new dataset"
    And I should see a submit button "Save"
    And I should not see "Terms and Conditions *"
    And I should not see "Verify Code"

  @ok
  Scenario: Ensure I can edit myp profile without changing the password
    Given I am on "/user/view_profile"
    And I press the button "Edit Profile"
    And I fill in the field of "id" "EditProfileForm_first_name" with "modified"
    And I fill in the field of "id" "EditProfileForm_email" with "test@test.fr"
    And I fill in the field of "id" "EditProfileForm_last_name" with "lastName modified"
    And I select "NCBI" from the field "EditProfileForm_preferred_link"
    And I press the button "Save"
    And I should see "modified"
    And I should see "test@test.fr"
    And I should see "NCBI"
    And I should see "lastName modified"

  @ok
  Scenario: Ensure my password must respect certain rules
    Given I am on "/user/view_profile"
    And I press the button "Change Password"
    And I fill in the field of "id" "ChangePasswordForm_password" with "test"
    And I fill in the field of "id" "ChangePasswordForm_confirmPassword" with "test"
    And I press the button "Save"
    And I should see "Make sure your password contains at least 8 characters with 1 uppercase character, 1 number and 1 special character."

  @ok
  Scenario: Ensure I can update my password
    Given I am on "/user/view_profile"
    And I press the button "Change Password"
    And I fill in the field of "id" "ChangePasswordForm_password" with "testTest5*"
    And I fill in the field of "id" "ChangePasswordForm_confirmPassword" with "testTest5*"
    And I press the button "Save"
    And I should see "Password successfully updated"
