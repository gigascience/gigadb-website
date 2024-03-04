@ok-needs-secrets @author-dataset-spreadsheet-upload
Feature: Dataset spreadsheet upload
  As an author
  I want to upload dataset metadata from a spreadsheet
  So that I can provide information about my dataset to GigaDB

@ok
Scenario: Display all the fields for uploading dataset spreadsheet
  Given I sign in as a user
  When I am on "/datasetSubmission/upload"
  Then I should see "Dataset Upload"
  And I should see a check-box field "agree-checkbox"
  And I should see "You must agree to the terms and conditions before continuing"
  And I should see "Excel File"
  And I should see a disabled file input for "xls"
  And I should see a disabled submit button "Upload New Dataset"

@ok
Scenario: Upload dataset metadata with an Excel spreadsheet
  Given I sign in as a user
  When I am on "/datasetSubmission/upload"
  And I check the field "agree-checkbox"
  And I should not see "You must agree to the terms and conditions before continuing"
  And I attach the file "spreadsheet.xls" to the file input element "xls"
  And I press the button "Upload New Dataset"
  Then I should see "Your GigaDB submission has been received and is currently under review."
  And I should see a link "Back to upload new dataset" to "/datasetSubmission/upload"
