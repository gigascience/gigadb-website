@ok-can-offline
Feature: admin page for dataset relations
  as a curator
  I want to see a table of all relations
  So that quickly navigate to the relation related data I am interested in

  Background:
    Given I have signed in as admin

  @ok
  Scenario: Add a relation and the reciprocal relation
    Given I am on "/adminRelation/create"
    And I should see "Create Relation"
    When I select "8" from the field "Relation_dataset_id"
    And I select "100039" from the field "Relation_related_doi"
    And I select "1" from the field "Relation_relationship_id"
    And I press the button "Create"
    And I wait "1" seconds
    Then I should see "View Relation"
    And I should see "8"
    And I should see "100039"
    Then I am on "adminRelation/admin"
    Then I should see the table with the following rows:
      | 100039  | 100006      | Cites             |
      | 100006  | 100039      | IsCitedBy         |

  @ok
  Scenario: Add a relation without the reciprocal relation
    Given I am on "/adminRelation/create"
    And I should see "Create Relation"
    When I select "8" from the field "Relation_dataset_id"
    And I select "100039" from the field "Relation_related_doi"
    And I select "1" from the field "Relation_relationship_id"
    Then I uncheck "Relation_add_reciprocal" checkbox
    And I press the button "Create"
    And I wait "1" seconds
    Then I should see "View Relation"
    And I should see "8"
    And I should see "100039"
    Then I am on "adminRelation/admin"
    Then I should not see the table with the following index 0:
      | 100039  | 100006 | Cites |

  @ok
  Scenario: Fail To save relation with same DOI
    Given I am on "/adminRelation/create"
    And I should see "Create Relation"
    When I select "5" from the field "Relation_dataset_id"
    And I select "100039" from the field "Relation_related_doi"
    And I select "1" from the field "Relation_relationship_id"
    And I press the button "Create"
    And I wait "1" seconds
    Then I should see "Can't refer the same DOI"
