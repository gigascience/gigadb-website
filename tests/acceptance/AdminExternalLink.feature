@ok-needs-secrets
Feature: form to create an external link
  As a curator
  I want to be able to add or update external links to a dataset
  So that ....

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
    And I should see "Authors code repositories"
    And I should see "USCS Tumour Map Viewer"
    And I should see "Pre-Print"
    And I should see "Software Heritage Archive (SWHA)"
    And I should see "Cited code repository"
