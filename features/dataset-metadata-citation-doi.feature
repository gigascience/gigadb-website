@issue-85 @dataset-metadata-citation-doi
Feature: Add the metadata schema on dataset page to allow Hypothesis to parse citation DOI
  As a researcher
  I want the Hypothesis annotation tool to recognise a GigaDB dataset page url as an alias of its DOI
  So that I can correctly cite it irrespective of which website it appears on or I annotate from

  @wip @javascript @issue-85
  Scenario: Go to dataset page and there is a meta-tag with DOI
    Given I am not logged in to Gigadb web site
    When I go to "/dataset/100006"
    Then I should see "Genomic data from Adelie penguin (<em>Pygoscelis adeliae</em>)"
    And There should be a meta-tag with name "citation_doi" and content "10.5072/100006"

