Feature:
  As an editor
  I want the automatically downloaded EM reports spreadsheet to be ingested automatically
  So that I can maintain an up-to-date database of peer reviews to be curated and to be shown on GigaReviews

  Background:
    Given the database is reset

  @wip
  Scenario: EM manuscript report is downloaded and saved to manuscript table
    Given the file is on the sftp server
    And the file ingester has run
    And the EM "manuscripts" report spreadsheet is downloaded
    When the queue job is pushed to "manuscripts" worker
    And the EM "manuscripts" report spreadsheet is parsed and saved
    Then I should see in "manuscript" table
    | manuscript_number | article_title | editorial_status_date | editorial_status |
    | GIGA-D-22-00054   | A machine learning framework for discovery and enrichment of metagenomics metadata from open access publications| 6/7/2022 | Final Decision Accept |
    | GIGA-D-22-00060   | A chromosome-level genome of the booklouse, Liposcelis brunnea provides insight into louse evolution and environmental stress adaptation | 6/7/2022 | Final Decision Reject |
    | GIGA-D-22-00030   | A novel ground truth multispectral image dataset with weight, anthocyanins and brix index measures of grape berries tested for its utility in machine learning pipelines | 6/7/2022 | Final Decision Pending |


  @wip
  Scenario: Download EM manuscript no results report
    Given the "manuscripts" no results report is created and found in the sftp
    And the file ingester has run
    And the EM "manuscripts" no results report spreadsheet is downloaded
    When the queue job is pushed to "manuscripts" worker
    Then the EM "manuscripts" report spreadsheet is not parsed nor saved
    And I should see "manuscript" table is empty
    And remove temporary "manuscripts" no results report spreadsheet
