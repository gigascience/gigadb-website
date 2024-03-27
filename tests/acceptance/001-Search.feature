# TODO Renaming this test to run first, otherwise it fails. Need to figure out why and fix.
@ok-can-offline
Feature: main search function
  As a website user
  I want to be able to search GigaDB
  So that I can find the information I need

  @ok
  Scenario: basic search
    Given I am on "/"
    When I fill in the field of "id" "keyword" with "penguin"
    And I press the button "Search"
    And I wait "1" seconds
    Then I should see a link "Genomic data from Adelie penguin (Pygoscelis adeliae)." to "/dataset/100006"
    And I should see a link "Pygoscelis_adeliae" to "/dataset/100006"
    And I should see the files:
    | download link title | download link url| file type | size |
    | Pygoscelis_adeliae.pep.gz | https://ftp.cngb.org/pub/gigadb/pub/10.5524/100001_101000/100006/phylogeny_study_update/Pygoscelis_adeliae.pep.gz              | Protein sequence | 4.17 MiB |
    | Pygoscelis_adeliae.gff.gz | https://ftp.cngb.org/pub/gigadb/pub/10.5524/100001_101000/100006/phylogeny_study_update/Pygoscelis_adeliae.gff.gz | Annotation       | 1.59 MiB   |
    | Pygoscelis_adeliae.fa.gz | https://ftp.cngb.org/pub/gigadb/pub/10.5524/100001_101000/100006/phylogeny_study_update/Pygoscelis_adeliae.fa.gz | Sequence assembly | 350.48 MiB |
    | Pygoscelis_adeliae.RepeatMasker.out.gz | https://ftp.cngb.org/pub/gigadb/pub/10.5524/100001_101000/100006/phylogeny_study_update/Pygoscelis_adeliae.RepeatMasker.out.gz | Repeat sequence | 7.49 MiB |
    | Pygoscelis_adeliae.cds.gz | https://ftp.cngb.org/pub/gigadb/pub/10.5524/100001_101000/100006/phylogeny_study_update/Pygoscelis_adeliae.cds.gz              | Coding sequence | 6.43 MiB |
    | Pygoscelis_adeliae.scaf.fa.gz | https://ftp.cngb.org/pub/gigadb/pub/10.5524/100001_101000/100006/Pygoscelis_adeliae.scaf.fa.gz | Sequence assembly | 350.61 MiB |
    | readme.txt | https://ftp.cngb.org/pub/gigadb/pub/10.5524/100001_101000/100006/readme.txt | Readme | 138 B |

  @todo @broken
  Scenario: pagination show correct number of pages
    Given I am on "/"
    When I fill in the field of "id" "keyword" with "genome"
    And I press the button "Search"
    And I wait "1" seconds
    Then I should see "Showing 1 - 2 of 6 datasets"
    And I should see a link "Three Bauhinia species transcriptome sequence data" to "/dataset/100245"
    And I should see a link "Data and software to accompany the paper: Applying compressed sensing to genome-wide association studies." to "/dataset/100094"
    And I should not see "Genome data from foxtail millet (Setaria italica)."
    And I should not see "Genomic data from Adelie penguin (Pygoscelis adeliae)."
    And I should see a link "1" to ""
    And I should see a link "2" to ""
    And I should see a link "3" to ""

  @todo @broken
  Scenario: Can navigate to the next page
    Given I am on "/"
    And I fill in the field of "id" "keyword" with "genome"
    And I press the button "Search"
    And I wait "1" seconds
    When I follow "2"
    Then I should see a link "Termitomyces sp. J132 fungus genome assembly data." to "/dataset/100056"
    And I should not see "Data and software to accompany the paper: Applying compressed sensing to genome-wide association studies."

  @ok
  Scenario: Can search compound term without operator
    Given I am on "/"
    When I fill in the field of "id" "keyword" with "penguin readme"
    And I press the button "Search"
    And I wait "1" seconds
    Then I should see "Showing 1 - 1 of 1 datasets"
    And I should see a link "Genomic data from Adelie penguin (Pygoscelis adeliae)." to "/dataset/100006"
    And I should see the files:
      | download link title | download link url| file type | size |
      | readme.txt | https://ftp.cngb.org/pub/gigadb/pub/10.5524/100001_101000/100006/readme.txt | Readme | 138 B |
    And I should not see "Pygoscelis_adeliae."


  @ok
  Scenario: Can search compound term with operator
    Given I am on "/"
    When I fill in the field of "id" "keyword" with "penguin & readme"
    And I press the button "Search"
    And I wait "1" seconds
    Then I should see "Showing 1 - 1 of 1 datasets"
    And I should see a link "Genomic data from Adelie penguin (Pygoscelis adeliae)." to "/dataset/100006"
    And I should see the files:
      | download link title | download link url| file type | size |
      | readme.txt | https://ftp.cngb.org/pub/gigadb/pub/10.5524/100001_101000/100006/readme.txt | Readme | 138 B |
    And I should not see "Pygoscelis_adeliae."

  @ok
  Scenario: Can search compound term with double quotes
    Given I am on "/"
    When I fill in the field of "id" "keyword" with "\"penguin readme\""
    And I press the button "Search"
    And I wait "1" seconds
    Then I should see "Showing 1 - 1 of 1 datasets"
    And I should see a link "Genomic data from Adelie penguin (Pygoscelis adeliae)." to "/dataset/100006"
    And I should see the files:
      | download link title | download link url| file type | size |
      | readme.txt | https://ftp.cngb.org/pub/gigadb/pub/10.5524/100001_101000/100006/readme.txt | Readme | 138 B |
    And I should not see "Pygoscelis_adeliae."

  @ok
  Scenario: can limit search to specific year
    Given I am on "/"
    When I fill in the field of "id" "keyword" with "genome & 2011"
    And I press the button "Search"
    And I wait "1" seconds
    Then I should see "Showing 1 - 2 of 2 datasets"
    And I should see a link "Genomic data from Adelie penguin (Pygoscelis adeliae)." to "/dataset/100006"
    And I should see a link "Genome data from foxtail millet (Setaria italica)." to "/dataset/100020"

  @ok
  Scenario: can limit search to specific month
    Given I am on "/"
    When I fill in the field of "id" "keyword" with "genome & 2011-11"
    And I press the button "Search"
    And I wait "1" seconds
    Then I should see "Showing 1 - 1 of 1 datasets"
    And I should see a link "Genome data from foxtail millet (Setaria italica)." to "/dataset/100020"
    And I should not see "Genomic data from Adelie penguin (Pygoscelis adeliae)."

  @ok
  Scenario: can limit search to specific day
    Given I am on "/"
    When I fill in the field of "id" "keyword" with "genome & 2011-07-06"
    And I press the button "Search"
    And I wait "1" seconds
    Then I should see "Showing 1 - 1 of 1 datasets"
    And I should see a link "Genomic data from Adelie penguin (Pygoscelis adeliae)." to "/dataset/100006"
    And I should not see "Genome data from foxtail millet (Setaria italica)."

  @ok
  Scenario: Limit results to datasets
    Given I am on "/"
    When I fill in the field of "id" "keyword" with "penguin"
    And I press the button "Search"
    And I wait "1" seconds
    And I check the field "type_0"
    And I press the button "Apply Filter"
    And I wait "1" seconds
    Then I should see a link "Genomic data from Adelie penguin (Pygoscelis adeliae)." to "/dataset/100006"
    And I should not see "Pygoscelis_adeliae"

  @ok
  Scenario: Limit results to samples
    Given I am on "/"
    When I fill in the field of "id" "keyword" with "penguin"
    And I press the button "Search"
    And I wait "1" seconds
    And I check the field "type_1"
    And I press the button "Apply Filter"
    And I wait "1" seconds
    Then I should see a link "Genomic data from Adelie penguin (Pygoscelis adeliae)." to "/dataset/100006"
    Then I should see a link "Pygoscelis_adeliae" to "/dataset/100006"
    And I should not see "Pygoscelis_adeliae."


  @ok
  Scenario: Limit results to files
    Given I am on "/"
    When I fill in the field of "id" "keyword" with "penguin"
    And I press the button "Search"
    And I wait "1" seconds
    And I check the field "type_2"
    And I press the button "Apply Filter"
    And I wait "1" seconds
    Then I should see a link "Genomic data from Adelie penguin (Pygoscelis adeliae)." to "/dataset/100006"
    Then I should see a link "Pygoscelis_adeliae.scaf.fa.gz" to "https://ftp.cngb.org/pub/gigadb/pub/10.5524/100001_101000/100006/Pygoscelis_adeliae.scaf.fa.gz"
    And I should not see "Adelie penguin NCBI taxonomy"

  @ok
  Scenario: Show a message when nothing is found
    Given I am on "/"
    When I fill in the field of "id" "keyword" with "teletubbies"
    And I press the button "Search"
    And I wait "1" seconds
    Then I should see "No results found for 'teletubbies'"

  @ok
  Scenario: Query for specific dataset type
    Given I am on "/"
    When I follow "Software"
    And I wait "1" seconds
    Then I should see a link "Data and software to accompany the paper: Applying compressed sensing to genome-wide association studies." to "/dataset/100094"

  @ok
  Scenario: Search for a term that is only in dataset types
    Given I am on "/"
    And I fill in the field of "id" "keyword" with "epigenomic"
    And I press the button "Search"
    And I wait "1" seconds
    Then I should see a link "Genome data from foxtail millet (Setaria italica)." to "/dataset/100020"

#  @error @todo
#  Scenario: Query for specific author id
#    Given I am on "/dataset/100006"
#    When I follow "Lambert DM"
#    And I wait "5" seconds
#    Then I should see a link "Genomic data from Adelie penguin (Pygoscelis adeliae)." to "/dataset/100006"