Feature: Dataset upload
  As a gigadb user,
  I want to upload dataset metadata from a spreadsheet
  So that I can provide information about my dataset to GigaDB

Background:
  Given Gigadb web site is loaded with "gigadb_testdata.pgdmp" data
  And user "joy_fox" is loaded

@ok
Scenario: Allow users to upload dataset spreadsheet
  Given I sign in as a user
  When I go to "/datasetSubmission/upload"
  Then I should see "Dataset Upload"
  And I should see a form element labelled "agree-checkbox"
  And I should see a form element labelled "xls"
  And I should see a button input "Upload New Dataset"
    
@wip
Scenario: Upload dataset metadata with an Excel spreadsheet
  Given I sign in as a user
  When I go to "/datasetSubmission/upload"
  And I check "agree-checkbox"
  And I attach the file "spreadsheet.xls" to "xls"
  And I press "Upload New Dataset"
  And I wait "5" seconds
  Then I should see "Your GigaDB submission has been received and is currently under review."
  And I should see a button input "Back to upload new dataset"
#    Then send email to database@gigasciencejournal.com with file as attachment NB- subject and body of email are already defined, check should be in place to ensure they are not empty.
