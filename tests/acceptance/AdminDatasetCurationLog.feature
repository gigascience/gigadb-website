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
    And I should see "This DOI exists in DataCite already, so it has now been updated with the current values from GigaDB."
    Then I am on "/adminDataset/update/id/8"
    And I wait "3" seconds
    And I should see "Dataset 100006 | Check DOI: OK | update metadata response: OK"
    And I should see "<?xml"
    When I press the button "+"
    And I wait "3" seconds
    Then I should see "Dataset as XML"

  @ok
  Scenario: Fails minting DOI but save xml in curation log
    When I am on "/adminDataset/update/id/5"
    And I press the button "Mint DOI"
    And I wait "3" seconds
    And I should see "This DOI exists in datacite, but failed to update metadata because of: DOI 10.80027/100039: Missing child element(s)"
    Then I am on "/adminDataset/update/id/5"
    And I wait "3" seconds
    And I should see "Failed to send DataCite XML"
    And I should see "<?xml"

  @ok
  Scenario: Minting Doi and adding curation log entry with created_by filled in with by the connected user's name
    When I am on "/adminDataset/update/id/8"
    And I press the button "Mint DOI"
    And I wait "3" seconds
    And I should see "This DOI exists in DataCite already, so it has now been updated with the current values from GigaDB."
    Then I am on "/adminDataset/update/id/8"
    And I wait "3" seconds
    And I should see "Joe Bloggs"
