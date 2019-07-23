# Created by serhi at 6/3/2019
Feature: Add Authors

  Scenario: check if a correct ORCiD is added
    Given I am on "site/login" and I login
    When I go to submission wizard "/datasetSubmission/authorManagement/id/322" URL
    And I enter First Name "QA"
    And I enter Middle Name "test middle name"
    And I enter Last Name "Engineer"
    And I enter ORCiD code "4444444444444444"
    And I enter CrediT "con"
    And select CreadiT form the autocomplete list "Conceptualization"
    And I click Add Author button
    And I click "Save" button on Author tab
    Then ORCiD format is nnnn-nnnn-nnnn-nnnn
    And Author is added to DB author table
    When I delete the added author from DB



  Scenario: add an Author via CSV file
    Given I am on "site/login" and I login
    When I go to submission wizard "/datasetSubmission/authorManagement/id/322" URL
    And Choose CSV or TSV 'csv' file from file system on Author tab
    And I click Add Authors button
    Then The authors from the file "csv" are added accordingly
    And I click "Save" button on Author tab
    And Author is added to DB author table from the file
    Then I delete the added author from DB

  Scenario: add an Author via CSV file and save it to DB by clicking Next buton
    Given I am on "site/login" and I login
    When I go to submission wizard "/datasetSubmission/authorManagement/id/322" URL
    And Choose CSV or TSV 'csv' file from file system on Author tab
    And I click Add Authors button
    Then The authors from the file "csv" are added accordingly
    And I click "Next" button on Author tab
    And Author is added to DB author table from the file
    Then I delete the added author from file in DB

