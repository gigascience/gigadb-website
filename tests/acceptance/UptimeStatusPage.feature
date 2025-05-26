Feature:
  As a website user
  I want to know the the server's status of gigadb
  So that the server's status could be monitored

  @ok
  Scenario: Systems Status could be found in the main page
    Given I am on "/"
    When I click the dropdown toggle "dropdown-help"
    Then I should see "Systems Status"

  @ok
  Scenario: Go to uptime status dashboard from the main page
    Given I am on "/"
    When I click the dropdown toggle "dropdown-help"
    And I follow "Systems Status"
    And I go to the new tab
    Then I should see "Service status"

  @ok
  Scenario: Systems Status could be found in the faq page
    Given I am on "/site/faq"
    When I click the dropdown toggle "dropdown-help"
    Then I should see "Systems Status"

  @ok
  Scenario: Go to uptime status dashboard from the faq page
    Given I am on "/site/faq"
    When I click the dropdown toggle "dropdown-help"
    And I follow "Systems Status"
    And I go to the new tab
    Then I should see "Service status"

  @ok
  Scenario: Systems Status could be found in the dataset page
    Given I am on "/dataset/100006"
    When I click the dropdown toggle "dropdown-help"
    Then I should see "Systems Status"

  @ok
  Scenario: Go to uptime status dashboard from the dataset page
    Given I am on "/dataset/100006"
    When I click the dropdown toggle "dropdown-help"
    And I follow "Systems Status"
    And I go to the new tab
    Then I should see "Service status"
