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
    And I should see a "Upload new image" button

  @ok
  Scenario: Can display dataset image
    When I am on "/adminDataset/update/id/8"
    Then I should see an image located in "http://gigadb.org/images/data/cropped/100006_Pygoscelis_adeliae.jpg"
    And I should see a "Replace image" button
    And I should see a "Remove image!!!" button