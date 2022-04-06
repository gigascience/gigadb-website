Feature: Excel spreadsheet upload
  As a curator
  I want to use the consultant's tool to upload new metadata about a dataset
  So that new metadata for the dataset is stored in GigaDB

  Background:
    Given I have signed in as admin

  @ok-dataset-upload-tool
  Scenario: Upload new dataset using example Excel spreadsheet
    When I use the dataset upload tool with spreadsheet "100679newversion.xls"
    And I go to "/adminDataset/update/id/701"
    Then I should "Image URL" "http://gigadb.org/images/data/cropped/100679.png"
    And I should see "Dataset Size *" "9663676416"
    And I should see "Ftp Site *" "ftp://parrot.genomics.cn/gigadb/pub/10.5524/100001_101000/100679/"
    And I should see "Title *" "Supporting data for "The draft nuclear genome assembly of Eucalyptus pauciflora: a pipeline for comparing de novo assemblies""
