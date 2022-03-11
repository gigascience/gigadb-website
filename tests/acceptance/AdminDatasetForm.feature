Feature: form to update dataset details
  As a curator
  I want a form to update dataset details
  So that the dataset information is up-to-date

  Background:
    Given I have signed in as admin

  @ok
  Scenario: Can display generic image, but no image meta data fields for no image dataset in update page
    When I am on "/adminDataset/update/id/144"
    Then I should see an image located in "https://assets.gigadb-cdn.net/images/datasets/no_image.png"
    And I should not see "Remove image!!!"
    And I should not see "Image URL"
    And I should not see "Image Source*"
    And I should not see "Image Tag"
    And I should not see "Image License*"
    And I should not see "Image Photographer*"

  @wip
  Scenario: Can preview uploaded image for no image dataset in update page
    When I am on "/adminDataset/update/id/144"
    And I attach the file "bgi_logo_new.png" to the file input element "datasetImage"
    Then I should see an image located in "blob:http://gigadb.gigasciencejournal.com:9170/6470ad8c-c6ad-40b6-b498-39ac4f34219f"

  @ok
  Scenario: Can save image and display image meta data fields to no image dataset update page
    When I am on "/adminDataset/update/id/144"
    And I attach the file "bgi_logo_new.png" to the file input element "datasetImage"
    And I should see "Image URL"
    And I should see "Image Source"
    And I should see "Image Tag"
    And I should see "Image License"
    And I should see "Image Photographer"
    And I press the button "Save"
    Then I am on "/dataset/100094"
    And I should see an image located in "/files/dev/images/datasets/bgi_logo_new.png"

  @ok
  Scenario: Can display dataset image, meta data and remove image button in update page
    When I am on "/adminDataset/update/id/8"
    Then I should see an image located in "https://assets.gigadb-cdn.net/live/images/datasets/images/data/cropped/100006_Pygoscelis_adeliae.jpg"
    And I should see a "Remove image!!!" button
    And I should see "Image URL"
    And I should see "Image Source"
    And I should see "Image Tag"
    And I should see "Image License"
    And I should see "Image Photographer"

  @ok
  Scenario: No meta image data fields when no image is loaded in create page
    When I am on "/adminDataset/admin"
    And I press the button "Create Dataset"
    Then I should see "Fields with * are required"
    And I should see an image located in "https://assets.gigadb-cdn.net/images/datasets/no_image.png"
    And I should not see "Image URL"
    And I should not see "Image Source"
    And I should not see "Image Tag"
    And I should not see "Image License"
    And I should not see "Image Photographer"

  @ok
  Scenario: Display image meta data fields when image is loaded in create page
    When I am on "adminDataset/create"
    And I attach the file "bgi_logo_new.png" to the file input element "datasetImage"
    Then I should see "Image URL"
    And I should see "Image Source"
    And I should see "Image Tag"
    And I should see "Image License"
    And I should see "Image Photographer"

  @ok
  Scenario: Can create dataset with image
    When I am on "adminDataset/create"
    And I select "test+14@gigasciencejournal.com" from the field "Dataset_submitter_id"
    And I fill in the field of "name" "Dataset[dataset_size]" with "1024"
    And I attach the file "bgi_logo_new.png" to the file input element "datasetImage"
    And I fill in the field of "name" "Image[url]" with "/files/dev/images/datasets/bgi_logo_new.png"
    And I fill in the field of "name" "Image[source]" with "test source"
    And I fill in the field of "name" "Image[license]" with "test license"
    And I fill in the field of "name" "Image[photographer]" with "test Joe"
    And I fill in the field of "name" "Dataset[identifier]" with "400789"
    And I fill in the field of "name" "Dataset[ftp_site]" with "ftp://test"
    And I fill in the field of "name" "Dataset[title]" with "test dataset"
    And I press the button "Create"
    Then I am on "dataset/view/id/400789"
    And I should see an image located in "/files/dev/images/datasets/bgi_logo_new.png"


    
