# Created by serhi at 6/4/2019
Feature: Funding tab


  Scenario: I click No button on Funding tab and I click Next button in order to go to Sample tab
    Given I am on "site/login" and I login
    When I go to submission wizard "/datasetSubmission/fundingManagement/id/210" URL
    And I click "no" on Fundings tab
    Then Next button class 'btn btn-green js-save-funding' becomes active
    And I click 'Next' button on Fundings tab
    Then the user is redirected to "Add Samples" page


  Scenario: I add a grant to the table
    Given I am on "site/login" and I login
    When I go to submission wizard "/datasetSubmission/fundingManagement/id/210" URL
    And I click "yes" on Fundings tab
    And I enter a program name "FP7 framework", the unique reference "Grant reference", PI name "Bloggs J" field, and choose a funding body option "2" from dropdown list
    And I click Add Link button on Funding tab
    Then the grant details are added into the table
    And I click 'Save' button on Fundings tab
    And the grant details are saved into DB
    Then I delete the added grant form DB where program name is 'FP7 framework'


  Scenario: delete grant from the table
    Given I am on "site/login" and I login
    When I go to submission wizard "/datasetSubmission/fundingManagement/id/210" URL
    And I click "yes" on Fundings tab
    And I enter a program name "FP7 framework", the unique reference "Grant reference", PI name "Bloggs J" field, and choose a funding body option "2" from dropdown list
    And I click Add Link button on Funding tab
    And I click Delete this row "1" button
    And I click OK button on the alert pop-up
    Then The table No"1" is empty and contains "No results found." on Additional Info tab

  Scenario: I add a grant to the table by clicking NEXT button and I check if it is saved into DB
    Given I am on "site/login" and I login
    When I go to submission wizard "/datasetSubmission/fundingManagement/id/210" URL
    And I click "yes" on Fundings tab
    And I enter a program name "FP7 framework", the unique reference "Grant reference", PI name "Bloggs J" field, and choose a funding body option "3" from dropdown list
    And I click Add Link button on Funding tab
    Then the grant details are added into the table
    And I click 'Next' button on Fundings tab
    And the grant details are saved into DB
    Then I delete the added grant form DB where program name is 'FP7 framework'