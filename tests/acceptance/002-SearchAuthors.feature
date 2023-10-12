# TODO Renaming this test to run first, otherwise it fails. Need to figure out why and fix.
@ok-can-offline
Feature:
  As an author
  I want to save keywords from the search bar
  So that I can have my own search record


  @ok @issue-1186
  Scenario: Hide the Save current search criteria option
    Given I sign in as a user
    And I am on "/"
    And I fill in the field of "id" "keyword" with "penguin"
    And I press the button "Search"
    And I wait "1" seconds
    Then I should see a link "Genomic data from Adelie penguin (Pygoscelis adeliae)." to "/dataset/100006"
    And I should see a link "Pygoscelis_adeliae.scaf.fa.gz" to "https://ftp.cngb.org/pub/gigadb/pub/10.5524/100001_101000/100006/Pygoscelis_adeliae.scaf.fa.gz"
    And I should see "Adelie penguin NCBI taxonomy"
    And I should see "Search again"
    And I should not see an input button "Save current search criteria"