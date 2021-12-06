Feature: NewUser
  As a curator
  I want a form to enter user details
  So that I can add a new user to GigaDB database

  Scenario: form for creating new user
    Given I am on "/site/login"
    When I follow "Create account"
    Then I should see "Registration"
    And I should not see "Link this user to an author"
    And I should see a text field "User_email"
    And I should see a text field "User_first_name"
    And I should see a text field "User_last_name"
    And I should see a password field "User_password"
    And I should see a password field "User_password_repeat"
    And I should see a text field "User_affiliation"
    And I should see a drop-down field "User_preferred_link" with values
    | values |
    | EBI    |
    | NCBI   |
    | DDBJ   |
    And I should see a check-box field "User_newsletter"
    And I should see a check-box field "User_terms"
    And I should see a link "Terms of use" to "/site/term#policies"
    And I should see a link "Privacy Policy" to "/site/term#privacy"
    And I should see a text field "User_verifyCode"
    And I should see a submit button "Register"