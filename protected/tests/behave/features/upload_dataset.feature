# Created by serhi at 6/3/2019
Feature: Upload your dataset metadata from a spreadsheet page

 Scenario: The user uploads dataset metadata from a spreadsheet
    Given I am on "site/login" and I login
    When I click View profile link
    And I click Submit new dataset button
    And I click "Upload new dataset from spreadsheet" button
    And mark "I have read Terms and Conditions" check-box on Study tab
    And Choose file from file system on 'Upload your dataset metadata from a spreadsheet' page
    And I click "Upload New Dataset"
#    Then send email to database@gigasciencejournal.com with file as attachment NB- subject and body of email are already defined, check should be in place to ensure they are not empty.

