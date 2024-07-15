@ok-can-offline
Feature: admin page for species
  As a curator
  I want to be able to update the species information
  So that I can update the species info


  Background:
    Given I have signed in as admin

  @ok @issue-1871
  Scenario: Save a new species without having to fill in Genbank field
    Given I am on "/adminSpecies/create"
    And I should see "Create"
    When I fill in the field of "name" "Species[tax_id]" with "1"
    And I fill in the field of "name" "Species[common_name]" with "foo bar"
    And I fill in the field of "name" "Species[scientific_name]" with "lorem ipsum dolorem"
    And I press the button "Create"
    And I wait "1" seconds
    Then I should see "View Species #"
    And I should see "foo bar"
    And I should see "lorem ipsum dolorem"