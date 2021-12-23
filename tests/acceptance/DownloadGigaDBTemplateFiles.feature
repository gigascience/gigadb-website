Feature: An author can download file template
  As an author
  I want to download upload form template after login
  So that I can upload data using the form

  Scenario: Sign is as user
    Given I am on "/site/login"
    When I fill in the field "LoginForm_username" with "user@gigadb.org"
    And I fill in the field "LoginForm_password" with "gigadb"
    And I press the button "Login"
    Then I should see "John's GigaDB Page"

  Scenario: Go to Download template file page as user
    Given I am on "/site/login"
    When I fill in the field "LoginForm_username" with "user@gigadb.org"
    And I fill in the field "LoginForm_password" with "gigadb"
    And I press the button "Login"
    Then I am on "/datasetSubmission/upload"
    And I should see "Download Template File"
    And I should see "Download Example File 1"
    And I should see "Download Example File 2"