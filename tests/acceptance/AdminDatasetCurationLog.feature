@ok-needs-secrets
Feature: curation log entry under the dataset form
  As a curator
  I want to see see a curation log entry after minting DOI
  So that the curation log entry is visible

  Background:
    Given I have signed in as admin

  @ok
  Scenario: Minting Doi and adding curation log entry
    When I am on "/adminDataset/update/id/8"
    And I press the button "Mint DOI"
    And I wait "3" seconds
    And I should see "This DOI exists in datacite already, no need to mint, but the metadata is updated!"
    Then I am on "/adminDataset/update/id/8"
    And I wait "3" seconds
    And I should see "Dataset 100006 - Check DOI: OK - update md response: OK"
    And I should see "<?xml"
    When I press the button "+"
    And I wait "3" seconds
    Then I should see "Dataset as XML"
