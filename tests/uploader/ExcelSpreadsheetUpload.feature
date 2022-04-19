Feature: Excel spreadsheet upload
  As a curator
  I want to use the consultant's tool to upload metadata for a new dataset
  So that the dataset metadata is stored in GigaDB

  Background:
    Given I have signed in as admin

  @ok-dataset-upload-tool
  Scenario: Upload new dataset using example Excel spreadsheet
    When I am on "/adminDataset/admin"
    Then I should see "test dataset"
#    And I should see "100679"
#    And I am on "/adminDataset/update/id/701"
#    And I select "Private" from the field "Dataset_upload_status"
#    And I press the button "Save"
#    And I am on "/adminDataset/update/id/701"
#    And I press the button "Create/Reset Private URL"
    And I am on "/adminDataset/update/id/701"
    And I follow "Open Private URL"
    And I make a screenshot called "screenshot"
#    Then I should see an image located in "http://gigadb.org/images/data/cropped/100679.png"
#    Then I should see "Image URL" "http://gigadb.org/images/data/cropped/100679.png"
#    And I should see "Dataset Size *" "9663676416"
#    And I should see "Ftp Site *" "ftp://parrot.genomics.cn/gigadb/pub/10.5524/100001_101000/100679/"
#    And I should see "Title *" "Supporting data for "The draft nuclear genome assembly of Eucalyptus pauciflora: a pipeline for comparing de novo assemblies""
