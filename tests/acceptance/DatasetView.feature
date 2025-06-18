Feature: a user visit the dataset page
  As a website user
  I want to see all the information pertaining to a dataset
  So that I can use it to further my research or education

  @ok
  Scenario: Core information
    When I am on "/dataset/100006"
    Then I should see "Genomic data from Adelie penguin (Pygoscelis adeliae)."
    And I should see "Zhang G; Lambert DM; Wang J (2011): Genomic data from Adelie penguin (Pygoscelis adeliae). GigaScience."
    And I should see "https://doi.org/10.5524/100006"
    And I should see "Additional details"
    And I should see "Read the peer-reviewed publication(s):"
    And I should see "Zhang, G., Li, B., Li, C., Gilbert, M. T. P., Jarvis, E. D., & Wang, J. (2014). Comparative genomic data of the Avian Phylogenomics Project. GigaScience, 3(1). https://doi.org/10.1186/2047-217x-3-26"
    And I should see "Related datasets:"
    And I should see "Projects:"
    And I should see "Samples"
    And I should see "Files"
    And I should see "Funding"
    And I should see "3D Models"
    And I should see "3D Sketchfab"
    And I should see "History"

  @ok
  Scenario: Keywords are displayed are displayed
    Given I have not signed in
    When I am on "dataset/100142"
    Then I should see "Keywords:"
    And I should see "Sequence Read Archive"
    And I should see "metadata"
    And I should see "SQL"
    And I should see "experimental protocol"

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

    @ok
  Scenario: Don't give guest a button to claim a dataset
    Given I have not signed in
    When I am on "/dataset/100006"
    Then I should not see "Your dataset?"

  @ok
  Scenario: Give users a button to claim a dataset they have authored
    Given I have signed in as user
    When I am on "/dataset/100006"
    Then I should see "Your dataset?"

  @ok @javascript
  Scenario: a user is shown a modal to claim his/her dataset by reconcilling his/her author identity to his/her account
    Given I have signed in as user
    And I am on "/dataset/100006"
    When I follow "Your dataset?"
    And I wait "2" seconds
    Then I should see "David M Lambert"
    And I should see "Guojie Zhang"
    And I should see "Jun Wang"
    And I should see "Select an author to link to your Gigadb User ID"

  @ok @javascript @insulate
  Scenario: a user select an author to claim and submit the claim form
    Given I have signed in as user
    And I am on "/dataset/100006"
    When I follow "Your dataset?"
    And I wait "2" seconds
    And I follow "Guojie Zhang"
    And I wait "3" seconds
    Then I should see "Your claim has been submitted to the administrators."
    And I should see "You can close this box now."

  @ok @javascript @insulate
  Scenario: a user with a pending claim visit dataset page and attempt to re claim the author
    Given I have signed in as user
    And I am on "/dataset/100006"
    When I follow "Your dataset?"
    And I wait "2" seconds
    And I follow "David M Lambert"
    And I wait "3" seconds
    Then I should see "We cannot submit the claim: You already have a pending claim."

  @ok @javascript @claim-error-path
  Scenario: a user with a rejected claim visit dataset page and attempt to re claim the author
    Given I have signed in as admin
    And I am on "/user/update/id/401"
    And I follow "Reject"
    And I should see "Claimed rejected. No linking performed"
    And I have not signed in
    And I have signed in as user
    And I am on "/dataset/100006"
    When I follow "Your dataset?"
    And I wait "1" seconds
    And I follow "David M Lambert"
    And I wait "3" seconds
    Then I should see "We cannot submit the claim: Your claim on this author has already been rejected."
    And I should see "You can close this box now."

  @ok @javascript @claim-error-path
  Scenario: a user with a rejected claim visit dataset page and attempt to claim an author
    Given I have signed in as admin
    And I am on "/user/update/id/401"
    And I follow "Reject"
    And I should see "Claimed rejected. No linking performed"
    And I have not signed in
    And I have signed in as user
    And I am on "/dataset/100006"
    When I follow "Your dataset?"
    And I wait "1" seconds
    And I follow "Guojie Zhang"
    And I wait "3" seconds
    Then I should see "Your claim has been submitted to the administrators."
    And I should see "You can close this box now."

  @ok @javascript @claim-error-path
  Scenario:a user already associated to an author cannot claim another author
    Given I have signed in as admin
    And I am on "/user/update/id/401"
    And I follow "Validate"
    And I should see "This user is linked to author: Lambert DM (3371)"
    And I have not signed in
    And I have signed in as user
    And I am on "/dataset/100006"
    Then I should not see "Your dataset?"

  @ok @javascript
  Scenario: a user with a pending claim can cancel the claim
    Given I have signed in as user
    And I am on "/dataset/100006"
    When I follow "Your dataset?"
    And I wait "1" seconds
    And I follow "Cancel current claim"
    And I wait "1" seconds
    Then I should see "Your claim has been successfully canceled."

  @ok @issue-877
  Scenario: The google scholar link is working
    When I am on "/dataset/100094"
    Then I should see an image "/images/google_scholar.png" is linked to "https://scholar.google.com/scholar?q=10.80027/100094"

  @ok @issue-877
  Scenario: The Euro PubMed Central link is working
    When I am on "/dataset/100094"
    Then I should see an image "/images/ePMC.jpg" is linked to "https://europepmc.org/search?scope=fulltext&query=(REF:%2710.80027/100094%27)"

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
    Then I should see "Zhang, G., Lambert, D. M., & Wang, J. (2011). Genomic data from Adelie penguin (Pygoscelis adeliae). [Data set]. GigaScience. https://doi.org/10.5524/100006"

  @ok @cite-dataset-button
  Scenario: Display no Cite Dataset button when bad request
    Given I have not signed in
    When I am on "/dataset/300070"
    And I should see "test generic image will be display for no image dataset"
    Then I should not see "Cite Dataset"

  @ok
  Scenario: Pagination in the sample tab
    Given I have not signed in
    And I am on "/dataset/100035"
    And I follow "Sample"
    And I should see "SRS173539"
    When I follow "2"
    Then I should see "SRS173549"
    And I should not see "SRS173539"

  @ok @no-double-pagination
  Scenario: Check there is no double pagination in URL when viewing between samples and files
    Given I have not signed in
    And I am on "/dataset/100035"
    And I follow "Sample"
    And I should see "SRS173539"
    When I follow "2"
    And I follow "Files"
    And I follow "3"
    Then I should be on "/dataset/view/id/100035/Files_page/3"
    # If bug was present, URL would look like:
    # "/dataset/view/id/100035/Samples_page/2/Files_page/3"
    And I should see "GSM678699_sample16_mefTDGmm.bam.gz"

  @ok
  Scenario: Canonical URL is shown in page head block
    When I am on "/dataset/view/id/100035/Samples_page/2"
    Then I should see link element of type "canonical" to "http://gigadb.test/dataset/100035"

  @ok @sample-attributes-toggler
  Scenario: Expand the sample attributes box in sample tab
    Given I have not signed in
    And I am on "/dataset/100006"
    And I follow "Sample"
    And I should not see "Alternative names:PYGAD"
    When I press the button "+"
    Then I should see "Alternative names:PYGAD"

  @ok @sample-attributes-toggler
  Scenario: Collapse the sample attributes box in sample tab
    Given I have not signed in
    And I am on "/dataset/100006"
    And I follow "Sample"
    And I press the button "+"
    And I should see "Alternative names:PYGAD"
    When I press the button "-"
    Then I should not see "Alternative names:PYGAD"

  @ok @issue-2067
  Scenario: Files tab can be sort by size in ascending order
    Given I have not signed in
    And I am on "/dataset/100035"
    And I follow "Files"
    When I follow "[aria-label^='Size']"
    Then I should see "1.19 kB" in the table "#files_table" cell 1 6

  @ok @issue-2067
  Scenario: Files tab can be sort by size in descending order
    Given I have not signed in
    And I am on "/dataset/100035"
    And I follow "Files"
    When I follow "[aria-label^='Size']"
    And I follow "[aria-label^='Size']"
    Then I should see "3.88 GB" in the table "#files_table" cell 1 6

  @ok @files @javascript
  Scenario: Files - table
    Given I have not signed in
    And I am on "/dataset/100035"
    And I follow "Files"
    Then I should see "GSM678684_sample1_undifTDGpm.bam.gz"
    And I should see "GSM678685_sample2_undifTDGpm.bam.gz"
    And I should see "GSM678686_sample3_undifTDGpm.bam.gz"
    And I should see "GSM678687_sample4_undifTDGmm.bam.gz"

  @ok @files @pr464
  Scenario: Files - Columns
    Given I have not signed in
    And I am on "/dataset/100035"
    When I follow "Files"
    Then I should see "File Name"
    And I should see "Description"
    And I should see "Data Type"
    And I should see "Size"
    And I should see "File Attributes"
    And I should see "Download"

  @ok @files @pr464
  Scenario: Files - Table settings controls
    Given I have not signed in
    When I am on "/dataset/100035"
    And I follow "Files"
    And I click the table settings for "files_table_settings"
    And I wait "1" seconds
    Then I should see "Items per page:"
    And I should see "Columns to display:"
    And I should see "File Description"
    And I should see "description" checkbox is checked
    And I should see "Sample ID"
    And I should see "sample_id" checkbox is checked
    And I should see "Data Type"
    And I should see "type_id" checkbox is checked
    And I should see "File Format"
    And I should see "format_id" checkbox is checked
    And I should see "Size"
    And I should see "size" checkbox is checked
    And I should see "Release Date"
    And I should see "date_stamp" checkbox is checked
    And I should see "Download Link"
    And I should see "location" checkbox is checked
    And I should see "File Attributes"
    And I should see "attribute" checkbox is checked
    And I should see "Save changes"
    And I should see "Close"

  @ok @issue-2054
  Scenario: 3D Models tab
    Given I have not signed in
    When I am on "/dataset/100006"
    Then I should see "3D Models"

  @ok @issue-2054
  Scenario: 3D model drop down list
    Given I have not signed in
    When I am on "/dataset/100006"
    And I follow "3D Models"
    Then I should see "3D Models:"
    And I should see "Select a model"
    And I should see "GeoB8502_865cm_Shell-4.obj"

  @ok
  Scenario: 3D Sketchfab tab
    Given I have not signed in
    When I am on "/dataset/100006"
    Then I should see "3D Sketchfab"

  @ok
  Scenario: 3D Sketchfab tab content
    Given I have not signed in
    When I am on "/dataset/100006"
    And I follow "3D Sketchfab"
    Then I should see "3D Sketchfab:"


  @ok
  Scenario: List ordered author list
    Given I have not signed in
    When I am on "/dataset/100020"
    Then I should see "Liu X; Quan Z; Cheng S; Xu X; Pan S; Zeng P; Xie M; Yue Z; Zhan D; Li Y; Wang J; Zhao Z; Zhang G (2011)"
    And I should not see "Wang, J; Quan, Z; Zhao, Z; Cheng, S; Liu, X; Li, Y; Pan, S; Xie, M; Xu, X; Yue, Z; Zeng, P; Zhan, D; Zhang, G (2011)"

    @ok
  Scenario: Show pre print publications
    Given I have not signed in
    When I am on "/dataset/100142"
    Then I should see "Read the pre-print publication(s):"

  @ok @javascript
  Scenario: Popup Old version with link to new version doesn't have a close button
    Given I have not signed in
    When I am on "dataset/100044"
    And I wait "3" seconds
    And I should see "There is a new version of this dataset available at DOI 10.80027/100006"
    And I should not see "Close"

  @ok @javascript
  Scenario: Popup Old version with link to new version go to new version when choosing new version
    Given I have not signed in
    And I am on "dataset/100044"
    And I wait "3" seconds
    And I should see "There is a new version of this dataset available at DOI 10.80027/100006"
    And I should see "View new version"
    When I press the button "View new version"
    And I wait "3" seconds
    Then I should be on "/dataset/100006"

  @ok @javascript
  Scenario: Popup Old version with link to new version closes when choosing old version
    Given I have not signed in
    And I am on "dataset/100044"
    And I wait "3" seconds
    And I should see "There is a new version of this dataset available at DOI 10.80027/100006"
    And I should see "Continue to view old version"
    When I press the button "Continue to view old version"
    And I wait "3" seconds
    Then I should not see "There is a new version of this dataset available at DOI 10.80027/100006"

  @ok @samples @javascript
  Scenario: Samples - Pagination
    Given I have not signed in
    And I am on "/dataset/100035"
    And I should see "Sample"
    When I follow "2"
    Then I should see the table with the following rows:
      | SRS173549 	| Mouse | Mus musculus | 10090 |  | house mouse |
      | SRS173550 	| Mouse | Mus musculus | 10090 |  | house mouse |
      | SRS173551 	| Mouse | Mus musculus | 10090 |  | house mouse |
      | SRS173552 	| Mouse | Mus musculus | 10090 |  | house mouse |
      | SRS173553 	| Mouse | Mus musculus | 10090 |  | house mouse |
      | SRS173554 	| Mouse | Mus musculus | 10090 |  | house mouse |
      | SRS173555 	| Mouse | Mus musculus | 10090 |  | house mouse |

  @ok @samples @javascript @pr464
  Scenario: Samples - Columns
    Given I have not signed in
    And I am on "/dataset/100035"
    When I follow "Sample"
    And I click the table settings for "samples_table_settings"
    And I wait "1" seconds
    And I uncheck "common_name" checkbox
    And I press the button "Save changes"
    Then I should see the table with the following rows:
      | SRS173539 	| Mus musculus | 10090 |  | house mouse |
      | SRS173540 	| Mus musculus | 10090 |  | house mouse |
      | SRS173541 	| Mus musculus | 10090 |  | house mouse |
      | SRS173542 	| Mus musculus | 10090 |  | house mouse |
      | SRS173543 	| Mus musculus | 10090 |  | house mouse |
      | SRS173544 	| Mus musculus | 10090 |  | house mouse |
      | SRS173545 	| Mus musculus | 10090 |  | house mouse |

  @ok @samples @javascript @pr464
  Scenario: Samples - Items per page
    Given I have not signed in
    And I am on "/dataset/100035"
    When I follow "Sample"
    And I click the table settings for "samples_table_settings"
    And I wait "1" seconds
    And I select "5" from the field "selectPageSizeSampleSetting"
    And I press the button "Save changes"
    Then I should see the table with the following rows:
      | SRS173539 	| Mouse | Mus musculus | 10090 |  | house mouse |
      | SRS173540 	| Mouse | Mus musculus | 10090 |  | house mouse |
      | SRS173541 	| Mouse | Mus musculus | 10090 |  | house mouse |
      | SRS173542 	| Mouse | Mus musculus | 10090 |  | house mouse |
      | SRS173543 	| Mouse | Mus musculus | 10090 |  | house mouse |
    And I should not see the table with the following rows:
      | SRS173544 	| Mouse | Mus musculus | 10090 |  | house mouse |
      | SRS173545 	| Mouse | Mus musculus | 10090 |  | house mouse |
      | SRS173546 	| Mouse | Mus musculus | 10090 |  | house mouse |

  @ok @samples
  Scenario: Samples - Table settings controls
    Given I have not signed in
    When I am on "/dataset/100035"
    And I follow "Sample"
    And I click the table settings for "samples_table_settings"
    And I wait "1" seconds
    Then I should see "Items per page:"
    And I should see "Common Name"
    And I should see "common_name" checkbox is checked
    And I should see "Scientific Name"
    And I should see "scientific_name" checkbox is checked
    And I should see "Sample Attributes"
    And I should see "sample_attribute" checkbox is checked
    And I should see "Taxonomic ID"
    And I should see "taxonomic_id" checkbox is checked
    And I should see "Genbank Name"
    And I should see "genbank_name" checkbox is checked
    And I should see "Save changes"
    And I should see "Close"

  @ok
  Scenario: JBrowse
    Given I have not signed in
    When I am on "/dataset/100020"
    And I should see "JBrowse"

  @ok
  Scenario: Code Ocean
    Given I have not signed in
    When I am on "/dataset/100020"
    Then I should see "Code Ocean"

  @ok
  Scenario: Protocols.io
    Given I have not signed in
    When I am on "/dataset/100020"
    Then I should see "Protocols.io"

  @ok
  Scenario: Funding
    Given I have not signed in
    When I am on "/dataset/100020"
    And I follow "Funding"
    Then I should see "Funding body"
    And I should see "Awardee"
    And I should see "Award ID"
    And I should see "Comments"

  @ok
  Scenario: History
    Given I have not signed in
    When I am on "/dataset/100020"
    And I follow "History"
    Then I should see "Date"
    And I should see "Action"

  @ok
  Scenario: Call To Actions - not logged in
    Given I have not signed in
    When I am on "/dataset/100020"
    Then I should see "Contact Submitter"
    And I should not see "Your dataset?"

  @ok
  Scenario: Non-Tabbed External Links (e.g: Genome Browser)
    Given I have not signed in
    When I am on "/dataset/100020"
    Then I should see "http://foxtailmillet.genomics.org.cn/"

  @ok
  Scenario: Semantic Links
    Given I have not signed in
    When I am on "/dataset/100006"
    Then I should see "Related datasets:"
    And I should see "doi:10.80027/100006 IsSupplementTo doi:10.80027/100020"
