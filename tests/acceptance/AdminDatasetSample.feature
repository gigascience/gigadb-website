@ok-can-offline
Feature: admin page for samples
  as a curator
  I want to see a table of all d dataset/samples associations
  So that quickly navigate to the sample related data I am interested in

  Background:
    Given I have signed in as admin

  @ok
  Scenario: sample attributes short description an call to action for more
    When I am on "/adminDatasetSample/admin"
    Then I should see "Source material identifiers:David Lambert & BG..."
    And I should not see "Geographic location (country and/or sea,region):Antarctica, Inexpressible Island, Ross Sea"
    And I should not see "Alternative names:PYGAD"
    And I should see "... +"

  @ok
  Scenario: sample attributes show long description when clicking +
    Given I am on "/adminDatasetSample/admin"
    When I follow "+"
    Then I should see "Source material identifiers:David Lambert & BGI"
    And I should see "Geographic location (country and/or sea,region):Antarctica, Inexpressible Island, Ross Sea"
    And I should see "Alternative names:PYGAD"
    And I should not see "... +"

  @ok
  Scenario: Sorting on DOI column in ascending order
    Given I am on "/adminDatasetSample/admin"
    When I follow "DOI"
    And I wait "1" seconds
    Then I should see the table is sorted by column "DOI" in the "asc" order

  @ok
  Scenario: Sorting on DOI column in descending order
    Given I am on "/adminDatasetSample/admin"
    When I follow "DOI"
    And I wait "1" seconds
    And I follow "DOI"
    And I wait "1" seconds
    Then I should see the table is sorted by column "DOI" in the "desc" order