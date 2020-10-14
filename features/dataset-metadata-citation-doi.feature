@issue-85 @dataset-metadata-citation-doi
Feature: Add the metadata schema on dataset page to allow Hypothesis to parse citation DOI
  As a researcher
  I want the Hypothesis annotation tool to recognise a GigaDB dataset page url as an alias of its DOI
  So that I can correctly cite it irrespective of which website it appears on or I annotate from

  @wip @javascript
  Scenario: Go to dataset page and see DOI value
    Given I am not logged in to Gigadb web site
    When I go to "/dataset/100002"
    And I am on "/dataset/100002"
    Then I should see a "DOI" related links to "10.5524/100002"
