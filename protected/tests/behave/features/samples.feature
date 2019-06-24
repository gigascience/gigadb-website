# Created by serhi at 6/6/2019
Feature: Samples tab


  Scenario: Warn the user that all data in table will be over-written when applying a template on Sample table with data
    Given I am on "site/login" and I login as "user@gigadb.org" with password "gigadb"
    When I go to submission wizard "/datasetSubmission/sampleManagement/id/397" URL
    And I add some data into Sample table
    And I select a template №"2"
    And I click 'Apply' button
    Then A pop-up message appears "Please note that all data in table will be overwritten!"
#    it is blocked by a bug

  Scenario: if Sample table is empty the selected template is applied without an error message
    Given I am on "site/login" and I login as "user@gigadb.org" with password "gigadb"
    When I go to submission wizard "/datasetSubmission/sampleManagement/id/397" URL
    And I select a template №"2"
    And I click 'Apply' button
    Then The appropriate template and display new sample table with those columns defined in the template chosen

  Scenario: A valid “metadata file” is uploaded
    Given I am on "site/login" and I login as "user@gigadb.org" with password "gigadb"
    When I go to submission wizard "/datasetSubmission/sampleManagement/id/397" URL
    And I choose a valid matadata file to upload on Sample tab
    And I click Upload button on Sample tab
    Then the metadata is used to populate the sample table

  Scenario: A valid “metadata file” is uploaded
    Given I am on "site/login" and I login as "user@gigadb.org" with password "gigadb"
    When I go to submission wizard "/datasetSubmission/end/id/397" URL
    And I click "Return to your profile page" on Fundings tab
    Then the user is redirected to Your profile page page
#    And recently submitted dataset is highlighted in table




    


