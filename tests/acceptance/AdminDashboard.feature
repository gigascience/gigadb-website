@ok-needs-secrets
Feature: admin Dashboard
  as a curator
  I want to access and admin dashboard
  So that I can quickly access all the GigaDB object I manage

  Background:
    Given I have signed in as admin

  @ok
  Scenario: the currently deployed version of GigaDB is shown on the admin dashboard
    When I am on "/site/admin"
    Then I should see the application version
    And I should see a link "" to "https://raw.githubusercontent.com/gigascience/gigadb-website/develop/CHANGELOG.md"


