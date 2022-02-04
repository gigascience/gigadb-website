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
    Then I am on "/user/reset/username//style/float%3Aright"
    And I should see "If you have lost your password, enter your email and we will send a new password to the email address associated with your account."
    And I should see "Email *"
    And I should see a submit button "Reset"
    And I should not see "Mailing list"
    And I should not see "Please tick here to join the GigaDB mailing list to receive news, updates and quarterly newsletters about GigaDB"
    And I should not see "User_newsletter" checkbox
    And I should not see "Terms and Conditions *"
    And I should not see "Please tick here to confirm you have read and understood our Terms of use and Privacy Policy"
    And I should not see "User_terms" checkbox

  @ok
  Scenario: Check reset password functionality
    When I am on "/user/reset/username//style/float%3Aright"
    And I fill in the field of "name" "User[email]" with "user@mailinator.com"
    Then I am on "/user/resetThanks"
    And I should see "Password Reset"
    And I should see "If it is valid, we will send new password."