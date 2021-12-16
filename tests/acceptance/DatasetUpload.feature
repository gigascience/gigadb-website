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

@ok
Scenario: Upload dataset metadata with an Excel spreadsheet
  Given I sign in as a user
  When I am on "/datasetSubmission/upload"
  And I check the field "agree-checkbox"
  And I attach the file "spreadsheet.xls" to the file input element "xls"
  And I press the button "Upload New Dataset"
  Then I should see "Your GigaDB submission has been received and is currently under review."
  And I should see a link "Back to upload new dataset" to "/datasetSubmission/upload"
