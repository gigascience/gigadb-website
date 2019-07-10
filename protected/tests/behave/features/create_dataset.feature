# Created by serhi at 6/3/2019
Feature: Create Dataset

  Scenario: The user navigates to "Create Dataset"
    Given url address "site/login"
    When I enter email address "user@gigadb.org"
    And I enter password "gigadb"
    And I click Login button
    And I click View profile link
    And I click Submit new dataset button
    And I click "Create new dataset online using wizard" button
    Then the user is redirected to "Create Dataset" page

  Scenario: "Submitter" field is auto-filled with username/email
    Given url address "site/login"
    When I enter email address "user@gigadb.org"
    And I enter password "gigadb"
    And I click Login button
    And I click View profile link
    And remember user email on "Your profile page"
    And I click Submit new dataset button
    And I click "Create new dataset online using wizard" button
    Then Then “submitter” name is auto-filled with my username/email

  Scenario: Length warning message appears And more that 100 chars are I entered
    Given url address "site/login"
    When I enter email address "user@gigadb.org"
    And I enter password "gigadb"
    And I click Login button
    And I click View profile link
    And I click Submit new dataset button
    And I click "Create new dataset online using wizard" button
    And I enter Title "more_than_100_chars_more_than_100_chars_more_than_100_chars_more_than_100_chars_more_than_100_chars_1" on Study tab
    And I click out of the field
    Then the length warning message appears "Warning: Your title is over 100 characters long, you should reduce it if possible."

  Scenario: Creating Dataset
    Given url address "site/login"
    When I enter email address "user@gigadb.org"
    And I enter password "gigadb"
    And I click Login button
    And I click View profile link
    And I click Submit new dataset button
    And I click "Create new dataset online using wizard" button
    And I enter GigaScience manuscript "GIGA-D-18-00123"
    And select a Type No "1" on Study tab
    And mark "If you are unable to provide a suitable image to help..." check-box on Study tab
    And I enter Title "Dataset_title" on Study tab
    And I enter Description "test description" on Study tab
    And mark "I have read Terms and Conditions" check-box on Study tab
    And I click Save button on Study tab
    Then "Next" button appears
    And A new dataset is created in DB table dataset
    And I click Next button on Study tab
    Then the user is redirected to "Add Authors" page


  Scenario: Validation "Type" and "Title" required fields
    Given url address "site/login"
    When I enter email address "user@gigadb.org"
    And I enter password "gigadb"
    And I click Login button
    And I click View profile link
    And I click Submit new dataset button
    And I click "Create new dataset online using wizard" button
    And mark "If you are unable to provide a suitable image to help..." check-box on Study tab
    And mark "I have read Terms and Conditions" check-box on Study tab
    And I click Save button on Study tab
    Then "Types cannot be blank." error message appears
    Then "Title cannot be blank." error message appears

  Scenario: Image required fields validation
    Given url address "site/login"
    When I enter email address "user@gigadb.org"
    And I enter password "gigadb"
    And I click Login button
    And I click View profile link
    And I click Submit new dataset button
    And I click "Create new dataset online using wizard" button
    And select a Type No "1" on Study tab
    And I enter Title "Dataset_title" on Study tab
    And I enter Description "test description" on Study tab
    And Choose image file '1200per800' to upload on Study tab
    And mark "I have read Terms and Conditions" check-box on Study tab
    And I click Save button on Study tab
    Then "Image License cannot be blank." error message appears
    Then "Image Credit cannot be blank." error message appears
    Then "Image Source cannot be blank." error message appears

  Scenario: Image upload
    Given I am on "site/login" and I login as "user@gigadb.org" with password "gigadb"
    When I go to submission wizard "datasetSubmission/datasetManagement/id/210" URL
    And Choose image file '1200per800' to upload on Study tab
    And I enter Image Title "Test_image_title" on Study tab
    And choose Image License "Public Domain" drop-down list on Study tab
    And I enter Image Credit "mam" on Study tab
    And I enter Image Source "wiki" on Study tab
    And I click Save button on Study tab

