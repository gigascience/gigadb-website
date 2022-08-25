@admin-file @issue-457
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

  @ok @published
  Scenario: See Attribute, Edit, Delete, and Save buttons on admin file update page
    Given I have signed in as admin
    When I am on "/adminFile/update/id/13973"
    Then I should see create new file attribute link button
    And I should see a file attribute table
      | Attribute Name | Value     | Unit |
      | last_modified  | 2013-7-15 |      |
    And I should see edit file attribute link button
    And I should see delete file attribute link button
    And I should see a submit button "Save"

  @ok @javascript @published
  Scenario: See the File Attribute value of the last modified attribute on admin file view page
    Given I have signed in as admin
    When I am on "/adminFile/view/id/13973"
    Then I should see a view file table
      | File Attribute | 2013-7-15 |
    
  #TODO: Fix problem why this test can sometimes randomly fail  
  @ok @javascript @published
  Scenario: Delete a last modified attribute on admin file update page
    Given I have signed in as admin
    And I am on "/adminFile/update/id/13973"
    When I press the button "Delete"
    Then I should not see "last_modified"
    And I should not see "2013-7-15"
    And I should not see delete file attribute link button

  @ok @javascript @published
  Scenario: File Attribute value is empty after deleting an attribute and saving
    Given I have signed in as admin
    And I am on "/adminFile/update/id/13973"
    When I press the button "Delete"
    And I press the button "Save"
    Then I am on "/adminFile/view/id/13973"
    And I should not see "File Attribute"

  @ok @javascript @published
  Scenario: File attribute deletion is recorded in History tab
    Given I have signed in as admin
    When I am on "/adminFile/update/id/13973"
    And I press the button "Delete"
    And I press the button "Save"
    And I am on "/dataset/100056"
    And I should see "Termitomyces sp. J132 fungus genome assembly data."
    And I follow "History"
    Then I should see "Termitomyces_assembly_v1.0.fa.gz: file attribute deleted"

  @ok @javascript @nonPublished
  Scenario: See a keyword attribute and a camera parameters attribute on admin file update page
    Given I have signed in as admin
    When I am on "/adminFile/update/id/95354"
    Then I should see a file attribute table
      | Attribute Name    | Value                            | Unit |
      | keyword           | test Bauhinia                    |      |
      | camera parameters | test photo                       |      |
      | MD5 checksum      | b584eb4ce0947dbf9529acffc3e9f7cc |      |

  @ok @javascript @nonPublished
  Scenario: See File Attribute value on admin file view page
    Given I have signed in as admin
    When I am on "/adminFile/view/id/95354"
    Then I should see a view file table
      | File Attribute | test Bauhinia                    |
      | File Attribute | test photo                       |
      | File Attribute | b584eb4ce0947dbf9529acffc3e9f7cc |

  @ok @javascript @nonPublished
  Scenario: Delete a keyword attribute on admin file update page
    Given I have signed in as admin
    And I am on "/adminFile/update/id/95354"
    When I press the button "Delete"
    Then I should see a file attribute table
      | Attribute Name    | Value                            | Unit |
      | camera parameters | test photo                       |      |
      | MD5 checksum      | b584eb4ce0947dbf9529acffc3e9f7cc |      |

  @ok @javascript @nonPublished
  Scenario: Delete camera parameters attribute and save, then check for File Attribute Value on admin file view page
    Given I have signed in as admin
    And I am on "/adminFile/update/id/95354"
    When I press the button "Delete"
    And I press the button "Save"
    Then I am on "/adminFile/view/id/95354"
    And I should see a view file table
      | File Attribute | test photo                       |
      | File Attribute | b584eb4ce0947dbf9529acffc3e9f7cc |

  @ok @javascript @nonPublished
  Scenario: Delete last MD5 checksum file attribute from a non published dataset
    Given I have signed in as admin
    And I am on "/adminFile/update/id/95354"
    When I press the button "Delete"
    And I press the button "Delete"
    And I press the button "Delete"
    Then I should not see "test Bauhinia"
    And I should not see "test photo"
    And I should not see "b584eb4ce0947dbf9529acffc3e9f7cc"

  @ok @javascript @nonPublished
  Scenario: Check admin file view page is now empty after all file attributes have been deleted
    Given I have signed in as admin
    And I am on "/adminFile/update/id/95354"
    When I press the button "Delete"
    And I press the button "Delete"
    And I press the button "Delete"
    And I press the button "Save"
    Then I am on "/adminFile/view/id/95354"
    And I should not see "File Attribute"
