Feature: An author can download file template
  As an author
  I want to download the dataset metadata upload form template after login
  So that I can upload information about my dataset using the form

  @ok
  Scenario: Go to Download template file page as an author and each download button linked with correct file path
    Given I sign in as a user
    When I am on "/datasetSubmission/upload"
    Then I should see a link "Download Template File" to "/files/templates/GigaDBUploadForm-forWebsite-v22Dec2021.xlsx"
    And I should see a link "Download Example File 1" to "/files/templates/GigaDBUpload-Example1-forWebsite-v22Dec2021.xlsx"
    And I should see a link "Download Example File 2" to "/files/templates/GigaDBUpload-Example2-forWebsite-v22Dec2021.xlsx"

  @ok
  Scenario: Go to help page as a website user and the Excel template file is linked with correct file path
    Given I am on "site/logout"
    When I am on "/site/help#guidelines"
    Then I should see a link "Excel template file" to "/files/templates/GigaDBUploadForm-forWebsite-v22Dec2021.xlsx"