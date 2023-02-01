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


  @ok @datasetimage
  Scenario: Can preview uploaded image and display image meta data fields for no image dataset in update page
    When I am on "/adminDataset/update/id/144"
    And I attach the file "bgi_logo_new.png" to the file input element "datasetImage"
    Then I should see an image located in "blob:http://gigadb.test/"
    And I should see "Image URL"
    And I should see "Image Source"
    And I should see "Image Tag"
    And I should see "Image License"
    And I should see "Image Photographer"

  @ok @datasetimage
  Scenario: Can save image to no image dataset update page
    When I am on "/adminDataset/update/id/144"
    And I attach the file "bgi_logo_new.png" to the file input element "datasetImage"
    And I press the button "Save"
    Then I am on "/dataset/100094"
    And I should see an image located in "/images/datasets/9febbdcf-3f7c-5558-abaa-448e633a109d/bgi_logo_new.png"

  @ok @datasetimage
  Scenario: Can display dataset image, meta data and remove image button in update page
    When I am on "/adminDataset/update/id/8"
    Then I should see an image located in "https://assets.gigadb-cdn.net/live/images/datasets/images/data/cropped/100006_Pygoscelis_adeliae.jpg"
    And I should see "Remove image"
    And I should see "Image URL"
    And I should see "Image Source"
    And I should see "Image Tag"
    And I should see "Image License"
    And I should see "Image Photographer"

  @ok @datasetimage
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

  @ok @datasetimage
  Scenario: No meta image data fields when no image is loaded in create page
    When I am on "/adminDataset/admin"
    And I press the button "Create Dataset"
    Then I should see "Fields with * are required"
    And I should not see "Image URL"
    And I should not see "Image Source"
    And I should not see "Image Tag"
    And I should not see "Image License"
    And I should not see "Image Photographer"

  @ok @datasetimage
  Scenario: Can preview image and display image meta data fields when image is loaded in create page
    When I am on "adminDataset/create"
    And I attach the file "bgi_logo_new.png" to the file input element "datasetImage"
    Then I should see an image located in "blob:http://gigadb.test/"
    And I should see "Image URL"
    And I should see "Image Source"
    And I should see "Image Tag"
    And I should see "Image License"
    And I should see "Image Photographer"

  @ok @datasetimage
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
    Then I am on "dataset/view/id/400789"
    And I should see an image located in "/images/datasets/e166c2a0-3684-5209-bccd-c4b18ff87be9/bgi_logo_new.png"

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

  @ok
  Scenario:  Can remove custom image
    When I am on "/adminDataset/update/id/200"
    And I follow "Remove image"
    And I confirm to "Are you sure? This will take effect immediately"
    And I wait "1" seconds
    Then I should not see "Image URL"
    And I should not see "Image Source"
    And I should not see "Image Tag"
    And I should not see "Image License"
    And I should not see "Image Photographer"
    And I should see an image located in "/images/datasets/no_image.png"

  @ok
  Scenario: Can remove custom image and immediately upload a new image
    When I am on "/adminDataset/update/id/22"
    And I follow "Remove image"
    And I confirm to "Are you sure? This will take effect immediately"
    And I wait "1" seconds
    And I attach the file "bgi_logo_new.png" to the file input element "datasetImage"
    And I wait "1" seconds
    Then I should see an image located in "blob:http://gigadb.test/"
    And I should not see "Remove image"
    And I should see "Image URL"
    And I should see "Image Source"
    And I should see "Image Tag"
    And I should see "Image License"
    And I should see "Image Photographer"


  @ok @datasetimage
  Scenario: No image, but metadata only is shown if image record's url is not valid url
    When I am on "/adminDataset/update/id/668"
    Then I should see an image located in ""
    And I should see "Remove image"
    And I should see "Image URL"
    And I should see "Image Source"
    And I should see "Image Tag"
    And I should see "Image License"
    And I should see "Image Photographer"

  @ok @datasetimage
  Scenario: Delete dataset's existing custom image file (but not the image metadata)
    When I am on "/adminDataset/update/id/700"
    And I press the button "[x]"
    And I confirm to "Are you sure? This will take effect immediately"
    And I wait "1" seconds
    Then I should see "Image URL"
    And I should see "Image Source"
    And I should see "Image Tag"
    And I should see "Image License"
    And I should see "Image Photographer"
    And I should see an image located in ""
    And I should not see "[x]"
    And I should see "Remove image"

  @ok @datasetimage
  Scenario: Delete an image's file and then remove the image record
    When I am on "/adminDataset/update/id/5"
    And I press the button "[x]"
    And I confirm to "Are you sure? This will take effect immediately"
    And I wait "2" seconds
    And I follow "Remove image"
    And I confirm to "Are you sure? This will take effect immediately"
    And I wait "1" seconds
    Then I should not see "Image URL"
    And I should not see "Image Source"
    And I should not see "Image Tag"
    And I should not see "Image License"
    And I should not see "Image Photographer"
    And I should see an image located in "/images/datasets/no_image.png"
    And I should not see "[x]"

  @ok
  Scenario: can save keywords on update
    When I am on "/adminDataset/update/id/8"
    And I click on keywords field
    And I fill in keywords fields with "bam"
    And I press the button "Save"
    Then I am on "dataset/100006"
    And I should see "bam"

  @ok @curationlog
  Scenario: Create new curation log record for a dataset
    When I am on "/adminDataset/update/id/5"
    And I press the button "Create New Log"
    And I am on "/curationLog/create/id/5"
    And I should see "Create Curation Log"
    And I select "Comment" from the field "CurationLog_action"
    And I fill in the field of "name" "CurationLog[comments]" with "hello world"
    And I press the button "Create"
    Then I am on "/curationLog/view/id/1"
    And I should see "View Curation Log #1"
    And I should see "hello world"

  @ok @curationlog
  Scenario: Click view curation record image with link
    When I am on "/adminDataset/update/id/22"
    And I should see an image with alternate text "View" is linked to "http://gigadb.test/curationlog/view/id/3"
    And I click on image with alternate text "View"
    Then I am on "/curationlog/view/id/3"
    And I should see "View Curation Log #3"
    And I should see a link "Back to this Dataset Curation Log" to "http://gigadb.test/adminDataset/update/id/22"

  @ok @curationlog
  Scenario: Click update curation record image with link
    When I am on "/adminDataset/update/id/22"
    And I should see an image with alternate text "Update" is linked to "http://gigadb.test/curationlog/update/id/3"
    And I click on image with alternate text "Update"
    Then I am on "/curationlog/update/id/3"
    And I should see "Update Curation Log 3"

  @ok @curationlog
  Scenario: Click delete curation record image with link
    When I am on "/adminDataset/update/id/22"
    And I should see "Status changed to Published"
    And I should see an image with alternate text "Delete" is linked to "http://gigadb.test/curationlog/delete/id/3"
    And I click on image with alternate text "Delete"
    And I confirm to "Are you sure you want to delete this item?"
    And I wait "2" seconds
    And I make a screenshot called "delete_popup"
    Then I am on "/adminDataset/update/id/22"
    And I should not see "Status changed to Published"
