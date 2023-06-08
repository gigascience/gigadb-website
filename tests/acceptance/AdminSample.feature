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
    And I should see "Attribute name for the input animal=\tiger\ is not valid - please select a valid attribute name!"

  @ok
  Scenario: display 2 input error messages when update
    Given I am on "/adminSample/update/id/432"
    And I should see "lat_lon"
    When I fill in the field of "name" "Sample[attributesList]" with "animal=\"tiger\",plant=\"rose\""
    And I press the button "Save"
    And I wait "1" seconds
    Then I should see "Please fix the following input errors:"
    And I should see "Attribute name for the input animal=\tiger\ is not valid - please select a valid attribute name!"
    And I should see "Attribute name for the input plant=\rose\ is not valid - please select a valid attribute name!"

  @ok
  Scenario: display error message for not found species name when create
    Given I am on "/adminSample/create"
    And I should see "Create"
    When I fill in the field of "name" "Sample[species_id]" with ""
    And I press the button "Create"
    And I wait "1" seconds
    Then I should see "Please fix the following input errors:"
    And I should see "Species name is empty!"

  @ok
  Scenario: display error message for non exist species name when create
    Given I am on "/adminSample/create"
    And I should see "Create"
    When I fill in the field of "name" "Sample[species_id]" with "Human"
    And I press the button "Create"
    And I wait "1" seconds
    Then I should see "Please fix the following input errors:"
    And I should see "Species name Human is not found!"

  @ok
  Scenario: display error message for non exist species id when create
    Given I am on "/adminSample/create"
    And I should see "Create"
    When I fill in the field of "name" "Sample[species_id]" with "789123"
    And I press the button "Create"
    And I wait "1" seconds
    Then I should see "Please fix the following input errors:"
    And I should see "Species id 789123 is not found!"

  @ok
  Scenario: display 1 input error message when create
    Given I am on "/adminSample/create"
    And I should see "Create"
    When I fill in the field of "name" "Sample[species_id]" with "87676:Eucalyptus pauciflora"
    And I fill in the field of "name" "Sample[attributesList]" with "animal=\"tiger\""
    And I press the button "Create"
    And I wait "1" seconds
    Then I should see "Please fix the following input errors:"
    And I should see "Attribute name for the input animal=\tiger\ is not valid - please select a valid attribute name!"

  @ok
  Scenario: display 2 input error messages when create
    Given I am on "/adminSample/create"
    And I should see "Create"
    When I fill in the field of "name" "Sample[species_id]" with "87676:Eucalyptus pauciflora"
    And I fill in the field of "name" "Sample[attributesList]" with "animal=\"tiger\",plant=\"rose\""
    And I press the button "Create"
    And I wait "1" seconds
    Then I should see "Please fix the following input errors:"
    And I should see "Attribute name for the input animal=\tiger\ is not valid - please select a valid attribute name!"
    And I should see "Attribute name for the input plant=\rose\ is not valid - please select a valid attribute name!"

  @ok
  Scenario: display error message for non exist species with attributes when create
    Given I am on "/adminSample/create"
    And I should see "Create"
    When I fill in the field of "name" "Sample[species_id]" with "789123:Human"
    And I fill in the field of "name" "Sample[attributesList]" with "animal=\"tiger\""
    And I press the button "Create"
    And I wait "1" seconds
    Then I should see "Please fix the following input errors:"
    And I should see "Species id 789123 is not found!"
    And I should see "Attribute name for the input animal=\tiger\ is not valid - please select a valid attribute name!"