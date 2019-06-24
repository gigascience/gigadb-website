# Created by serhi at 6/21/2019
Feature: File tab
  # Enter feature description here

  Scenario: the user is redirected to File tab when clicking Update button for a dataset that has status is UserUploadingData
    Given I am on "site/login" and I login as "user@gigadb.org" with password "gigadb"
    When I go to submission wizard "/user/view_profile#submitted" URL
    And a dataset with status “UserUploadingData” is included in my user account
    And I click Update button on dataset id "393"
    Then the user is redirected to "File details" page


  Scenario: the user gets file names from FTP
    Given I am on "site/login" and I login as "user@gigadb.org" with password "gigadb"
    When I go to submission wizard "/adminFile/create1/id/210" URL
    And I have a valid value in FTP username “user99“
    And I have a valid value in FTP password “WhiteLabel”
    When I click “Get File Names” button
    Then retrieve the list of files names & sizes from the FTP server ftp://user99@parrot.genomics.cn
    And parse file list into table using rules for file extensions
