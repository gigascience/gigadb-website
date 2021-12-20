Feature: A user visit gigadb website
  As a website user
  I want to see useful and consistent navigational controls in the website's static pages area
  So that I can easily navigate in gigadb website

  @ok @issue-873 @issue-874
  Scenario: Terms - GigaDB User Policies
    When I am on "/site/term"
    Then I should see "GigaDB User Policies"
    And I should not see "<em>GigaDB</em> User Policies"