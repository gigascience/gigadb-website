Feature: An author can download file template
  As an author
  I want to download upload form template after login
  So that I can upload data using the form

  Scenario: Sign is as a user
    When I sign in gigadb website as "user"
    Then I should see "John's GigaDB Page"
    
  Scenario: Go to Download template file page as a user
    Given I sign in gigadb website as "user"
    When I am on "/datasetSubmission/upload"
    And I should see "Download Template File"
    And I should see "Download Example File 1"
    And I should see "Download Example File 2"

  @test
  Scenario: Download template file as a user
    Given I sign in gigadb website as "user"
    And I am on "/datasetSubmission/upload"
    When I am on "/files/templates/GigaDBUploadForm-forWebsite-v22Dec2021.xlsx"
    Then The response should contain "200"