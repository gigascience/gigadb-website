@ok-can-offline
Feature: admin page for samples
  as a curator
  I want to see a table of all samples
  So that quickly navigate to the sample related data I am interested in

  Background:
    Given I have signed in as admin

  @ok
  Scenario: sample attributes full description
    When I am on "/adminSample/admin"
    Then I should see "Source material identifiers:David Lambert & BGI"
    And I should see "Geographic location (country and/or sea,region):Antarctica, Inexpressible Island, Ross Sea"
    And I should see "Alternative names:PYGAD"

  @ok
  Scenario: display 1 input error message when update
    Given I am on "/adminSample/update/id/432"
    And I should see "lat_lon"
    When I fill in the field of "name" "Sample[attributesList]" with "animal=\"tiger\""
    And I press the button "Save"
    And I wait "1" seconds
    Then I should see "Please fix the following input errors:"
    And I should see "Attribute name animal is not valid - please select a valid attribute name!"

  @ok
  Scenario: display 2 input error messages when update
    Given I am on "/adminSample/update/id/432"
    And I should see "lat_lon"
    When I fill in the field of "name" "Sample[attributesList]" with "animal=\"tiger\",plant=\"rose\""
    And I press the button "Save"
    And I wait "1" seconds
    Then I should see "Please fix the following input errors:"
    And I should see "Attribute name animal is not valid - please select a valid attribute name!"
    And I should see "Attribute name plant is not valid - please select a valid attribute name!"

  @ok
  Scenario: display 1 input error message when create
    Given I am on "/adminSample/create"
    And I should see "Create"
    When I fill in the field of "name" "Sample[species_id]" with "87676:Eucalyptus pauciflora"
    And I fill in the field of "name" "Sample[attributesList]" with "animal=\"tiger\""
    And I press the button "Create"
    And I wait "1" seconds
    Then I should see "Please fix the following input errors:"
    And I should see "Attribute name animal is not valid - please select a valid attribute name!"

  @ok
  Scenario: display 2 input error messages when create
    Given I am on "/adminSample/create"
    And I should see "Create"
    When I fill in the field of "name" "Sample[species_id]" with "87676:Eucalyptus pauciflora"
    And I fill in the field of "name" "Sample[attributesList]" with "animal=\"tiger\",plant=\"rose\""
    And I press the button "Create"
    And I wait "1" seconds
    Then I should see "Please fix the following input errors:"
    And I should see "Attribute name animal is not valid - please select a valid attribute name!"
    And I should see "Attribute name plant is not valid - please select a valid attribute name!"