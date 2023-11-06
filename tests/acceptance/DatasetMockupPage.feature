@issue-1528 @wip
Feature: A curator opens the mockup page
  As a curator
  I want to see my changes appear in the pre-publication view of the dataset page
  So that I can confirm the changes are correct and show the relevant users the private mockup page displaying the correct information

  Background:
    Given I have signed in as admin
    And I am on "/dataset/200070/token/ImP3Bbu7ytRSfYFh"

  Scenario: Dataset metadata changes
    Given I make an update to the non-public dataset "200070"'s "dataset metadata" in the admin pages
    When I am on "/adminDataset/update/id/668"
    And I follow "Open Private URL"
    Then I am on "/dataset/200070/token/ImP3Bbu7ytRSfYFh"
    And I can see the changes to the "dataset metadata" displayed

  Scenario: Sample metadata changes
    Given I make an update to the non-public dataset "200070"'s "sample metadata" in the admin pages
    When I am on "/adminDataset/update/id/668"
    And I follow "Open Private URL"
    Then I am on "/dataset/200070/token/ImP3Bbu7ytRSfYFh"
    And I can see the changes to the "sample metadata" displayed

  Scenario: File metadata changes
    Given I make an update to the non-public dataset "200070"'s "file metadata" in the admin pages
    When I am on "/adminDataset/update/id/668"
    And I follow "Open Private URL"
    Then I am on "/dataset/200070/token/ImP3Bbu7ytRSfYFh"
    And I can see the changes to the "file metadata" displayed

  Scenario: Author metadata changes
    Given I make an update to the non-public dataset "200070"'s "author metadata" in the admin pages
    When I am on "/adminDataset/update/id/668"
    And I follow "Open Private URL"
    Then I am on "/dataset/200070/token/ImP3Bbu7ytRSfYFh"
    And I can see the changes to the "author metadata" displayed