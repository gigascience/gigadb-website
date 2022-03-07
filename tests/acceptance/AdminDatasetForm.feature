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
    Then I should see an image located in "http://gigadb.org/images/data/cropped/100006_Pygoscelis_adeliae.jpg"
    And I should see a "Remove image!!!" button

  @ok
  Scenario: Can save image to no image dataset
    When I am on "/adminDataset/update/id/144"
    And I attach the file "bgi_logo_new.png" to the file input element "datasetImage"
    And I press the button "Save"
    Then I am on "/dataset/100094"
    And I should see an image located in "/files/dev/images/datasets/bgi_logo_new.png"

  @ok
  Scenario: Can create dataset with image
    When I am on "/adminDataset/admin"
    And I press the button "Create Dataset"
    And I should see "Fields with * are required"
    And I select "test+14@gigasciencejournal.com" from the field "Dataset_submitter_id"
    And I fill in the field of "name" "Dataset[dataset_size]" with "1024"
    And I attach the file "bgi_logo_new.png" to the file input element "datasetImage"
    And I fill in the field of "name" "Image[source]" with "test source"
    And I fill in the field of "name" "Image[license]" with "test license"
    And I fill in the field of "name" "Image[photographer]" with "test Joe"
    And I fill in the field of "name" "Dataset[identifier]" with "400789"
    And I fill in the field of "name" "Dataset[ftp_site]" with "ftp://test"
    And I fill in the field of "name" "Dataset[title]" with "test dataset"
    And I press the button "Create"
    Then I am on "dataset/view/id/400789"
    And I should see an image located in "/files/dev/images/datasets/bgi_logo_new.png"


    
