# Created by serhi at 6/21/2019
Feature: File tab
  # Enter feature description here

  Scenario: the user is redirected to File tab when clicking Update button for a dataset that has status is UserUploadingData
    Given I am on "site/login" and I login as "user@gigadb.org" with password "gigadb"
    When I go to submission wizard "/user/view_profile#submitted" URL
    And a dataset with status “UserUploadingData” is included in my user account
    And I click Update button on dataset id "393"
    Then the user is redirected to "File details" page
#    need an id with stutus UserUploadingData on DEV


  Scenario: the user gets file names from FTP, adds the description saves files into DB by clicking Save button
    Given I am on "site/login" and I login as "user@gigadb.org" with password "gigadb"
    When I go to submission wizard "/adminFile/create1/id/210" URL
    And I have a valid value in FTP username “user99“
    And I have a valid value in FTP password “WhiteLabel”
    When I click “Get File Names” button
    Then retrieve the list of files names & sizes from the FTP server ftp://user99@parrot.genomics.cn
    And parse file list into table using rules for file extensions
    When I add description to the files loaded from ftp
    When I click Save button on Files tab
    Then file details are saved to database where dataset_id is '210'
