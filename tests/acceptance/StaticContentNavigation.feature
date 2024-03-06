@ok-can-offline
Feature: A user visit gigadb website
  As a website user
  I want to see useful and consistent navigational controls in the website's static pages area
  So that I can easily navigate in gigadb website

  @ok @issue-873 @issue-874
  Scenario: Terms - GigaDB User Policies
    When I am on "/site/term"
    Then I should see "GigaDB User Policies"
    And I should not see "<em>GigaDB</em> User Policies"

  @ok @issue-870
  Scenario: Update dataset types in controlled vocabulary tab
    When I am on "/site/help#vocabulary"
    Then I should see "Dataset types"
    And I should see "Genomic"
    And I should see "Transcriptomic"
    And I should see "Epigenomic"
    And I should see "Metagenomic"
    And I should see "Metatranscriptomic"
    And I should see "Genome mapping"
    And I should see "Imaging"
    And I should see "Software"
    And I should see "Virtual-Machine"
    And I should see "Workflow"
    And I should see "Metabolomic"
    And I should see "Proteomic"
    And I should see "Lipidomic"
    And I should see "Metabarcoding"
    And I should see "Metadata"
    And I should see "Network-Analysis"
    And I should see "Neuroscience"
    And I should see "Electro-encephalography (EEG)"
    And I should see "Phenotyping"
    And I should see "Ecology"
    And I should see "Climate"
    And I should see "Additional dataset types can be added, upon review, as new submissions are received."

  @ok @issue-871
  Scenario: The anchor tag in working for GigaDB search tab
    Given I am on "/site/help"
    When I go to a page tab "/site/help#search"
    Then I should see "Search operation"
    And I should see "Search result"
    And I should see "Filtering result"

  @ok @issue-871
  Scenario: The anchor tag in working for submission guidelines tab
    Given I am on "/site/help"
    When I go to a page tab "/site/help#guidelines"
    Then I should see "Mandatory fields are highlighted in yellow."
    And I should see "Study"
    And I should see "Samples"
    And I should see "Files"

  @ok @issue-871
  Scenario: The anchor tag is working for controlled vocabulary tab
    Given I am on "/site/help"
    When I go to a page tab "/site/help#vocabulary"
    Then I should see "Dataset types"
    And I should see "File types"
    And I should see "File formats"
    And I should see "Upload status"
    And I should see "DOI relationship"
    And I should see "Missing Value reporting"

  @ok @issue-871
  Scenario: The anchor tag is working for Application programming interface
    Given I am on "/site/help"
    When I go to a page tab "/site/help#interface"
    Then I should see "Availability"
    And I should see "Comments and Bug reporting"
    And I should see "Summary"
    And I should see "Terminology"
    And I should see "Examples"
    And I should see "Command line usage"

  @ok @issue-872
  Scenario: Scroll bar is found in tables in guide page
    When I am on "/site/guide"
    Then I should see an element has id "table_guide_submission" with class "scrollbar"
    And I should see an element has id "table_guide_attribute" with class "scrollbar"
    And I should see an element has id "table_guide_details" with class "scrollbar"

  @ok @issue-872
  Scenario: Scroll bar is found in tables in genomic page
    When I am on "/site/guidegenomic"
    Then I should see an element has id "table_genomic_format" with class "scrollbar"
    And I should see an element has id "table_transcriptomic" with class "scrollbar"
    And I should see an element has id "table_genomic_meta" with class "scrollbar"

  @ok @issue-872
  Scenario: Scroll bar is found in tables in imaging page
    When I am on "/site/guideimaging"
    Then I should see an element has id "table_imaging_format" with class "scrollbar"
    And I should see an element has id "table_imaging_attribute" with class "scrollbar"
    And I should see an element has id "table_imaging_meta" with class "scrollbar"

  @ok @issue-872
  Scenario: Scroll bar is found in tables in metabolomic page
    When I am on "/site/guidemetabolomic"
    Then I should see an element has id "table_metabolomic_data" with class "scrollbar"
    And I should see an element has id "table_metabolomic_meta" with class "scrollbar"


  @ok @issue-872
  Scenario: Scroll bar is found in tables in epigenomic page
    When I am on "/site/guideepigenomic"
    Then I should see an element has id "table_epigenomic_format" with class "scrollbar"
    And I should see an element has id "table_epigenomic_meta" with class "scrollbar"

  @ok @issue-872
  Scenario: Scroll bar is found in tables in metagenomic page
    When I am on "/site/guidemetagenomic"
    Then I should see an element has id "table_metagenomic_format" with class "scrollbar"
    And I should see an element has id "table_metatranscriptomic" with class "scrollbar"
    And I should see an element has id "table_metagenomic_meta" with class "scrollbar"

  @ok @issue-872
  Scenario: Scroll bar is found in tables in software page
    When I am on "/site/guidesoftware"
    Then I should see an element has id "table_software_format" with class "scrollbar"
    And I should see an element has id "table_software_dataset" with class "scrollbar"

  @ok @spike
  Scenario: Jobs could be found in the About dropdown box in the main page
    When I am on "/index.php"
    And I press the button "About"
    Then I should see "General"
    And I should see "Our team"
    And I should see "Jobs"
    And I should see "Contact"
    And I should see "Advisory Board"

  # @ok @spike
  # Scenario: Go to the Jobs page from the main page
  #   When I am on "/index.php"
  #   And I press the button "About"
  #   And I press the button "Jobs"
  #   Then I should see "Jobs at GigaScience"
  #   And I am on "https://jobs.gigasciencejournal.com/"

  @ok @spike
  Scenario: Jobs could be found in the About dropdown box in the faq page
    When I am on "/site/faq"
    And I press the button "About"
    Then I should see "General"
    And I should see "Our team"
    And I should see "Jobs"
    And I should see "Contact"
    And I should see "Advisory Board"

  # @ok @spike
  # Scenario: Go to the Jobs page from the faq page
  #   When I am on "/site/faq"
  #   And I press the button "About"
  #   And I press the button "Jobs"
  #   Then I should see "Jobs at GigaScience"
  #   And I am on "https://jobs.gigasciencejournal.com/"

  @ok @spike
  Scenario: Jobs could be found in the About dropdown box in the dataset page
    When I am on "/dataset/100006"
    And I press the button "About"
    Then I should see "General"
    And I should see "Our team"
    And I should see "Jobs"
    And I should see "Contact"
    And I should see "Advisory Board"

  # @ok @spike
  # Scenario: Go to the Jobs page from the dataset page
  #   When I am on "/dataset/100006"
  #   And I press the button "About"
  #   And I press the button "Jobs"
  #   Then I should see "Jobs at GigaScience"
  #   And I am on "https://jobs.gigasciencejournal.com/"