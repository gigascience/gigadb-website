@issue-875
Feature: Go to login page and affiliate login options will not been seen
  As website user,
  I want there are no affiliate login options in the login page
  So that I will not login gigadb web site using affiliate account credentials

  @ok
  Scenario: Disable affiliate login temporarily
    When I am on "/site/login"
    Then I should not see "Or login with your preferred identity provider:"
