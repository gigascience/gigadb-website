Feature: Want to download dataset files from the website
  As a website user
  I want to download from GigaDB website the files associated with a dataset
  So that I can do my work

  Scenario: basic configuration
    Given the tool is configured
    When I run the command "./yii dataset-files/download-restore-backup" with options "--help"
    Then I should see "--date: string"