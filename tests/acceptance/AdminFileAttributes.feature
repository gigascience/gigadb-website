Feature: A curator can manage file attributes in admin file update page
  As a curator
  I want to manage file attributes from the update form
  So that I can associate various attributes to files

  @ok
  Scenario: Guest user cannot visit admin file update page
    Given I have not signed in
    When I am on "/adminFile/update/"
    Then I should see "Login"

  @ok
  Scenario: Go to a published dataset
    Given I have not signed in
    When I am on "/dataset/100056"
    And I follow "History"
    Then I should see "File Termitomyces_assembly_v1.0.fa.gz updated"
    And I should see "File Termitomyces_gene_v1.0.cds.fa updated"
    And I should see "File Termitomyces_gene_v1.0.cds.fa updated"
    And I should see "File Termitomyces_gene_v1.0.gff updated"
    And I should see "File Termitomyces_gene_v1.0.gff updated"
    And I should see "File Termitomyces_gene_v1.0.pep.fa updated"
    And I should see "File Termitomyces_gene_v1.0.pep.fa updated"

  @ok @published @wip
  Scenario: See Attribute, Edit, Delete, and Save buttons on admin file update page
    Given I have signed in as admin
    When I am on "/adminFile/update/id/13973"
    Then I should see create new file attribute button
    And I should see a file attribute table
      | Attribute Name | Value     | Unit |
      | last_modified  | 2013-7-15 |      |
#    And I should see a button input "Edit"
#    And I should see a button input "Delete"
#    And I should see a button input "Save"
