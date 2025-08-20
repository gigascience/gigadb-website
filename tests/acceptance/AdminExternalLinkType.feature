@ok-needs-secrets
Feature: form to update externalLink types details
  As a curator
  I want a form to update externalLink types details
  So that the externalLink type information is up-to-date

  Background:
    Given I have signed in as admin

    @ok
  Scenario: Admin page loading with all necessary fields
    When I am on "/adminExternalLinkType/admin"
    Then I should see "Create a new External Link Type"
    And I should see "Name"
    And I should see "Description"
    And I should see "Actions"
    And I should see "Additional information"
    And I should see "Genome browser"
    And I should see "JBrowse"

  @ok
  Scenario: Form loading with all necessary fields
    When I am on "/adminExternalLinkType/create"
    Then I should see "Name"
    And I should see "Description"
    And I should see "Prefix"
    And I should see "Displayed as"
    And I should see "Relationship Id"
    And I should see "Can be multiple instances of that external_link per dataset"
    And I should see "ExternalLinkType_multiple" checkbox is unchecked
    And I should see "Can be linked to another external link url"
    And I should see "ExternalLinkType_can_self_referred" checkbox is unchecked

  @ok
  Scenario: Can create a new ExternalLinkType as link
    Given I am on "/adminExternalLinkType/create"
    And I fill in the field of "name" "ExternalLinkType[name]" with "test name"
    And I fill in the field of "name" "ExternalLinkType[description]" with "test description"
    And I select "link" from the field "displayed-as"
    And I select "2" from the field "ExternalLinkType_relationship_id"
    When I press the button "Create"
    Then I should see "View ExternalLinkType"
    And I should see "test name"
    And I should see "test description"
    And I should see "link"
    And I should see "No"

  @ok
  Scenario: Can create a new ExternalLinkType as tab
    Given I am on "/adminExternalLinkType/create"
    And I fill in the field of "name" "ExternalLinkType[name]" with "test name"
    And I fill in the field of "name" "ExternalLinkType[description]" with "test description"
    And I select "tab" from the field "displayed-as"
    And I select "2" from the field "ExternalLinkType_relationship_id"
    When I press the button "Create"
    Then I should see "View ExternalLinkType"
    And I should see "test name"
    And I should see "test description"
    And I should see "tab"
    And I should see "No"

  @ok
  Scenario: Can create a new ExternalLinkType specifying that multiple instances of that external link type per dataset can exists
    Given I am on "/adminExternalLinkType/create"
    And I fill in the field of "name" "ExternalLinkType[name]" with "test name"
    And I fill in the field of "name" "ExternalLinkType[description]" with "test description"
    And I select "link" from the field "displayed-as"
    And I select "2" from the field "ExternalLinkType_relationship_id"
    And I check "ExternalLinkType_multiple" checkbox
    When I press the button "Create"
    Then I should see "View ExternalLinkType"
    And I should see "test name"
    And I should see "test description"
    And I should see "link"
    And I should see "yes"

  @ok
  Scenario: Can't create two externalLinkType with the same name
    Given I am on "/adminExternalLinkType/create"
    And I fill in the field of "name" "ExternalLinkType[name]" with "Additional information"
    And I fill in the field of "name" "ExternalLinkType[description]" with "test description"
    When I press the button "Create"
    Then I should not see "View ExternalLinkType"
    And I should see "duplicate"

  @ok
  Scenario: Can update an ExternalLinkType
    Given I am on "/adminExternalLinkType/update/id/1"
    And I fill in the field of "name" "ExternalLinkType[name]" with "modified"
    When I press the button "Save"
    Then I should see "View ExternalLinkType"
    And I should see "modified"

  @ok
  Scenario: Can't create another externalLink if the externalLinkType is not multiple
    Given I am on "/adminExternalLink/create"
    And I select "22" from the field "ExternalLink_dataset_id"
    And I fill in the field of "name" "ExternalLink[url]" with "test url"
    And I select "2" from the field "ExternalLink_external_link_type_id"
    When I press the button "Create"
    Then I should see "Can't be multiple instances of that external_link per dataset"

  @ok
  Scenario: Can toggle whether it is self-referred
    Given I am on "/adminExternalLinkType/update/id/11"
    And I uncheck "ExternalLinkType_can_self_referred" checkbox
    When I press the button "Save"
    Then I should see "Can Self Referred"
    And I should not see "yes"
