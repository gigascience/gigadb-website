Feature: admin Dashboard
  as a curator
  I want to access and admin dashboard
  So that I can quickly access all the GigaDB object I manage

  Background:
    Given I have signed in as admin

  @ok
  Scenario: Show all available categories
    When I am on "/site/admin"
    Then I should see "Datasets"
    And I should see "Dataset:Authors"
    And I should see "Dataset:Samples"
    And I should see "Dataset:Files"
    And I should see "Dataset:Project links"
    And I should see "Dataset:Links"
    And I should see "Dataset:Relations"
    And I should see "Dataset:Funder"
    And I should see "Dataset:Manuscript"
    And I should see "Authors"
    And I should see "Samples"
    And I should see "Species"
    And I should see "Projects"
    And I should see "External Links"
    And I should see "Link Prefixes"
    And I should see "Funder"
    And I should see "Attribute"
    And I should see "Dataset Types"
    And I should see "Data Types"
    And I should see "File Formats"
    And I should see "Users"
    And I should see "Newsletter Subscribers"
    And I should see "News items"
    And I should see "RSS Messages"
    And I should see "Publishers"
    And I should see "Update Logs"
    And I should not see "Google Analytics"

  @ok
  Scenario: the currently deployed version of GigaDB is shown on the admin dashboard
    When I am on "/site/admin"
    Then I should see the application version
    And I should see a link "" to "https://raw.githubusercontent.com/gigascience/gigadb-website/develop/CHANGELOG.md"


