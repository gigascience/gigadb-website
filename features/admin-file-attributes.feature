@admin-file @issue-457
  Feature: A curator can manage file attributes in admin file update page
    As a curator,
    I want to manage file attributes from the update form
    So that I can associate various attributes to files

  Background:
    Given Gigadb web site is loaded with production-like data
    And an admin user exists

  @ok
  Scenario: Guest user cannot visit admin file update page
    Given I am not logged in to Gigadb web site
    When I go to "/adminFile/update/"
    Then I should see "Login"

  @ok
  Scenario: Go to a published dataset
    Given I am not logged in to Gigadb web site
    When I go to "/dataset/100056"
    And I should see "Termitomyces sp. J132 fungus genome assembly data."
    And I follow "History"
    Then I should see "History" tab with text "File Termitomyces_assembly_v1.0.fa.gz updated"
    And I should see "History" tab with text "File Termitomyces_gene_v1.0.cds.fa updated"
    And I should see "History" tab with text "File Termitomyces_gene_v1.0.cds.fa updated"
    And I should see "History" tab with text "File Termitomyces_gene_v1.0.gff updated"
    And I should see "History" tab with text "File Termitomyces_gene_v1.0.gff updated"
    And I should see "History" tab with text "File Termitomyces_gene_v1.0.pep.fa updated"
    And I should see "History" tab with text "File Termitomyces_gene_v1.0.pep.fa updated"

  @ok @published
  Scenario: See Attribute, Edit, Delete, and Save buttons on admin file update page
    Given I sign in as an admin
    When I am on "/adminFile/update/id/13973"
    Then I should see a button "New Attribute"
    And I should see a file attribute table
      | Attribute Name | Value     | Unit |
      | last_modified  | 2013-7-15 |      |
    And I should see a button input "Edit"
    And I should see a button input "Delete"
    And I should see a button input "Save"

  @ok @javascript @published
  Scenario:  See the File Attribute Value of the last modified attribute on admin file view page
    Given I sign in as an admin
    When I go to "/adminFile/view/id/13973"
    Then I should see field "File Attribute Value" with "2013-7-15 "


  @ok @javascript @published
  Scenario: Delete a last modified attribute on admin file update page
    Given I sign in as an admin
    And I am on "/adminFile/update/id/13973"
    When I press "Delete"
    Then I should not see "last_modified"
    And I should not see "2013-7-15"
    And I should not see a button "Delete"

  @ok @javascript @published
  Scenario: File Attribute value is empty empty after deleting an attribute and saving
    Given I sign in as an admin
    And I am on "/adminFile/update/id/13973"
    When I press "Delete"
    And I press "Save"
    Then I am on "/adminFile/view/id/13973"
    And I should not see "File Attribute Value"

  @ok @javascript @published
  Scenario: File attribute deletion is recorded in History tab
    Given I sign in as an admin
    And I go to "/adminFile/update/id/13973"
    And I should see a file attribute table
      | Attribute Name | Value     | Unit |
      | last_modified  | 2013-7-15 |      |
    And I should see a button input "Delete"
    When I press "Delete"
    And I press "Save"
    Then I go to "/dataset/100056"
    And I should see "Termitomyces sp. J132 fungus genome assembly data."
    And I follow "History"
    And I should see "History" tab with text "Termitomyces_assembly_v1.0.fa.gz: file attribute deleted"

  @ok @javascript @nonPublished
  Scenario: See a keyword attribute and a camera parameters attribute on admin file update page
    Given I sign in as an admin
    And I am on "/adminFile/update/id/95354"
    And I should see a file attribute table
    | Attribute Name    | Value         | Unit |
    | keyword           | test Bauhinia |      |
    | camera parameters | test photo    |      |

  @ok @javascript @nonPublished
  Scenario: See File Attribute Value on admin file view page
    Given I sign in as an admin
    When I go to "/adminFile/view/id/95354"
    Then I should see field "File Attribute Value" with "test Bauhinia test photo "

  @ok @javascript @nonPublished
  Scenario: Delete a keyword attribute on admin file update page
    Given I sign in as an admin
    And I am on "/adminFile/update/id/95354"
    When I press "Delete"
    Then I should see a file attribute table
    | Attribute Name    | Value         | Unit |
    | camera parameters | test photo    |      |

  @ok @javascript @nonPublished
  Scenario: Delete a keyword attribute and save, then check for File Attribute Value on admin file view page
    Given I sign in as an admin
    And I am on "/adminFile/update/id/95354"
    When I press "Delete"
    And I press "Save"
    Then I am on "/adminFile/view/id/95354"
    And I should see field "File Attribute Value" with "test photo "

  @ok @javascript @nonPublished
  Scenario: Delete all attributes from a non published dataset
    Given I sign in as an admin
    And I am on "/adminFile/update/id/95354"
    When I press "Delete"
    And I press "Delete"
    Then I should not see "test Bauhinia"
    And I should not see "test photo"

  @wip @javascript @nonPublished
  Scenario: Delete all attributes and save, File Attribute Value on admin file view page should be empty
    Given I sign in as an admin
    And I am on "/adminFile/update/id/95354"
    When I press "Delete"
    And I press "Delete"
    And I press "Save"
    Then I am on "/adminFile/view/id/95354"
    And I should not see "File Attribute Value"

  @ok @javascript @nonPublished
  Scenario: Delete a file attribute and check the last page of dataset log
    Given I sign in as an admin
    And I am on "/adminFile/update/id/95354"
    When I press "Delete"
    And I press "Save"
    Then I go to "datasetLog/admin/DatasetLog_page/74"
    And I should not see "100245"
    And I should not see "FCHCGJYBBXX-HKBAUpcgEAACRAAPEI-201_L2_2.fq.gz: file attribute added"
    And I should not see "FCHCGJYBBXX-HKBAUpcgEAACRAAPEI-201_L2_2.fq.gz: file attribute deleted"



