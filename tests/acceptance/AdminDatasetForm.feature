Feature: form to update dataset details
  As a curator
  I want a form to update dataset details
  So that the dataset information is up-to-date

  Background:
    Given I have signed in as admin

  @ok @issue-381 @issue-926
  Scenario: Form loading with all necessary fields
    When I am on "/adminDataset/update/id/8"
    Then I should see "Submitter *"
    And I should see "Curator Id"
    And I should see "Manuscript Id"
    And I should see "Upload Status"
    And I should see "Epigenomic"
    And I should see "Software"
    And I should see "Genomic"
    And I should see "Metadata"
    And I should see "Dataset Size in Bytes *"
    And I should see "Image Status"
    And I should see "Image URL"
    And I should see "Image Source *"
    And I should see "Image Tag"
    And I should see "Image License *"
    And I should see "Image Photographer *"
    And I should see "DOI *"
    And I should see "Ftp Site *"
    And I should see "Fair Use Policy"
    And I should see "Publication Date"
    And I should see "Modification Date"
    And I should see "Title *"
    And I should see "Description"
    And I should see "Keywords"
    And I should see "URL to redirect"
    And I should see a submit button "Save"
    And I should see a button "Create New Log" with creation log link
    And I should not see "Publisher"

  @ok
  Scenario: Can display generic image, but no image meta data fields for no image dataset in update page
    When I am on "/adminDataset/update/id/144"
    Then I should see an image located in "https://assets.gigadb-cdn.net/images/datasets/no_image.png"
    And I should not see "Remove image"
    And I should not see "Image URL"
    And I should not see "Image Source*"
    And I should not see "Image Tag"
    And I should not see "Image License*"
    And I should not see "Image Photographer*"

  @ok
  Scenario: Can preview uploaded image and display image meta data fields for no image dataset in update page
    When I am on "/adminDataset/update/id/144"
    And I attach the file "bgi_logo_new.png" to the file input element "datasetImage"
    Then I should see an image located in "blob:http://gigadb.test/"
    And I should see "Image URL"
    And I should see "Image Source"
    And I should see "Image Tag"
    And I should see "Image License"
    And I should see "Image Photographer"

  @ok
  Scenario: Can save image to no image dataset update page
    When I am on "/adminDataset/update/id/144"
    And I attach the file "bgi_logo_new.png" to the file input element "datasetImage"
    And I press the button "Save"
    Then I am on "/dataset/100094"
    And I should see an image located in "/images/datasets/bgi_logo_new.png"

  @ok
  Scenario: Can display dataset image, meta data and remove image button in update page
    When I am on "/adminDataset/update/id/8"
    Then I should see an image located in "https://assets.gigadb-cdn.net/live/images/datasets/images/data/cropped/100006_Pygoscelis_adeliae.jpg"
    And I should see a "Remove image" button
    And I should see "Image URL"
    And I should see "Image Source"
    And I should see "Image Tag"
    And I should see "Image License"
    And I should see "Image Photographer"

  @ok
  Scenario: Can preview uploaded image and display image meta data fields update page
    When I am on "/adminDataset/update/id/8"
    And I attach the file "bgi_logo_new.png" to the file input element "datasetImage"
    Then I should see an image located in "blob:http://gigadb.test/"
    And I should not see "Remove image"
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
  Scenario: Can preview image and display image meta data fields when image is loaded in create page
    When I am on "adminDataset/create"
    And I attach the file "bgi_logo_new.png" to the file input element "datasetImage"
    Then I should see an image located in "blob:http://gigadb.test/"
    And I should see "Image URL"
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
    And I fill in the field of "name" "Image[source]" with "test source"
    And I fill in the field of "name" "Image[license]" with "test license"
    And I fill in the field of "name" "Image[photographer]" with "test Joe"
    And I fill in the field of "name" "Dataset[identifier]" with "400789"
    And I fill in the field of "name" "Dataset[ftp_site]" with "ftp://test"
    And I fill in the field of "name" "Dataset[title]" with "test dataset"
    And I press the button "Create"
    Then I wait "1" seconds
    And I should see current url contains "/dataset/400789/token/"
    And I should see an image located in "/images/datasets/bgi_logo_new.png"

  @ok @issue-1023
  Scenario: To confirm the upload status of published dataset has changed to incomplete
    When I am on "/adminDataset/update/id/5"
    Then I should see "Incomplete"
    And I should see "Create/Reset Private URL"
    And I should not see "Open Private URL"

  @ok @issue-1023
  Scenario: An incomplete dataset page cannot be visited publicly
    When I am on "/dataset/100039/"
    Then I should see "The DOI 100039 cannot be displayed. "

  @ok @issue-1023
  Scenario: Can create/reset private url
    When I am on "/adminDataset/update/id/5"
    And I press the button "Create/Reset Private URL"
    And I wait "1" seconds
    Then I should see current url contains "/dataset/100039/token/"
    And I should see "Genomic data of the Puerto Rican Parrot (Amazona vittata) from a locally funded project."

  @ok @issue-1023
  Scenario: Open private url is working
    When I am on "/adminDataset/update/id/5"
    And I press the button "Create/Reset Private URL"
    And I wait "1" seconds
    And I am on "/adminDataset/update/id/5"
    And I follow "Open Private URL"
    Then I should see current url contains "/dataset/100039/token/"
    And I should see "Genomic data of the Puerto Rican Parrot (Amazona vittata) from a locally funded project."

  @ok @issue-1023
  Scenario: Create AuthorReview dataset with token URL
    When I am on "/adminDataset/admin/"
    And I press the button "Create Dataset"
    And I wait "1" seconds
    And I should see "AuthorReview"
    And I select "test+14@gigasciencejournal.com" from the field "Dataset_submitter_id"
    And I fill in the field of "name" "Dataset[dataset_size]" with "1024"
    And I fill in the field of "name" "Dataset[title]" with "test dataset"
    And I fill in the field of "name" "Dataset[identifier]" with "123789"
    And I fill in the field of "name" "Dataset[ftp_site]" with "ftp://test"
    And I press the button "Create"
    And I wait "1" seconds
    Then I should see current url contains "/dataset/123789/token/"
    And I should see "https://doi.org/10.5524/123789"

  @ok @issue-1023
  Scenario: AuthorReview dataset with private URL buttons
    When I am on "/adminDataset/admin/"
    And I press the button "Create Dataset"
    And I wait "1" seconds
    And I should see "AuthorReview"
    And I select "test+14@gigasciencejournal.com" from the field "Dataset_submitter_id"
    And I fill in the field of "name" "Dataset[dataset_size]" with "1024"
    And I fill in the field of "name" "Dataset[title]" with "test dataset"
    And I fill in the field of "name" "Dataset[identifier]" with "123789"
    And I fill in the field of "name" "Dataset[ftp_site]" with "ftp://test"
    And I press the button "Create"
    And I wait "1" seconds
    Then I am on "/adminDataset/update/id/2343"
    And I should see "AuthorReview"
    And I should see "123789"
    And I should see "Create/Reset Private URL"
    And I should see "Open Private URL"

  @ok @issue-1023
  Scenario: Open Private URL from AuthorReview dataset
    When I am on "/adminDataset/admin/"
    And I press the button "Create Dataset"
    And I wait "1" seconds
    And I should see "AuthorReview"
    And I select "test+14@gigasciencejournal.com" from the field "Dataset_submitter_id"
    And I fill in the field of "name" "Dataset[dataset_size]" with "1024"
    And I fill in the field of "name" "Dataset[title]" with "test dataset"
    And I fill in the field of "name" "Dataset[identifier]" with "123789"
    And I fill in the field of "name" "Dataset[ftp_site]" with "ftp://test"
    And I press the button "Create"
    And I wait "1" seconds
    And I am on "/adminDataset/update/id/2343"
    And I follow "Open Private URL"
    And I wait "1" seconds
    Then I should see current url contains "/dataset/123789/token/"
    And I should see "https://doi.org/10.5524/123789"





