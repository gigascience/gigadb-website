Feature: Dataset logging after adding and deleting a file attribute
  As a guest user
  I want to go the dataset's history tab
  So that I can know the date and action of that file attribute has been modified

  @ok @issue-#457
  Scenario: Visit dataset page
    Given I am not logged in to Gigadb web site
    When I go to "/dataset/100006"
    Then I should see "Genomic data from Adelie penguin (<em>Pygoscelis adeliae</em>)"

  @ok @javascript @issue-#457
  Scenario: Go to History tab
    Given I am not logged in to Gigadb web site
    When I go to "/dataset/100006"
    Then I should see "History" tab with text "File Pygoscelis_adeliae.scaf.fa.gz updated"
    And I should see "History" tab with text "Relationship added : DOI 100649"
    And I should see "History" tab with text "Pygoscelis_adeliae.RepeatMasker.out.gz: additional file attribute added"
    And I should see "History" tab with text "Pygoscelis_adeliae.RepeatMasker.out.gz: file attribute deleted"

