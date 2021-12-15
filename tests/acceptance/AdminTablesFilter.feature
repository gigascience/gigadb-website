Feature: filter tables on admin page
  As a curator
  I want to filter the tables on the admin pages
  So that I can quickly access the rows I am interested in

  Background:
    Given I have signed in as admin

  @ok
  Scenario: Dataset table
    Given I am on "/adminDataset/admin"
    When I fill in the field of "name" "Dataset[identifier]" with "100005"
    And I press return on the element "(//input)[2]"
    And I wait "1" seconds
    Then I should see "100005"
    And I should not see "100039"
    And I should not see "100002"
    And I should not see "100003"
    And I should not see "100004"

