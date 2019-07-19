# Created by serhi at 6/3/2019
Feature: Upload your dataset metadata from a spreadsheet page

 Scenario: The user uploads dataset metadata from a spreadsheet
    Given url address "site/login"
    When I enter email address "local-gigadb-admin@rijam.ml1.net"
    And I enter password "gigadb"
    And I click Login button
    And I click View profile link
    And I click Submit new dataset button
    And I click "Upload new dataset from spreadsheet" button
    And mark "I have read Terms and Conditions" check-box on Study tab
    And Choose file from file system on 'Upload your dataset metadata from a spreadsheet' page
    And I click "Upload New Dataset"
#    Then send email to database@gigasciencejournal.com with file as attachment NB- subject and body of email are already defined, check should be in place to ensure they are not empty.


#  Scenario: The user downloads template spreadsheet (Excel)
#    Given url address "site/login"
#    When I enter email address "local-gigadb-admin@rijam.ml1.net"
#    And I enter password "gigadb"
#    And I click Login button
#    And I click View profile link
#    And I click Submit new dataset button
#    And I click "Upload new dataset from spreadsheet" button
#    And I click "Download template spreadsheet" (Excel) button
#    Then the file is downloaded (not finished yet)

#  Scenario: The user downloads template spreadsheet (Open Office)
#    Given url address "site/login"
#    When I enter email address "local-gigadb-admin@rijam.ml1.net"
#    And I enter password "gigadb"
#    And I click Login button
#    And I click View profile link
#    And I click Submit new dataset button
#    And I click "Upload new dataset from spreadsheet" button
#    And I click "Download template spreadsheet (Open Office)" button
#    Then the file is downloaded (not finished yet)

#  Scenario: The user downloads Example 1 (Excel)
#    Given url address "site/login"
#    When I enter email address "local-gigadb-admin@rijam.ml1.net"
#    And I enter password "gigadb"
#    And I click Login button
#    And I click View profile link
#    And I click Submit new dataset button
#    And I click "Upload new dataset from spreadsheet" button
#    And I click "Download Example 1 (Excel)" button
#    Then the file is downloaded (not finished yet)

#  Scenario: The user downloads Example 1 (Open Office)
#    Given url address "site/login"
#    When I enter email address "local-gigadb-admin@rijam.ml1.net"
#    And I enter password "gigadb"
#    And I click Login button
#    And I click View profile link
#    And I click Submit new dataset button
#    And I click "Upload new dataset from spreadsheet" button
#    And I click "Download Example 1 (Open Office)" button

#    Then the file is downloaded (not finished yet)