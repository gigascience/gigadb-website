# Created by serhi at 6/6/2019
Feature: Samples tab

  Scenario: any rows in the sample table are saved to the database “dataset status” is set to “Assigning FTP dropbox and an email is sent
    Given I am on "site/login" and I login
    When I go to submission wizard "datasetSubmission/sampleManagement/id/210" URL
    And I update dataset status to "Incomplete" where id is "210"
    And I add a row and enter Sample ID "Sample ID", Species name "Adelie penguin" and "Description"
    And I click on "Next" button on Sample tab
    Then the user is redirected to The end page
    Then any rows in the sample table are saved to the database
    When I click "Return to your profile page" button on Sample tab
    Then dataset status is changed to "AssigningFTPbox" where dataset id is "210"
#    And email is sent to database@gigasciencejournal.com to alert us to a new submission
    And I delete the added sample form DB

# need to add templates
  Scenario: Warn the user that all data in table will be over-written when applying a template on Sample table with data
    Given I am on "site/login" and I login
    When I go to submission wizard "/datasetSubmission/sampleManagement/id/210" URL
    And I add some data into Sample table
    And I select a template №"2"
    And I click 'Apply' button
    Then A pop-up message appears "Please note that all data in table will be overwritten! Are you sure?"


  Scenario: if Sample table is empty the selected template is applied without an error message
    Given I am on "site/login" and I login
    When I go to submission wizard "/datasetSubmission/sampleManagement/id/210" URL
    And I select a template №"2"
    And I click 'Apply' button
    Then The appropriate template and display new sample table with those columns defined in the template chosen

  Scenario: A valid “metadata file” is uploaded
    Given I am on "site/login" and I login
    When I go to submission wizard "/datasetSubmission/sampleManagement/id/210" URL
    And I choose a valid matadata file to upload on Sample tab
    And I click Upload button on Sample tab
    Then the metadata is used to populate the sample table
    And I click on "Save" button on Sample tab
    And I delete the added sample form DB where Sample name is "SRS004381"


  Scenario: recently submitted dataset is highlighted in table
    Given I am on "site/login" and I login
    When I go to submission wizard "/datasetSubmission/end/id/210" URL
    And I click "Return to your profile page" button on Sample tab
    Then the user is redirected to Your profile page page
    And recently submitted dataset is highlighted in table


  Scenario: when Save button clicked the dated is saved to DB on Sample tab
    Given I am on "site/login" and I login
    When I go to submission wizard "datasetSubmission/sampleManagement/id/210" URL
    And I add a row and enter Sample ID "Sample ID", Species name "Adelie penguin" and "Description"
    And I click on "Save" button on Sample tab
    Then any rows in the sample table are saved to the database
    And I delete the added sample form DB

  Scenario:  when adding the same Sample ID twice the error pop-up appears
    Given I am on "site/login" and I login
    When I go to submission wizard "datasetSubmission/sampleManagement/id/210" URL
    And I add a row and enter Sample ID "Sample ID", Species name "Adelie penguin" and "Description"
    And I add a second row and enter Sample ID "Sample ID", Species name "Adelie penguin" and "Description"
    And I click on "Save" button on Sample tab
    Then A pop-up message appears "Row 2: Sample ID already exist."

  Scenario:  the user adds an invalid species name and gets notified
    Given I am on "site/login" and I login
    When I go to submission wizard "datasetSubmission/sampleManagement/id/210" URL
    And I add a row and enter Sample ID "Sample ID", Species name "Adelie penguin1" and "Description"
    And I click on "Save" button on Sample tab
    Then A pop-up message appears "Row 1: Species Name is invalid."





    


