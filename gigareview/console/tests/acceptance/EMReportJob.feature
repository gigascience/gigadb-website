Feature:
  As an editor
  I want the automatically downloaded EM reports spreadsheet to be ingested automatically
  So that I can maintain an up-to-date database of peer reviews to be curated and to be shown on GigaReviews

  @wip
  Scenario: Download EM manuscript report
    Given the file is on the sftp server
    When the file ingester has run
    Then the EM "manuscripts" report spreadsheet is downloaded