@issue-513
Feature: Add the metadata schema on dataset page to allow other web sites to make link previews of our web site
  As an operator of a partner website
  I want to extract preview metadata from links to GigaDB datasets
  So I can present preview information to my visitors interested in those links

  Background:
    Given Gigadb web site is loaded with "gigadb_testdata.pgdmp" data

  @wip @issue-513
    Scenario: Can be parsed by preview tools that use HTML meta-tags (e.g: search engines)
    Given I am not logged in to Gigadb web site
    When I am on "/dataset/100002"
#    Then I should see "Genomic data from Adelie penguin (Pygoscelis adeliae)."
    Then there is a meta tag "title" with value "GigaDB Dataset - DOI 10.5524/100002 - Genomic data from Adelie penguin (Pygoscelis adeliae)."
#    And there is a meta tag "description" with value "The Adelie penguin (Pygoscelis adeliae) is an iconic penguin of moderate stature and a tuxedo of black and white feathers."


