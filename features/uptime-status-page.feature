@issue-739
Feature:
  As a gigadb user
  I want to know the the server's status of gigadb
  So that the server's status could be monitored

  Background:
    Given Gigadb web site is loaded with production-like data

  @ok
  Scenario: Systems Status could be found in the main page
    Given I am not logged in to Gigadb web site
    When I go to "/index.php"
    And I click on the "Help" button
    Then I should see "Systems Status"

  @ok
  Scenario: Go to uptime status dashboard from the main page
    Given I am not logged in to Gigadb web site
    And I go to "/index.php"
    And I click on the "Help" button
    And I click on the "Systems Status" button
    Then I am on "https://stats.uptimerobot.com/LGVQXSkN1y"
    And I should see "GigaDB"

  @ok
  Scenario: Systems Status could be found in the faq page
    Given I am not logged in to Gigadb web site
    When I go to "/site/faq"
    And I click on the "Help" button
    Then I should see "Systems Status"

  @ok
  Scenario: Go to uptime status dashboard from the faq page
    Given I am not logged in to Gigadb web site
    And I go to "/site/faq"
    And I click on the "Help" button
    And I click on the "Systems Status" button
    Then I am on "https://stats.uptimerobot.com/LGVQXSkN1y"
    And I should see "GigaDB"

  @ok
  Scenario: Systems Status could be found in the dataset page
    Given I am not logged in to Gigadb web site
    When I go to "/dataset/100016"
    And I click on the "Help" button
    Then I should see "Systems Status"

  @ok
  Scenario: Go to uptime status dashboard from the dataset page
    Given I am not logged in to Gigadb web site
    And I go to "/dataset/100016"
    And I click on the "Help" button
    And I click on the "Systems Status" button
    Then I am on "https://stats.uptimerobot.com/LGVQXSkN1y"
    And I should see "GigaDB"