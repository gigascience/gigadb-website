Feature: FAQ Search and Contact Form
  As a Website user
  I want to be able to find info I want easily
  so that I dont get frustrated and leave

  Background:
    Given I am on "/site/faq"

  @ok @issue-193
  Scenario: Search FAQ exists
    Then I should see a field of type "search" with id "faqSearch"

  @ok @issue-193
  Scenario: Search input filters FAQ results
    When I fill in the field of "id" "faqSearch" with "data/files/tools/software"
    And I wait "1" seconds
    Then I should see less than "3" FAQ panels

  @ok @issue-193
  Scenario: Show contact button even if no FAQ results
    When I fill in the field of "id" "faqSearch" with "lorem ipsum dolor sit amet"
    And I wait "1" seconds
    Then I should see "Can't find what you're looking for?"
    And I should see a text field "ContactForm_name"
    And I should see a text field "ContactForm_email"
    And I should see a text field "ContactForm_subject"
    And I should see a text field "ContactForm_body"
