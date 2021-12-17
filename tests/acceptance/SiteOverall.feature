Feature: A user visit gigadb website
  As a gigadb user
  I want to see all front end views display properly
  So that I can experience a good performance of gigadb website

  @test @issue-872
  Scenario: Scroll bar is found in guide page
    Given I am on "/site/guide"
    And I should see a table "table_submission"
#    And I make a screenshot called "test-scrollbar"
    When I scroll the table "table_submission" horizontally by "2000" "50"
    Then I make a screenshot called "test-scrollbar2"