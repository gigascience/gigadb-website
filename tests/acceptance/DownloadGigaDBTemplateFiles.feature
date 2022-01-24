Feature: An author can download file template
  As an author
  I want to download the dataset metadata upload form template after login
  So that I can upload information about my dataset using the form

  Background: Sign in as a user
    Given I sign in as a user
    Then I should see "John's GigaDB Page"

  @ok
  Scenario: Go to Download template file page as a user and each download button linked with correct file path
    When I am on "/datasetSubmission/upload"
    Then I should see a link "Download Template File" to "/files/templates/GigaDBUploadForm-forWebsite-v22Dec2021.xlsx"
    And I should see a link "Download Example File 1" to "/files/templates/GigaDBUpload-Example1-forWebsite-v22Dec2021.xlsx"
    And I should see a link "Download Example File 2" to "/files/templates/GigaDBUpload-Example2-forWebsite-v22Dec2021.xlsx"

  @ok
  Scenario: Download template file as a user
    When I am on "/datasetSubmission/upload"
    Then I press "Download Template File" and the response should contain "200"

  @ok
  Scenario: Download example file 1 as a user
    When I am on "/datasetSubmission/upload"
    Then I press "Download Example File 1" and the response should contain "200"

  @ok
  Scenario: Download example file 2  as a user
    When I am on "/datasetSubmission/upload"
    Then I press "Download Example File 1" and the response should contain "200"

  @ok
  Scenario: Download template file from help page
    Given I am on "/site/logout"
    When I am on "/site/help#guidelines"
    Then I should see "Excel template file"
    And I press "Excel template file" and the response should contain "200"

  @test
  Scenario: Check if file is download
    When I am on "/datasetSubmission/upload"
#    And I press the button "Download Example File 1"
    Then I press "Download Example File 1" and the response should contain "200"
    And I make a screenshot called "download-button"
    And The file "GigaDBUpload-Example1-forWebsite-v22Dec2021.xlsx" is downloaded