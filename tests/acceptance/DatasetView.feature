Feature: a user visit the dataset page
  As a website user
  I want to see all the information pertaining to a dataset
  So that I can use it to further my research or education

  @ok
  Scenario: number of files in current page and total number of files are displayed
    Given I have not signed in
    When I am on "dataset/100142"
    Then I should see "Displaying 4 files of 4"

  @ok
  Scenario: pagination widget is not shown when total number of file is less or equal to page size setting
    Given I have not signed in
    When I am on "dataset/100142"
    Then I should not see "Go to page"
    And I should not see "of 1"

  @ok
  Scenario: pagination widget is shown when total number of file greater than the page size setting
    Given I have not signed in
    And I have set the page size setting to 5
    When I am on "/dataset/100006"
    And I follow "Files"
    Then I should see "Next >"
    Then I should see "Go to page"