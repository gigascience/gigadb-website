@admin-file @issue-457
  Feature: A curator can manage file attributes in admin file update page
    As a curator,
    I want to manage file attributes from the update form
    So that I can associate various attributes to files

  Background:
    Given Gigadb web site is loaded with production-like data

  @ok @issue-457
  Scenario: Guest user cannot visit admin file update page
    Given I am not logged in to Gigadb web site
    When I go to "/adminFile/update/"
    Then I should see "Login"

  @ok @issue-457
  Scenario: Go to a published dataset found in production-like database
    Given I am not logged in to Gigadb web site
    When I go to "dataset/100056"
    And I should see "Termitomyces sp. J132 fungus genome assembly data."
    And I follow "History"
    Then I should see "History" tab with text "File Termitomyces_assembly_v1.0.fa.gz updated"
    And I should see "History" tab with text "File Termitomyces_gene_v1.0.cds.fa updated"
    And I should see "History" tab with text "File Termitomyces_gene_v1.0.cds.fa updated"
    And I should see "History" tab with text "File Termitomyces_gene_v1.0.gff updated"
    And I should see "History" tab with text "File Termitomyces_gene_v1.0.gff updated"
    And I should see "History" tab with text "File Termitomyces_gene_v1.0.pep.fa updated"
    And I should see "History" tab with text "File Termitomyces_gene_v1.0.pep.fa updated"

  @wip @issue-457 @Published
  Scenario: Sign in as admin and visit admin file update page and see New Attribute, Edit, Delete buttons
    Given I sign in as an admin
    When I am on "/adminFile/update/id/13973"
    Then I should see a button "New Attribute"
    And I should see a file attribute table
      | Attribute Name | Value | Unit |
      | last_modified  | 2013-7-15 |  |
    And I should see a button input "Edit"
    And I should see a button input "Delete"

  @ok @issue-457 @javascript @Published
  Scenario: Sign in as admin, delete an attribute of a published dataset and check history tab
    Given I sign in as an admin
    And I am on "/adminFile/update/id/13973"
    And I should see "last_modified"
    When I press "Delete"
    And I go to "dataset/100056"
    Then I should see "Termitomyces sp. J132 fungus genome assembly data."
    And I follow "History"
    And I should see "History" tab with text "Termitomyces_assembly_v1.0.fa.gz: file attribute deleted"

  @wip @issue-457 @javascript @Published
  Scenario: Sign in as admin, no delete button should be seen after delete action has been triggered
    Given I sign in as an admin
    And I am on "/adminFile/update/id/13973"
    And I should see a file attribute table
    | Attribute Name | Value | Unit |
    | last_modified  | 2013-7-15 |  |
    And I should see a button input "Delete"
    When I press "Delete"
    And I wait "3" seconds
    Then I should not see "last_modified"
    And I should not see "2013-7-15"
    And I should not see a button "Delete"

  @ok @issue-457 @javascript @NonPublished
  Scenario: Go to a non published dataset found in production-like database, create then delete a keyword attribute
    Given I sign in as an admin
    And I am on "/adminFile/update/id/95354"
    And I press "New Attribute"
    And I select "keyword" from "FileAttributes_attribute_id"
    And I fill in "FileAttributes_value" with "test Bauhinia"
    And I press "Add"
    And I should see "test Bauhinia"
    And I press "Delete"
    And I should not see "test Bauhinia"
    #Go to the last page of dataset log
    When I go to "datasetLog/admin/DatasetLog_page/74"
    Then I should not see "100245"
    And I should not see "FCHCGJYBBXX-HKBAUpcgEAACRAAPEI-201_L2_2.fq.gz: file attribute added"
    And I should not see "FCHCGJYBBXX-HKBAUpcgEAACRAAPEI-201_L2_2.fq.gz: file attribute deleted"







