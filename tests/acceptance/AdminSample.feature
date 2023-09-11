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