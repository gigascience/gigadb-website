@issue-785
Feature:
  As an admin
  I do not want all search engines to recognise staging and new live GigaDB page urls
  So that pages under development would not be released

  Background:
    Given Gigadb web site is loaded with production-like data

  @ok
  Scenario: Search engines cannot index and follow faq page
    Given I am not logged in to Gigadb web site
    When I go to "site/faq"
    Then there is a meta tag "robots" with value "noindex, nofollow"
    And there is a meta tag "googlebot" with value "noindex, nofollow"

  @ok
  Scenario: Search engines cannot index and follow main page
    Given I am not logged in to Gigadb web site
    When I go to "/index.php"
    Then there is a meta tag "robots" with value "noindex, nofollow"
    And there is a meta tag "googlebot" with value "noindex, nofollow"

  @ok
  Scenario: Search engines cannot index and follow dataset page
    Given I am not logged in to Gigadb web site
    When I go to "/dataset/100016"
    Then there is a meta tag "robots" with value "noindex, nofollow"
    And there is a meta tag "googlebot" with value "noindex, nofollow"