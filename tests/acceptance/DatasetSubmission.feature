@ok-needs-secrets
Feature: Allow dataset updates based on specific conditions
  As an author
  I should only be able to update the dataset if it is not marked as "Published" or "Private"
  So that I can ensure the dataset I modify is the one that will be published

  @ok
  Scenario: Cannot update a dataset if the user is not the owner
    Given I sign in as a user
    When I am on "/datasetSubmission/datasetManagement/id/5"
    Then I should not see "Manage Your Dataset"
    And I should see "You are not the owner of dataset"

  @ok
  Scenario: Cannot update a dataset with published status even by its own owner
    Given I sign in as a user
    When I am on "/datasetSubmission/datasetManagement/id/8"
    Then I should not see "Manage Your Dataset"
    And I should see "You are not the owner of dataset"

  @ok
  Scenario: A dataset can be updated by its owner
    Given I sign in as the user "test+345@gigasciencejournal.com"
    And I am on "/datasetSubmission/datasetManagement/id/5"
    Then I should see "Manage Your Dataset"
    When I attach the file "bgi_logo_new.png" to the file input element by id "Image_image_upload"
    And I fill in the field of "name" "Dataset[title]" with "test dataset"
    And I press the button "Next"
    And I am on "/datasetSubmission/datasetManagement/id/5"
    Then I should see a dataset text field "title" with text "test dataset"
    And I should see an image located in "/images/datasets/4f9a4e84-f68a-5425-a96e-7a533818e12a/bgi-logo-new.png"
