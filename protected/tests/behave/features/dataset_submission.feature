
Feature: Dataset submission page

  Scenario: The user navigates to "Dataset submission selection" page
    Given I am on "site/login" and I login
    When I click View profile link
    And I click Submit new dataset button
    Then the user is redirected to "Dataset submission" page


  Scenario: The user navigates to "Upload your dataset metadata from a spreadsheet" page
    Given I am on "site/login" and I login
    When I click View profile link
    And I click Submit new dataset button
    And I click "Upload new dataset from spreadsheet" button
    Then the user is redirected to "Upload your dataset metadata from a spreadsheet" page








