@issue-515
Feature: Allow search engines to crawl and index GigaDB pages
  As a researcher
  I want all search engines, including google to recognise all GigaDB pages url
  So that all search engines can crawl and index GigaDB pages

  Background:
    Given Gigadb web site is loaded with "gigadb_testdata.pgdmp" data

  @ok
  Scenario: Go to faq page and search engines recognition is allowed
    Given I am not logged in to Gigadb web site
    When I go to "/site/faq"
    Then there is a meta tag "robots" with value "index, nofollow"

  @ok
  Scenario: Go to main page and search engines recognition is allowed
    Given I am not logged in to Gigadb web site
    When I go to "/index.php"
    Then there is a meta tag "robots" with value "index, nofollow"

  @ok
  Scenario: Go to dataset page and search engines recognition is allowed
    Given I am not logged in to Gigadb web site
    When I go to "/dataset/100002"
    Then there is a meta tag "robots" with value "index, nofollow"