@ok-can-offline
Feature: CLOCKSS permission
  As a third party system
  I want to be allowed to access GigaDB content
  So that I index its content

  @ok
  Scenario: Can read permission
    Given I have not signed in
    When I am on "/lockss.txt"
    Then I should see "CLOCKSS system has permission to ingest, preserve, and serve this open access Archival Unit."
