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
  Scenario: Filling in form to provide new password
    Given I sign in as a user
    When I am on "/user/changePassword"
    And I fill in the field "ChangePasswordForm_password" with "123456787"
    And I fill in the field "ChangePasswordForm_confirmPassword" with "123456787"
    And I check the field "ChangePasswordForm_terms"
    And I press the button "Save"
    Then I am on "user/view_profile"
    And I should see "Your profile page"
    And I should see "user@gigadb.org"
    And I should see "John"
    And I should see "Smith"
