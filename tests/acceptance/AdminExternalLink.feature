@ok
Feature: form to create an external link
  As a curator
  I want to be able to add or update external links to a dataset
  So that I can keep track of my links

  Background:
    Given I have signed in as admin

  @ok
  Scenario: Form loading with all necessary fields
    When I am on "/adminExternalLink/create"
    Then I should see "Additional information"
    And I should see "Genome browser"
    And I should see "Protocols.io"
    And I should see "JBrowse"
    And I should see "3D Models"
    And I should see "Code Ocean"
    And I should see "Github links"
    And I should see "USCS Tumour Map Viewer"
    And I should see "3D Sketchfab"
    And I should see "Description"

  @ok
  Scenario: I can see description on view page
    When I am on "/adminExternalLink/view/id/2"
    Then I should see "Description"
