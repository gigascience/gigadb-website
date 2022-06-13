Feature: FetchReports
  As an editor
  I want The EM report to be automatically captured periodically
  So that the regular reviews spreadsheet in the report is downloaded and can be further processed

  Scenario: try FetchReports
    Given a EM report is uploaded daily to a sftp server
    When the file is on the sftp server
    And the file ingester has run
    Then the EM report spreadsheet is downloaded