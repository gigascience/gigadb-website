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

  @ok @caching
  Scenario: Dataset page can view the updated location url with caching on
    Given I am on "/dataset/100006"
    When I follow "Files"
    And I should see a link "Pygoscelis_adeliae.RepeatMasker.out.gz" to "https://ftp.cngb.org/pub/gigadb/pub/10.5524/100001_101000/100006/phylogeny_study_update/Pygoscelis_adeliae.RepeatMasker.out.gz"
    Then I am on "/adminFile/update/id/17679"
    And I fill in the field of "id" "File_location" with "https://test.org/phylogeny_study_update/Pygoscelis_adeliae.RepeatMasker.out.gz"
    And I press the button "Save"
    And I am on "/dataset/100006"
    And I follow "Files"
    And I should see a link "Pygoscelis_adeliae.RepeatMasker.out.gz" to "https://test.org/phylogeny_study_update/Pygoscelis_adeliae.RepeatMasker.out.gz"
