@ok-can-offline
Feature: Dataset dashboard
  As a curator
  I want the list of datasets to show in a useful way
  So that I can easily see those datasets relevant to me

  Background:
    Given I have signed in as admin
    And I am on "/adminDataset/admin"

  @ok
  Scenario: Default sorting by ID in descending order
    Then I should see the table is sorted by column "ID" in the "desc" order

  @ok
  Scenario: No modification date column
    Then I should not see "th" with text "Modification date"

  @ok
  Scenario: Curator column presence
    Then I should see "th" with text "Curator"

  @ok
  Scenario: Upload Status column sorting ascending
    When I follow "Upload Status"
    And I wait "1" seconds
    Then I should see the table is sorted by column "Upload Status" in the "asc" order

  @ok
  Scenario: Curator column sorting ascending
    When I follow "Curator"
    And I wait "1" seconds
    Then I should see the table is sorted by column "Curator" in the "asc" order

  @ok
  Scenario: Filter by Curator
    When I fill in the field of "name" "Dataset[curator_id]" with "Chris A"
    And I press return on the element "(//input)[6]"
    And I wait "1" seconds
    Then I should see "tbody td" with text "Chris A"
    And I should not see "tbody td" with text "C H"

  @ok
  Scenario: Filter by Upload Status
    When I fill in the field of "name" "Dataset[upload_status]" with "Published"
    And I press return on the element "(//input)[7]"
    And I wait "1" seconds
    Then I should see "tbody td" with text "Published"
    And I should not see "tbody td" with text "Unpublished"