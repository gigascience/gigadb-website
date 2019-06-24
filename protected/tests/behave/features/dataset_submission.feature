
Feature: Dataset submission page

  Scenario: The user navigates to "Dataset submission selection" page
    Given url address "site/login"
    When I enter email address "user@gigadb.org"
    And I enter password "gigadb"
    And I click Login button
    And I click View profile link
    And I click Submit new dataset button
    Then the user is redirected to "Dataset submission" page


  Scenario: The user navigates to "Upload your dataset metadata from a spreadsheet" page
    Given url address "site/login"
    When I enter email address "user@gigadb.org"
    And I enter password "gigadb"
    And I click Login button
    And I click View profile link
    And I click Submit new dataset button
    And I click "Upload new dataset from spreadsheet" button
    Then the user is redirected to "Upload your dataset metadata from a spreadsheet" page








