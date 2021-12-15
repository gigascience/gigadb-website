Feature: filter tables on admin page
  As a curator
  I want to filter the tables on the admin pages
  So that I can quickly access the rows I am interested in

  Background:
    Given I have signed in as admin

  @ok
  Scenario: Dataset table
    Given I am on "/adminDataset/admin"
    When I fill in the field of "name" "Dataset[identifier]" with "100005"
    And I press return on the element "(//input)[2]"
    And I wait "1" seconds
    Then I should see "100005"
    And I should not see "100039"
    And I should not see "100002"
    And I should not see "100003"
    And I should not see "100004"

  @ok
  Scenario: Dataset authors table
    Given I am on "/adminDatasetAuthor/admin"
    When I fill in the field of "name" "DatasetAuthor[doi_search]" with "100005"
    And I press return on the element "(//input)[1]"
    And I wait "1" seconds
    Then I should see "100005"
    And I should not see "100039"
    And I should not see "100002"
    And I should not see "100003"

  @ok
  Scenario: Dataset Files
    Given I am on "/adminFile/admin"
    When I fill in the field of "name" "File[doi_search]" with "100005"
    And I press return on the element "(//input)[1]"
    And I wait "1" seconds
    Then I should see "100005"
    And I should not see "100039"

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
    Then I should see "BioProject:PRJNA171587"
    And I should not see "GENBANK:AOCU01000000"
    And I should not see "ENA:PRJEB225"

  @ok
  Scenario: Dataset manuscript
    Given I am on "/adminManuscript/admin"
    When I fill in the field of "name" "Manuscript[identifier]" with "2047-217X-1-17"
    And I press return on the element "(//input)[1]"
    And I wait "1" seconds
    Then I should see "23587100"
    And I should not see "23587407"
    And I should not see "23587420"

  @ok
  Scenario: Authors
    Given I am on "/adminAuthor/admin"
    When I fill in the field of "name" "Author[dois_search]" with "100039"
    And I press return on the element "(//input)[5]"
    And I wait "1" seconds
    Then I should see "100039"
    And I should not see "100002"
    And I should not see "100003"

  @ok
  Scenario: Species
    Given I am on "/adminSpecies/admin"
    When I fill in the field of "name" "Species[common_name]" with "penguin"
    And I press return on the element "(//input)[3]"
    And I wait "1" seconds
    Then I should see "Adelie penguin"
    And I should not see "Puerto Rican parrot"
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
    When I fill in the field of "name" "ExternalLink[doi_search]" with "100039"
    And I press return on the element "(//input)[1]"
    And I wait "1" seconds
    Then I should see "100039"
    And I should not see "100004"

  @ok
  Scenario: Link prefixes
    Given I am on "/adminLinkPrefix/admin"
    When I fill in the field of "name" "Prefix[prefix]" with "SRA"
    And I press return on the element "(//input)[1]"
    And I wait "1" seconds
    Then I should see "NCBI"
    And I should not see "EBI"

  @ok
  Scenario: Attribute
    Given I am on "/attribute/admin"
    When I fill in the field of "name" "Attribute[attribute_name]" with "location"
    And I press return on the element "(//input)[2]"
    And I wait "1" seconds
    Then I should see "Geographic"
    And I should not see "sex"
    And I should not see "material"
    And I should not see "keyword"
    And I should not see "urltoredirect"

  @ok
  Scenario: Dataset types
    Given I am on "/adminDatasetType/admin"
    When I fill in the field of "name" "Type[name]" with "nomic"
    And I press return on the element "(//input)[1]"
    And I wait "1" seconds
    Then I should see "Genomic"
    Then I should see "Epigenomic"
    Then I should see "Metagenomic"
    And I should not see "Metadata"
    And I should not see "Software"
    And I should not see "Metabolomic"
    And I should not see "Workflow"
    And I should not see "Proteomic"
    And I should not see "Transcriptomic"
    And I should not see "Imaging"

  @ok
  Scenario: Data types
    Given I am on "/adminFileType/admin"
    When I fill in the field of "name" "FileType[name]" with "Other"
    And I press return on the element "(//input)[1]"
    And I wait "1" seconds
    Then I should see "to be used when no appropriate file type is available"
    And I should not see "Readme"
    And I should not see "Genome sequence"
    And I should not see "Sequence assembly"
    And I should not see "Annotation"
    And I should not see "Protein sequence"
    And I should not see "Repeat sequence"
    And I should not see "Coding sequence"

  @ok
  Scenario: File formats
    Given I am on "/adminFileFormat/admin"
    When I fill in the field of "name" "FileFormat[name]" with "EXCEL"
    And I press return on the element "(//input)[1]"
    And I wait "1" seconds
    Then I should see "(.xls, .xlsx) - Microsoft office spreadsheet files"
    And I should not see "TEXT"
    And I should not see "FASTA"
    And I should not see "GFF"
    And I should not see "TAR"
    And I should not see "PDF"

  @ok
  Scenario: Users
    Given I am on "/user/admin"
    When I fill in the field of "name" "User[email]" with "admin"
    And I press return on the element "(//input)[2]"
    And I wait "1" seconds
    Then I should see "admin@gigadb.org"
    And I should not see "user@gigadb.org"

#  Scenario: Dataset relations (no data)
#  Scenario: Dataset funders (no data)
#  Scenario: Funder (no data)
#  Scenario: News items (no data)
#  Scenario: Publishers (no data)
#  Scenario: Update logs (no data)




