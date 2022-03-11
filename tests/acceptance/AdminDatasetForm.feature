Feature: form to update dataset details
  As a curator
  I want a form to update dataset details
  So that the dataset information is up-to-date

  Background:
    Given I have signed in as admin

  @ok
  Scenario: Can display generic image for no image dataset
    When I am on "/adminDataset/update/id/144"
    Then I should see an image located in "https://assets.gigadb-cdn.net/images/datasets/no_image.png"

  @ok
  Scenario: Can display dataset image
    When I am on "/adminDataset/update/id/8"
    Then I should see an image located in "https://assets.gigadb-cdn.net/live/images/datasets/images/data/cropped/100006_Pygoscelis_adeliae.jpg"

  @ok
  Scenario: Can display generic image in create page
    When I am on "/adminDataset/admin"
    And I press the button "Create Dataset"
    Then I should see "Fields with * are required"
    And I should see an image located in "https://assets.gigadb-cdn.net/images/datasets/no_image.png"