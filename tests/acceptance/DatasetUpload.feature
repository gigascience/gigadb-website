Feature: Dataset upload
  As a gigadb user,
  I want to upload dataset metadata from a spreadsheet
  So that I can provide information about my dataset to GigaDB

@ok
Scenario: Allow users to upload dataset spreadsheet
  Given I sign in as a user
  When I am on "/datasetSubmission/upload"
  Then I should see "Dataset Upload"
  And I should see a check-box field "agree-checkbox"
  And I should see "Excel File"
  And I should see a submit button "Upload New Dataset"

#@wip
#Scenario: Upload dataset metadata with an Excel spreadsheet
#  Given I sign in as a user
#  When I go to "/datasetSubmission/upload"
#  And I check "agree-checkbox"
#  And I attach the file "spreadsheet.xls" to "xls"
#  And I press "Upload New Dataset"
#  And I wait "5" seconds
#  Then I should see "Your GigaDB submission has been received and is currently under review."
#  And I should see a button input "Back to upload new dataset"
##    Then send email to database@gigasciencejournal.com with file as attachment NB- subject and body of email are already defined, check should be in place to ensure they are not empty.
