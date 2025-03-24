@ok-needs-secrets
Feature: form to manage manuscripts
  As a curator
  I want to access a form to create/update a manuscript
  So I can create or update a manuscript


  Background:
    Given I have signed in as admin

  @ok
  Scenario: Can see all the required fields with the checkbox unchecked by default
    When I am on "/adminManuscript/create"
    Then I should see a text field "Manuscript_identifier"
    And I should see a text field "Manuscript_pmid"
    And I should see "Dataset *"
    And I should see a check-box field "Manuscript_is_pre_print"
    And I should see "Manuscript_is_pre_print" checkbox is not checked

  @ok
  Scenario: Can create a manuscript and not consider it a pre print link
    Given I am on "/adminManuscript/create"
    And I fill in the field of "name" "Manuscript[identifier]" with "test"
    And I fill in the field of "name" "Manuscript[pmid]" with "123"
    And I select "8" from the field "Manuscript_dataset_id"
    When I press the button "Create"
    Then I should see "View Manuscript"
    And I should see "This link is a pre-print"
    And I should see "No"

  @ok
  Scenario: Can create a manuscript and consider it a pre print link
    Given I am on "/adminManuscript/create"
    And I fill in the field of "name" "Manuscript[identifier]" with "test"
    And I fill in the field of "name" "Manuscript[pmid]" with "123"
    And I select "8" from the field "Manuscript_dataset_id"
    And I check the field "Manuscript_is_pre_print"
    When I press the button "Create"
    Then I should see "View Manuscript"
    And I should see "This link is a pre-print"
    And I should see "Yes"

  @ok
  Scenario: Can update a manuscript
    Given I am on "/adminManuscript/update/id/41"
    And  I should see "Manuscript_is_pre_print" checkbox is unchecked
    And I check the field "Manuscript_is_pre_print"
    And I fill in the field of "name" "Manuscript[identifier]" with "test"
    And I fill in the field of "name" "Manuscript[pmid]" with "123"
    When I press the button "Save"
    Then I should see "View Manuscript"
    And I should see "This link is a pre-print"
    And I should see "Yes"
    And I should see "test"
    And I should see "123"
