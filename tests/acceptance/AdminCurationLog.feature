@ok-can-offline
Feature: Manage curation log
  As an curator
  I want a form to manage curation log
  So that the curation information is up-to-date

  Background:
    Given I have signed in as admin

  @ok
  Scenario: curation log form loading with all necessary fields
    Given I am on "curationlog/admin"
    Then I should see "Manage Curation Log"
    And I should see "Status changed to Published"
    And I should see "Status changed to Uploaded"
    And I should see "Status changed to Request"

  @ok
  Scenario: view curation record
    When I am on "curationlog/admin"
    And I should see a curation log action "View" is linked to "http://gigadb.test/curationLog/view/id/3"
    And I click on curation log action "View"
    Then I am on "/curationLog/view/id/3"
    And I should see "View Curation Log #3"
    And I should see a link "Manage" to "http://gigadb.test/curationLog/admin"
    And I should see a link "Admin" to "http://gigadb.test/site/admin"
    And I should see a link "Back to this Dataset Curation Log" to "http://gigadb.test/adminDataset/update/id/22"

  @ok
  Scenario: update curation record
    When I am on "curationlog/admin"
    And  I should see a curation log action "Update" is linked to "http://gigadb.test/curationLog/update/id/3"
    And I click on curation log action "Update"
    Then I am on "/curationLog/update/id/3"
    And I should see "Update Curation Log 3"
    And I should see a link "Manage" to "http://gigadb.test/curationLog/admin"
    And I should see a link "Admin" to "http://gigadb.test/site/admin"
    And I fill in the field of "name" "CurationLog[comments]" with "cogito, ergo sum"
    And I press the button "Save"
    And I wait "2" seconds
    Then I am on "/curationLog/view/id/3"
    And I should see "View Curation Log #3"
    And I should see "cogito, ergo sum"

  @ok
  Scenario: delete curation record
    When I am on "curationlog/admin"
    And I should see "Status changed to Published"
    And I should see a curation log action "Delete" is linked to "http://gigadb.test/curationLog/delete/id/3"
    And I click on curation log action "Delete"
    And I confirm to "Are you sure you want to delete this item?"
    And I wait "2" seconds
    Then I am on "/adminDataset/update/id/22"
    And I should not see "Status changed to Published"