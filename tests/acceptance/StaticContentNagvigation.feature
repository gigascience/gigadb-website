Feature: A user visit gigadb website
  As a website user
  I want to see useful and consistent navigational controls in the website's static pages area
  So that I can easily navigate in gigadb website

  @ok @issue-872
  Scenario: Scroll bar is found in tables in guide page
    When I am on "/site/guide"
    Then I should see a table "table_guide_submission" with scroll bar
#    And I make a screenshot of a table "table_submission"
    And I should see a table "table_guide_attribute" with scroll bar
    And I should see a table "table_guide_details" with scroll bar

  @ok @issue-872
  Scenario: Scroll bar is found in tables in genomic page
    When I am on "/site/guidegenomic"
    Then I should see a table "table_genomic_format" with scroll bar
    And I should see a table "table_transcriptomic" with scroll bar
    And I should see a table "table_genomic_meta" with scroll bar

  @ok @issue-872
  Scenario: Scroll bar is found in tables in imaging page
    When I am on "/site/guideimaging"
    Then I should see a table "table_imaging_format" with scroll bar
    And I should see a table "table_imaging_attribute" with scroll bar
    And I should see a table "table_imaging_meta" with scroll bar

  @ok @issue-872
  Scenario: Scroll bar is found in tables in metabolomic page
    When I am on "/site/guidemetabolomic"
    Then I should see a table "table_metabolomic_data" with scroll bar
    And I should see a table "table_metabolomic_meta" with scroll bar


  @ok @issue-872
  Scenario: Scroll bar is found in tables in epigenomic page
    When I am on "/site/guideepigenomic"
    Then I should see a table "table_epigenomic_format" with scroll bar
    And I should see a table "table_epigenomic_meta" with scroll bar

  @ok @issue-872
  Scenario: Scroll bar is found in tables in metagenomic page
    When I am on "/site/guidemetagenomic"
    Then I should see a table "table_metagenomic_format" with scroll bar
    And I should see a table "table_metatranscriptomic" with scroll bar
    And I should see a table "table_metagenomic_meta" with scroll bar

  @ok @issue-872
  Scenario: Scroll bar is found in tables in software page
    When I am on "/site/guidesoftware"
    Then I should see a table "table_software_format" with scroll bar
    And I should see a table "table_software_dataset" with scroll bar