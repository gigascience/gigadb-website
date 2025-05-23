@ok-can-offline
Feature: Change password
  As an author
  I want a form to change my password
  So that I can provide a new password for my user account

  @ok
  Scenario: Form for providing new password
    Given I sign in as a user
    When I am on "/user/changePassword"
    Then I should see "Change Password"
    And I should see a password field "ChangePasswordForm_password"
    And I should see a password field "ChangePasswordForm_confirmPassword"
    And I should see a check-box field "ChangePasswordForm_newsletter"
    And I should see a check-box field "ChangePasswordForm_terms"
    And I should see a link "Cancel" to "/user/view_profile"
    And I should see a submit button "Save"

  @ok
  Scenario: Filling out the form to provide a new password, but the password does not meet the regex requirements
    Given I sign in as a user
    When I am on "/user/changePassword"
    And I fill in the field of "id" "ChangePasswordForm_password" with "123456787"
    And I fill in the field of "id" "ChangePasswordForm_confirmPassword" with "123456787"
    And I check the field "ChangePasswordForm_terms"
    And I press the button "Save"
    Then I should see "Make sure your password contains at least 8 characters with 1 uppercase character, 1 number and 1 special character."

  @ok
  Scenario: Filling out the form to provide a new password, and the password does meet the regex requirements
    Given I sign in as a user
    When I am on "/user/changePassword"
    And I fill in the field of "id" "ChangePasswordForm_password" with "Admintest123?"
    And I fill in the field of "id" "ChangePasswordForm_confirmPassword" with "Admintest123?"
    And I check the field "ChangePasswordForm_terms"
    And I press the button "Save"
    Then I should see "Your profile page"
