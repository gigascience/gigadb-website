@ok-needs-secrets
Feature: Reset password
  As an author
  I want to reset my password
  So that I can sign into GigaDB website again

  @ok
  Scenario: Go to login page
    When I am on "/site/login"
    Then I should see "Lost Password"

  @ok
  Scenario: Check reset password page
    When I am on "/site/login"
    And I follow "Lost Password"
    Then I am on "/site/forgot"
    And I should see "Please enter your email. A link to reset your password will be sent to you."
    And I should see "Email"
    And I should see a submit button "Reset Password"

  @ok
  Scenario: Unknown email address will display thanks page
    When I am on "/site/forgot"
    And I fill in the field of "name" "ForgotPasswordForm[email]" with "user123@modnar.com"
    And I press the button "Reset"
    Then I am on "/site/thanks"
    And I should see "Reset Password Request Submitted"
    And I should see "For security reasons, we cannot tell you if the email you entered is valid or not."

  @ok
  Scenario: Check invalid reset password token takes you back to request reset password page
    When I am on "/site/reset?token=123456789"
    Then I am on "/site/forgot"
    And I should see "Forgotten password"

  @ok
  Scenario: Check request reset password functionality
    When I am on "/site/forgot"
    And I fill in the field of "name" "ForgotPasswordForm[email]" with "user@gigadb.org"
    And I press the button "Reset"
    Then I am on "/site/thanks" 
    And I should see "Reset Password Request Submitted"
    And I should see "If it is valid, we will send an email containing a link to where you can reset your password."

  @ok
  Scenario: Check reset password functionality with valid token
    When I am on "/site/reset?token=6_WVbmz1e-nm6YPm2sTZc9SAkT7IlRgMtfgNFHj3"
    And I fill in the field of "name" "ResetPasswordForm[password]" with "Freed_From_Desire_GALA"
    And I fill in the field of "name" "ResetPasswordForm[confirmPassword]" with "Freed_From_Desire_GALA"
    And I press the button "Save"
    And I am on "/site/login"
    And I should see "Login"
    And I fill in the field of "name" "LoginForm[username]" with "user@mailinator.com"
    And I fill in the field of "name" "LoginForm[password]" with "Freed_From_Desire_GALA"
    When I press the button "Login"
    Then I should see "John's GigaDB Page"
