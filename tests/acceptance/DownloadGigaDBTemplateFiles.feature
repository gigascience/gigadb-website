Feature: An author can download file template
  As an author
  I want to download upload form template after login
  So that I can upload data using the form

  Background: Sign is as a user
    Given I sign in gigadb website as "user"
    Then I should see "John's GigaDB Page"
    
  Scenario: Go to Download template file page as a user and each download button linked with correct file path
    When I am on "/datasetSubmission/upload"
    Then I should see a link "Download Template File" to "/files/templates/GigaDBUploadForm-forWebsite-v22Dec2021.xlsx"
    And I should see a link "Download Example File 1" to "/files/templates/GigaDBUpload-Example1-forWebsite-v22Dec2021.xlsx"
    And I should see a link "Download Example File 2" to "/files/templates/GigaDBUpload-Example2-forWebsite-v22Dec2021.xlsx"

  @test
  Scenario: Download template file as a user
    When I am on "/datasetSubmission/upload"
#    And I should see a link "Download Template File" to "/files/templates/GigaDBUploadForm-forWebsite-v22Dec2021.xlsx"
#    When I click the button "/files/templates/GigaDBUploadForm-forWebsite-v22Dec2021.xlsx"
    And I press the button "Download Template File"
    Then The response should contain "200"