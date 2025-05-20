@issue-1528
Feature: A curator opens the mockup page
  As a curator
  I want to see my changes appear in the pre-publication view of the dataset page
  So that I can confirm the changes are correct and show the relevant users the private mockup page displaying the correct information

  Background:
    Given I have signed in as admin
    And I am on "/dataset/200070/token/ImP3Bbu7ytRSfYFh"

  @ok
  Scenario: Dataset metadata changes
    Given I make an update to the non-public dataset "200070"'s "dataset metadata" in the admin pages
    When I am on "/adminDataset/update/id/668"
    And I follow "Open Private URL"
    Then I am on "/dataset/200070/token/ImP3Bbu7ytRSfYFh"
    And I can see the changes to the "dataset metadata" displayed

  @ok
  Scenario: Sample metadata changes
    Given sample "154" is associated with dataset "2000070"
    And I am on "/dataset/200070/token/ImP3Bbu7ytRSfYFh"
    And I make an update to the non-public dataset "200070"'s "sample metadata" in the admin pages
    When I am on "/adminDataset/update/id/668"
    And I follow "Open Private URL"
    Then I am on "/dataset/200070/token/ImP3Bbu7ytRSfYFh"
    And I can see the changes to the "sample metadata" displayed

  @ok
  Scenario: File metadata changes
    Given file "95366" is associated with dataset "2000070"
    And I am on "/dataset/200070/token/ImP3Bbu7ytRSfYFh"
    And I make an update to the non-public dataset "200070"'s "file metadata" in the admin pages
    When I am on "/adminDataset/update/id/668"
    And I follow "Open Private URL"
    Then I am on "/dataset/200070/token/ImP3Bbu7ytRSfYFh"
    And I can see the changes to the "file metadata" displayed

  @ok
  Scenario: Author metadata changes
    Given I make an update to the non-public dataset "200070"'s "author metadata" in the admin pages
    When I am on "/adminDataset/update/id/668"
    And I follow "Open Private URL"
    Then I am on "/dataset/200070/token/ImP3Bbu7ytRSfYFh"
    And I can see the changes to the "author metadata" displayed

  @ok @release-year
  Scenario: Check for updating the release year
    Given I am on "/dataset/200070/token/ImP3Bbu7ytRSfYFh"
    When I make an update to the non-public dataset "200070"'s "author metadata" in the admin pages
    And I am on "/adminDataset/update/id/668"
    And I fill in the field of "id" "Dataset_publication_date" with "2020-01-01"
    And I press the button "Save"
    Then I am on "/dataset/200070/token/ImP3Bbu7ytRSfYFh"
    And I should see "Zhang G (2020)"

    @ok
  Scenario: Create Mockup access
    Given I am on "/adminDataset/update/id/5"
    And I select "Submitted" from the field "Dataset_upload_status"
    And I press the button "Save"
    And I should see "Updated successfully!"
    And I should see "Generate mockup for reviewers"
    And I press the button "Generate mockup for reviewers"
    And I wait "3" seconds
    And I should see "Generate unique and time-limited mockup url for reviewers"
    And I fill in the field of "id" "reviewerEmail" with "test@test.fr"
    And I press the button "Generate mockup"
    And I wait "3" seconds
    Then I should see "Unique (test@test.fr), time-limited (1 months) mockup url ready"
    Given I am on "/adminDataset/update/id/5"
    Then I should see "Mockup url created for test@test.fr for 1 months"

    @ok
  Scenario: No Mockup Button If Wrong Upload Status
    Given I am on "/adminDataset/update/id/5"
    And I select "Rejected" from the field "Dataset_upload_status"
    And I press the button "Save"
    And I should see "Updated successfully!"
    And I am on "/adminDataset/update/id/5"
    And I should not see "Generate mockup for reviewers"

  @ok
  Scenario: No Mockup Button If Published Status
    Given I am on "/adminDataset/update/id/8"
    And I should not see "Generate mockup for reviewers"





