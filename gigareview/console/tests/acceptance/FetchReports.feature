Feature: FetchReports
  As an editor
  I want The EM report to be automatically captured periodically
  So that the regular reviews spreadsheet in the report is downloaded and can be further processed

  Scenario: try FetchReports
    Given a EM report is uploaded daily to a sftp server
    And the database is clean
    When the file is on the sftp server
    And the file ingester has run
    Then the EM "manuscripts" report spreadsheet is downloaded
    And the EM "authors" report spreadsheet is downloaded
    And the EM "reviews" report spreadsheet is downloaded
    And the EM "reviewers" report spreadsheet is downloaded
    And the EM "questions" report spreadsheet is downloaded