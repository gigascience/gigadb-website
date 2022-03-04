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
    Then I am on "/resetPasswordRequest/forgot"
    And I should see "Please enter your email. A link to reset your password will be sent to you."
    And I should see "Email"
    And I should see a submit button "Reset"

  @ok
  Scenario: Check request reset password functionality
    When I am on "/resetPasswordRequest/forgot"
    And I fill in the field of "name" "ForgotPassword[email]" with "user@mailinator.com"
    And I press the button "Reset"
    Then I am on "/resetPasswordRequest/thanks" 
    And I should see "Reset Password Request Submitted"
    And I should see "If it is valid, we will send an email containing a link to where you can reset your password."
    
  @ok
  Scenario: Check reset password functionality with valid token
    When I am on "/resetPasswordRequest/reset?token=LcRiT6D70CBa1J9umjRMbgjhfE31Y3Bmd62qSvdm"
    And I fill in the field of "name" "ResetPasswordForm[password]" with "Freed_From_Desire_GALA"
    And I fill in the field of "name" "ResetPasswordForm[confirmPassword]" with "Freed_From_Desire_GALA"
    And I press the button "Save"
    Then I am on "/site/login"
    And I should see "Login"

  @ok
  Scenario: Check invalid reset password token takes you back to request reset password page
    When I am on "/resetPasswordRequest/reset?token=123456789"
    Then I am on "/resetPasswordRequest/forgot"
    And I should see "Forgotten password"
