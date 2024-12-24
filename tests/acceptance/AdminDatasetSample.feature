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

  @this
  Scenario: Select multiple samples on create form
    Given I have signed in as admin
    And I am on "/adminDatasetSample/create"
    When I select "100142" from the field "DatasetSample_dataset_id"
    And I multiselect "A. vittata, Pygoscelis_adeliae" from the field "DatasetSample_sample_id"
    And I press the button "Create"
    And I wait "1" seconds
    And I fill in the field of "name" "DatasetSample[doi_search]" with "100142"
    And I press return on the element "(//input)[2]"
    And I wait "1" seconds
    Then I should see "A. vittata"
    And I should see "Pygoscelis_adeliae"
