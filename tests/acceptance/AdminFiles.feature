@ok-can-offline
Feature: Files dashboard
  As a curator
  I want to be able to delete a file from the dashboard
  So that I can quickly manage all the files from the dashboard


  Background:
    Given I have signed in as admin

  @ok
  Scenario: I can delete a file from the dashboard
    Given I am on "/adminFile/admin"
    And I should see "readme.txt"
    When I press the button ".table tbody tr:first-child td:nth-child(8) .icon-delete"
    When I wait "3" seconds
    When I confirm to "Are you sure you want to delete this item?"
    And I wait "1" seconds
    Then I should not see "readme.txt"
