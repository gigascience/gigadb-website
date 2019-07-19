# Created by serhi at 6/4/2019
Feature: Funding tab
  # I enter feature description here

  Scenario: I click No button on Funding tab and I click Next button in order to go to Sample tab
#    Given url address "site/login"
#    When I enter email address "local-gigadb-admin@rijam.ml1.net"
#    And I enter password "gigadb"
#    And I click Login button
#    And I click View profile link
#    And I click Submit new dataset button
#    And I click "Create new dataset online using wizard" button
#    And select a Type No "1" on Study tab
#    And mark "If you are unable to provide a suitable image to help..." check-box on Study tab
#    And I enter Title "Dataset_title" on Study tab
#    And I enter Description "test description" on Study tab
#    And mark "I have read Terms and Conditions" check-box on Study tab
#    And I click Save button on Study tab
#    And I click Next button on Study tab
#    And I click Next button on Author tab
#    And I click No button for Public data archive links
#    And I click 'no' button for Related GigaDB Datasets
#    And I click 'no' button for Project links
#    And I click "no" button for "A published manuscript that uses this data"
#    And I click "no" button for "Protocols.io link to methods used to generate this data"
#    And I click "no" button for "SketchFab 3d-Image viewer links"
#    And I click "no" button for "Actionable code in CodeOceans"
#    And I click "no" button for "or any other URL to a stable source of data and files directly related to this dataset"
#    And I click Next button on Additional Information tab
    Given I am on "site/login" and I login as "local-gigadb-admin@rijam.ml1.net" with password "gigadb"
    When I go to submission wizard "/datasetSubmission/fundingManagement/id/210" URL
    And I click "no" on Fundings tab
    Then Next button class 'btn btn-green js-save-funding' becomes active
    And I click 'Next' button on Fundings tab
    Then the user is redirected to "Add Samples" page


  Scenario: I add a grant to the table
    Given I am on "site/login" and I login as "local-gigadb-admin@rijam.ml1.net" with password "gigadb"
    When I go to submission wizard "/datasetSubmission/fundingManagement/id/210" URL
    And I click "yes" on Fundings tab
    And I enter a program name "FP7 framework", the unique reference "Grant reference", PI name "Bloggs J" field, and choose a funding body option "2" from dropdown list
    And I click Add Link button on Funding tab
    Then the grant details are added into the table
    And I click 'Save' button on Fundings tab
    And the grant details are saved into DB
    Then I delete the added grant form DB where program name is 'FP7 framework'


  Scenario: delete grant from the table
    Given I am on "site/login" and I login as "local-gigadb-admin@rijam.ml1.net" with password "gigadb"
    When I go to submission wizard "/datasetSubmission/fundingManagement/id/210" URL
    And I click "yes" on Fundings tab
    And I enter a program name "FP7 framework", the unique reference "Grant reference", PI name "Bloggs J" field, and choose a funding body option "2" from dropdown list
    And I click Add Link button on Funding tab
    And I click Delete this row "1" button
    And I click OK button on the alert pop-up
    Then The table No"1" is empty and contains "No results found." on Additional Info tab

  Scenario: I add a grant to the table by clicking NEXT button and I check if it is saved into DB
    Given I am on "site/login" and I login as "local-gigadb-admin@rijam.ml1.net" with password "gigadb"
    When I go to submission wizard "/datasetSubmission/fundingManagement/id/210" URL
    And I click "yes" on Fundings tab
    And I enter a program name "FP7 framework", the unique reference "Grant reference", PI name "Bloggs J" field, and choose a funding body option "3" from dropdown list
    And I click Add Link button on Funding tab
    Then the grant details are added into the table
    And I click 'Next' button on Fundings tab
    And the grant details are saved into DB
    Then I delete the added grant form DB where program name is 'FP7 framework'