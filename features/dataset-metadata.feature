@issue-85 @dataset-metadata-citation-doi @issue-515
Feature: Add the metadata schema on dataset page to allow Hypothesis to parse citation DOI
  As a researcher
  I want the Hypothesis annotation tool to recognise a GigaDB dataset page url as an alias of its DOI
  I want all search engines, including google to recognise all GigaDB pages url
  So that I can correctly cite it irrespective of which website it appears on or I annotate from
  So that all search engines can crawl and index GigaDB pages

  Background:
    Given Gigadb web site is loaded with "gigadb_testdata.pgdmp" data

  @ok @issue-85 @issue-515
  Scenario: Go to dataset page, Hypothesis tag is found and search engines recognition is indexed
    Given I am not logged in to Gigadb web site
    When I go to "/dataset/100002"
    Then I should see "Genomic data from Adelie penguin (Pygoscelis adeliae)."
    And There is a meta tag "citation_doi" with value "10.5072/100002"
    And There is a meta tag "robots" with value "index"
    And There is a meta tag "googlebot" with value "index"

  @ok @issue-515
  Scenario: Go to faq page and search engines recognition is indexed
    Given I am not logged in to Gigadb web site
    When I go to "/site/faq"
    Then There is a meta tag "robots" with value "index"
    And There is a meta tag "googlebot" with value "index"

  @ok @issue-515
  Scenario: Go to main page and search engines recognition is indexed
    Given I am not logged in to Gigadb web site
    When I go to "/index.php"
    Then There is a meta tag "robots" with value "index"
    And There is a meta tag "googlebot" with value "index"


