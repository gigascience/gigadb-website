@issue-85 @dataset-metadata-citation-doi @issue-515
Feature: Add the metadata schema on dataset page to allow Hypothesis to parse citation DOI
  As a researcher
  I want the Hypothesis annotation tool to recognise a GigaDB dataset page url as an alias of its DOI
  So that I can correctly cite it irrespective of which website it appears on or I annotate from

  Background:
    Given Gigadb web site is loaded with "gigadb_testdata.pgdmp" data

  @wip @issue-85 @issue-515
  Scenario: Go to dataset page and there is a meta-tag with DOI
    Given I am not logged in to Gigadb web site
    When I go to "/dataset/100002"
    Then I should see "Genomic data from Adelie penguin (Pygoscelis adeliae)."
    And There is a meta tag "citation_doi" with value "10.5072/100002"
    And There is a meta tag "robots" with value "index"
    And There is a meta tag "googlebot" with value "index"


