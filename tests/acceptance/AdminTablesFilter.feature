Feature: filter tables on admin page
  As a curator
  I want to filter the tables on the admin pages
  So that I can quickly access the rows I am interested in

  Background:
    Given I have signed in as admin

  @ok
  Scenario: Dataset table
    Given I am on "/adminDataset/admin"
    When I fill in the field of "name" "Dataset[identifier]" with "100006"
    And I press return on the element "(//input)[2]"
    And I wait "1" seconds
    Then I should see "Genomic data from Adelie penguin"
    And I should not see "100020"
    And I should not see "100094"
    And I should not see "100142"

  @ok
  Scenario: Dataset authors table
    Given I am on "/adminDatasetAuthor/admin"
    When I fill in the field of "name" "DatasetAuthor[doi_search]" with "100006"
    And I press return on the element "(//input)[1]"
    And I wait "1" seconds
    Then I should see "Zhang, Guojie"
    And I should not see "100020"

  @ok
  Scenario: Dataset Files
    Given I am on "/adminFile/admin"
    When I fill in the field of "name" "File[doi_search]" with "100006"
    And I press return on the element "(//input)[1]"
    And I wait "1" seconds
    Then I should see "Pygoscelis_adeliae.RepeatMasker.out.gz"
    And I should not see "100020"
    And I should not see "100094"
    And I should not see "100142"

  @ok
  Scenario: Dataset Project
    Given I am on "/adminDatasetProject/admin"
    When I fill in the field of "name" "DatasetProject[project_name_search]" with "Avian"
    And I press return on the element "(//input)[2]"
    And I wait "1" seconds
    Then I should see "The Avian Phylogenomic Project"
    And I should not see "Genome 10K"

  @ok
  Scenario: Dataset Links
    Given I am on "/adminLink/admin"
    When I fill in the field of "name" "Link[link]" with "BioProject"
    And I press return on the element "(//input)[3]"
    And I wait "1" seconds
    Then I should see "BioProject:PRJNA77795"
    Then I should see "BioProject:PRJNA73995"
    And I should not see "SRA:SRA048234"

  @ok
  Scenario: Dataset relations
    Given I am on "/adminRelation/admin"
    When I fill in the field of "name" "Relation[doi_search]" with "100006"
    And I press return on the element "(//input)[1]"
    And I wait "1" seconds
    Then I should see "100649"
    Then I should see "IsCitedBy"
    And I should not see "100020"
    And I should not see "100213"
    And I should not see "IsReferencedBy"

  @ok
  Scenario: Dataset funders
    Given I am on "/datasetFunder/admin"
    When I fill in the field of "name" "DatasetFunder[doi_search]" with "100006"
    And I press return on the element "(//input)[1]"
    And I wait "1" seconds
    Then I should see "Catapult"
    And I should not see "Science IO"

  @ok
  Scenario: Dataset manuscript
    Given I am on "/adminManuscript/admin"
    When I fill in the field of "name" "Manuscript[identifier]" with "2047-217X-3-10"
    And I press return on the element "(//input)[1]"
    And I wait "1" seconds
    Then I should see "100094"
    And I should not see "100020"
    And I should not see "100006"
    And I should not see "100142"

  @ok
  Scenario: Authors
    Given I am on "/adminAuthor/admin"
    When I fill in the field of "name" "Author[dois_search]" with "100020"
    And I press return on the element "(//input)[5]"
    And I wait "1" seconds
    Then I should see "Zhiwu"
    And I should not see "100094"

  @ok
  Scenario: Species
    Given I am on "/adminSpecies/admin"
    When I fill in the field of "name" "Species[common_name]" with "penguin"
    And I press return on the element "(//input)[3]"
    And I wait "1" seconds
    Then I should see "Adelie penguin"
    And I should not see "foxtail"

  @ok
  Scenario: Projects
    Given I am on "/adminProject/admin"
    When I fill in the field of "name" "Project[name]" with "Avian"
    And I press return on the element "(//input)[2]"
    And I wait "1" seconds
    Then I should see "The Avian Phylogenomic Project"
    And I should not see "Genome 10K"

  @ok
  Scenario: External links
    Given I am on "/adminExternalLink/admin"
    When I fill in the field of "name" "ExternalLink[doi_search]" with "100094"
    And I press return on the element "(//input)[1]"
    And I wait "1" seconds
    Then I should see "https://github.com/ShashaankV/CS"
    Then I should see "https://github.com/ShashaankV/GD"
    And I should not see "100020"

  @ok
  Scenario: Link prefixes
    Given I am on "/adminLinkPrefix/admin"
    When I fill in the field of "name" "Prefix[prefix]" with "DDBJ"
    And I press return on the element "(//input)[1]"
    And I wait "1" seconds
    Then I should see "http://www.ddbj.nig.ac.jp/"
    And I should not see "EBI"
    And I should not see "NCBI"
    And I should not see "DOI"
    And I should not see "DOID"

  @ok
  Scenario: Funder
    Given I am on "/funder/admin"
    When I fill in the field of "name" "Funder[primary_name_display]" with "Catapult"
    And I press return on the element "(//input)[3]"
    And I wait "1" seconds
    Then I should see "http://catapult.org"
    And I should not see "http://science.io"

  @ok
  Scenario: Attribute
    Given I am on "/attribute/admin"
    When I fill in the field of "name" "Attribute[attribute_name]" with "location"
    And I press return on the element "(//input)[2]"
    And I wait "1" seconds
    Then I should see "Geographic"
    And I should not see "sex"
    And I should not see "material"
    And I should not see "tissue"
    And I should not see "ploidy"
    And I should not see "alternative"

  @ok
  Scenario: Dataset types
    Given I am on "/adminDatasetType/admin"
    When I fill in the field of "name" "Type[name]" with "nomic"
    And I press return on the element "(//input)[1]"
    And I wait "1" seconds
    Then I should see "Genomic"
    Then I should see "Epigenomic"
    And I should not see "Metadata"
    And I should not see "Software"

  @ok
  Scenario: Data types
    Given I am on "/adminFileType/admin"
    When I fill in the field of "name" "FileType[name]" with "Other"
    And I press return on the element "(//input)[1]"
    And I wait "1" seconds
    Then I should see "to be used when no appropriate file type is available"
    And I should not see "Readme"
    And I should not see "Text"
    And I should not see "Image"
    And I should not see "Annotation"
    And I should not see "Protein sequence"
    And I should not see "Repeat sequence"
    And I should not see "Coding sequence"
    And I should not see "Mixed archive"

  @ok
  Scenario: File formats
    Given I am on "/adminFileFormat/admin"
    When I fill in the field of "name" "FileFormat[name]" with "DOCX"
    And I press return on the element "(//input)[1]"
    And I wait "1" seconds
    Then I should see "microsoft word format"
    And I should not see "TEXT"
    And I should not see "FASTA"
    And I should not see "FASTQ"
    And I should not see "GFF"
    And I should not see "TAR"
    And I should not see "PDF"
    And I should not see "UNKNOWN"
    And I should not see "AGP"
    And I should not see "CSV"
    And I should not see "JPG"
    And I should not see "PNG"

  @ok
  Scenario: Users
    Given I am on "/user/admin"
    When I fill in the field of "name" "User[email]" with "admin@"
    And I press return on the element "(//input)[2]"
    And I wait "1" seconds
    Then I should see "admin@gigadb.org"
    And I should not see "user@gigadb.org"
    And I should not see "@gigasciencejournal.com"

  @ok
  Scenario: News items
    Given I am on "/news/admin"
    When I fill in the field of "name" "News[start_date]" with "2017-03-22"
    And I press return on the element "(//input)[4]"
    And I wait "1" seconds
    Then I should see "Join the team!"
    And I should not see "Testing News item"
    And I should not see "Planned maintenance"

  @ok
  Scenario: Publishers
    Given I am on "/adminPublisher/admin"
    When I fill in the field of "name" "Publisher[name]" with "database"
    And I press return on the element "(//input)[1]"
    And I wait "1" seconds
    Then I should see "GigaScience Database"
    And I should not see "Open Life Science publishing"

  @ok
  Scenario: Update logs
    Given I am on "/datasetLog/admin"
    When I fill in the field of "name" "DatasetLog[doi]" with "100020"
    And I press return on the element "(//input)[3]"
    And I wait "1" seconds
    Then I should see "File Millet.fa.glean.pep.v3.gz updated"
    And I should not see "100142"
    And I should not see "100006"
    And I should not see "100094"



