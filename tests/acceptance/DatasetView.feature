@ok-can-offline
Feature: a user visit the dataset page
  As a website user
  I want to see all the information pertaining to a dataset
  So that I can use it to further my research or education

  @ok
  Scenario: number of files in current page and total number of files are displayed
    Given I have not signed in
    When I am on "dataset/100142"
    Then I should see "Displaying 4 files of 4"

  @ok
  Scenario: pagination widget is not shown when total number of file is less or equal to page size setting
    Given I have not signed in
    When I am on "dataset/100142"
    Then I should not see "Go to page"
    And I should not see "of 1"

  @ok
  Scenario: pagination widget is shown when total number of file greater than the page size setting
    Given I have not signed in
    And I have set the page size setting to 5
    When I am on "/dataset/100006"
    And I follow "Files"
    Then I should see "Next >"
    Then I should see "Go to page"

  @ok @issue-877
  Scenario: The google scholar link is working
    When I am on "/dataset/100094"
    Then I should see an image "/images/google_scholar.png" is linked to "https://scholar.google.com/scholar?q=10.80027/100094"

  @ok @issue-877
  Scenario: The Euro PubMed Central link is working
    When I am on "/dataset/100094"
    Then I should see an image "/images/ePMC.jpg" is linked to "https://europepmc.org/search?scope=fulltext&query=(REF:%2710.80027/100094%27)"

  @ok @issue-877
  Scenario: The dimensions link is working
    When I am on "/dataset/100094"
    Then I should see an image "/images/dimensions.jpg" is linked to "https://app.dimensions.ai/discover/publication?search_text=10.80027/100094"


  @ok
  Scenario: Private dataset not visible
    Given I have not signed in
    When I am on "/dataset/200070"
    And I should not see "well now, how to describe nothing in particular?"
    Then I should see "The DOI 200070 cannot be displayed"

  @ok
  Scenario: Private dataset accessible through mockup url
    Given I have not signed in
    When I am on "/dataset/200070/token/ImP3Bbu7ytRSfYFh"
    Then I should see "well now, how to describe nothing in particular?"
    And I should not see "The DOI 200070 cannot be displayed"

  @ok @issue-917
  Scenario: Checkbox for files table in table settings exists
    When I am on "/dataset/100094"
    And I follow "Files"
    And I should see "Table Settings"
    And I click the table settings for "files_table_settings"
    Then I should see a check-box field "description"
    And I should see a check-box field "sample_id"
    And I should see a check-box field "type_id"
    And I should see a check-box field "format_id"
    And I should see a check-box field "size"
    And I should see a check-box field "date_stamp"
    And I should see a check-box field "location"
    And I should see a check-box field "attribute"

  @ok @issue-917
  Scenario:  Checkbox for files table in table settings can be checked
    When I am on "/dataset/100094"
    And I follow "Files"
    And I click the table settings for "files_table_settings"
    Then I check "description" checkbox
    And I should see "description" checkbox is checked
    And I check "sample_id" checkbox
    And I should see "sample_id" checkbox is checked
    And I check "type_id" checkbox
    And I should see "type_id" checkbox is checked
    And I check "sample_id" checkbox
    And I should see "sample_id" checkbox is checked
    And I check "format_id" checkbox
    And I should see "format_id" checkbox is checked
    And I check "size" checkbox
    And I should see "size" checkbox is checked
    And I check "date_stamp" checkbox
    And I should see "date_stamp" checkbox is checked
    And I check "location" checkbox
    And I should see "location" checkbox is checked
    And I check "attribute" checkbox
    And I should see "attribute" checkbox is checked

  @ok @issue-917
  Scenario:  Checkbox for files table in table settings can be unchecked
    When I am on "/dataset/100094"
    And I follow "Files"
    And I click the table settings for "files_table_settings"
    Then I uncheck "description" checkbox
    And I should see "description" checkbox is not checked
    And I uncheck "sample_id" checkbox
    And I should see "sample_id" checkbox is not checked
    And I uncheck "type_id" checkbox
    And I should see "type_id" checkbox is not checked
    And I uncheck "sample_id" checkbox
    And I should see "sample_id" checkbox is not checked
    And I uncheck "format_id" checkbox
    And I should see "format_id" checkbox is not checked
    And I uncheck "size" checkbox
    And I should see "size" checkbox is not checked
    And I uncheck "date_stamp" checkbox
    And I should see "date_stamp" checkbox is not checked
    And I uncheck "location" checkbox
    And I should see "location" checkbox is not checked
    And I uncheck "attribute" checkbox
    And I should see "attribute" checkbox is not checked

  @ok @datasetimage
  Scenario: Dataset with image associated will show dataset image
    Given I have not signed in
    When I am on "dataset/100006"
    Then I should see an image located in "https://assets.gigadb-cdn.net/live/images/datasets/images/data/cropped/100006_Pygoscelis_adeliae.jpg"

  @ok @datasetimage
  Scenario: Dataset with no image associated will show generic image
    Given I have not signed in
    When I am on "dataset/300070"
    Then I should see an image located in "https://assets.gigadb-cdn.net/live/images/datasets/no_image.png"

  @ok @issue-895
  Scenario: Project image with links
    Given I have not signed in
    When I am on "/dataset/100006"
    Then I should see an image "https://assets.gigadb-cdn.net/live/images/projects/genome_10k/G10Klogo.jpg" is linked to "http://www.genome10k.org/"
    And I should see an image "https://assets.gigadb-cdn.net/live/images/projects/the_avian_phylogenomic_project/phylogenomiclogo.png" is linked to "http://avian.genomics.cn/en/index.html"

  @ok
  Scenario: Github links are displayed on dataset page
    Given I have not signed in
    When I am on "/dataset/100935"
    Then I should see "Github links"
    And I should see "https://github.com/cihga39871/Atria"

  @ok @cite-dataset-button
  Scenario: Display the cite dataset dropdown box content
    Given I have not signed in
    And I am on "/dataset/100006"
    When I press the button "Cite Dataset"
    Then I should see "Text"
    And I should see "RIS"
    And I should see "BibTeX"

  @ok @cite-dataset-button
  Scenario: Display the citation text from datacite
    Given I have not signed in
    And I am on "/dataset/100006"
    And I press the button "Cite Dataset"
    When I follow "Text"
    And I go to the new tab
    Then I should see "Li, J., Zhang, G., Lambert, D., & Wang, J. (2011). Genomic data from Adelie penguin (Pygoscelis adeliae) [Data set]. GigaScience. https://doi.org/10.5524/100006"

  @ok @cite-dataset-button
  Scenario: Display no Cite Dataset button when bad request
    Given I have not signed in
    When I am on "/dataset/300070"
    And I should see "test generic image will be display for no image dataset"
    Then I should not see "Cite Dataset"

