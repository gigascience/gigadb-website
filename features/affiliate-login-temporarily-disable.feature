@issue-875
Feature: Go to login page and affiliate login options will not seen
  As an author,
  I do not want to see the affiliate login options in the login page
  So that I will not login gigadb web site using affiliate account credentials

  Background:
    Given Gigadb web site is loaded with production-like data

  @ok
  Scenario: Disable affiliate login temporarily
    Given I am not logged in to Gigadb web site
    When I go to "site/login"
    Then I should not see "Or login with your preferred identity provider:"
    And I should not see a button input "Facebook"
    And I should not see a button input "Google"
    And I should not see a button input "Twitter"
    And I should not see a button input "LindedIn"