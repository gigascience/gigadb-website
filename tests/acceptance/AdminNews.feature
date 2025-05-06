
@ok-can-offline
Feature: News items
  As a curator
  I want to be able to create news items

  Background:
    Given I have signed in as admin

  @ok @issue-208
  Scenario: Create News Items and view them in Latest News in the Home Page
    Given I am on "/news/create"
    When I fill in the field of "name" "News[title]" with "GigaDB new news!"
    And I fill in the field of "name" "News[body]" with "The GigaDB platform now fully supports news items truncated to 4 lines"
    And I fill in the field of "name" "News[start_date]" with "2025-05-02"
    And I fill in the field of "name" "News[end_date]" with "2035-05-02"
    And I press the button "Create"
    And I am on "/"
    Then I should see "GigaDB new news!"
    And I should see "The GigaDB platform now fully supports news items truncated to 4 lines"
