Feature: form to manage file metadata
  As a curator
  I want to access a form to update file metadata
  So that the metadata always reflects changes to the file or file workflow


  Background:
    Given I have signed in as admin

  @ok
  Scenario: Can unset release date
    Given I am on "/adminFile/update/id/17679"
    When I fill in the field of "id" "File_date_stamp" with ""
    And I press the button "Save"
    Then I should see "Not set"

  @ok
  Scenario: Can change release date
    Given I am on "/adminFile/update/id/17679"
    When I fill in the field of "id" "File_date_stamp" with "2022-01-01"
    And I press the button "Save"
    Then I should see "2022-01-01"
