Feature: A user visit gigadb website
  As a website user
  I want to see useful and consistent navigational controls in the website's static pages area
  So that I can experience a good performance of gigadb website

  Background:
    Given Gigadb web site is loaded with production-like data

  @ok
    Scenario: Terms - GigaDB User Policies
      Given I am not logged in to Gigadb web site
      When I go to "/site/term"
      Then I should see "GigaDB User Policies"
      And I should not see "<em>GigaDB</em> User Policies"