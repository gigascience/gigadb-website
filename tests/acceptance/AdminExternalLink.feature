@ok-needs-secrets
Feature: form to create an external link
  As a curator
  I want to be able to add or update external links to a dataset
  So that ....

  Background:
    Given I have signed in as admin

    @ok
  Scenario: Form loading with all necessary fields
    When I am on "/adminExternalLink/create"
    Then I should see "Additional information"
    And I should see "Genome browser"
    And I should see "Protocols.io"
    And I should see "JBrowse"
    And I should see "3D Models"
    And I should see "Code Ocean"
    And I should see "Authors code repositories"
    And I should see "USCS Tumour Map Viewer"
    And I should see "Software Heritage Archive (SWHA)"
    And I should see "Cited code repository"

  @ok
  Scenario: Will create an orphan SWHA if no origin url has been found for this dataset
    When I am on "/adminExternalLink/create"
    And I select "8" from the field "ExternalLink_dataset_id"
    And I fill in the field of "name" "ExternalLink[url]" with "https://archive.softwareheritage.org/browse/directory/d9323c56a707dc8e9fbea583c86fbec8d40b50c5/?origin_url&#x3D;https://github.com/cihga39871/Atria"
    And I select "11" from the field "ExternalLink_external_link_type_id"
    Then I press the button "Create"
    And I should not see "Related Link"

  @ok
  Scenario: Will remove the related link if no more origin is linked and add the related link is an origin is found again
    When I am on "/adminExternalLink/update/id/15332"
    And I select "8" from the field "ExternalLink_dataset_id"
    Then I press the button "Save"
    And I should not see "Related Link"
    When I am on "/adminExternalLink/update/id/15332"
    And I select "2342" from the field "ExternalLink_dataset_id"
    Then I press the button "Save"
    And I should see "Related Link"

  @ok
  Scenario: It doesn't accept invalid swhid
    When I am on "/adminExternalLink/update/id/15332"
    And I fill in the field of "name" "ExternalLink[url]" with "https://archive.softwareheritage.org/browse/directory/d9323c56a707dc8e9fbea583c86fbec8d40b50c5"
    Then I press the button "Save"
    And I should see "Invalid archived link - no origin url found"


