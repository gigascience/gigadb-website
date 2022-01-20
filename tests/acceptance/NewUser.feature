Feature: NewUser
  As a curator
  I want a form to enter user details
  So that I can add a new user to GigaDB database

  @ok
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

  @ok
  Scenario: Filling in the form to create new user
    Given I am on "/user/create"
    And there is no user with email "martianmanhunter@mailinator.com"
    When I fill in the field of "id" "User_email" with "martianmanhunter@mailinator.com"
    And I fill in the field of "id" "User_first_name" with "J'onn"
    And I fill in the field of "id" "User_last_name" with "J'onzz"
    And I fill in the field of "id" "User_password" with "123456787"
    And I fill in the field of "id" "User_password_repeat" with "123456787"
    And I fill in the field of "id" "User_affiliation" with "GigaScience"
    And I select "NCBI" from the field "User_preferred_link"
    And I check the field "User_terms"
    And I fill in the field of "id" "User_verifyCode" with "shazam"
    And I press the button "Register"
    Then I should see "Welcome!"

  @ok
  Scenario: Providing erroneous captcha prevents submission and show error message
    Given I am on "/user/create"
    And there is no user with email "martianmanhunter@mailinator.com"
    When I fill in the field of "id" "User_email" with "martianmanhunter@mailinator.com"
    And I fill in the field of "id" "User_first_name" with "J'onn"
    And I fill in the field of "id" "User_last_name" with "J'onzz"
    And I fill in the field of "id" "User_password" with "123456787"
    And I fill in the field of "id" "User_password_repeat" with "123456787"
    And I fill in the field of "id" "User_affiliation" with "GigaScience"
    And I select "NCBI" from the field "User_preferred_link"
    And I check the field "User_terms"
    And I fill in the field of "id" "User_verifyCode" with "testCaptcha"
    And I press the button "Register"
    Then I should see "Captcha is incorrect!"
    And I should see a text field "User_email"