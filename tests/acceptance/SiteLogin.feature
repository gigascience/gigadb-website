@ok-needs-secrets
Feature: Access
  As a website user
  I want a form to connect
  So that I can access the website according to my access and permissions

  @ok
  Scenario: Can connect to admin dashboard for admins
    When I am on "/site/login"
    And I fill in the field of "name" "LoginForm[username]" with "admin@gigadb.org"
    And I fill in the field of "name" "LoginForm[password]" with "gigadb"
    And I press the button "Login"
    Then I should see "Admin"
    And I should see "Joe's GigaDB Page"
    And I am on "/site/admin"
    And I should see "Administration Page"

  @ok
  Scenario: Can't connect to admin dashboard for user
    When I am on "/site/login"
    And I fill in the field of "name" "LoginForm[username]" with "user@gigadb.org"
    And I fill in the field of "name" "LoginForm[password]" with "gigadb"
    And I press the button "Login"
    Then I should not see "Admin"
    And I am on "/site/admin"
    And I should see "You are not authorized to perform this action."

  @ok
  Scenario: Can connect to user dashboard for user
    When I am on "/site/login"
    And I fill in the field of "name" "LoginForm[username]" with "user@gigadb.org"
    And I fill in the field of "name" "LoginForm[password]" with "gigadb"
    And I press the button "Login"
    Then I should see "John's GigaDB Page"
