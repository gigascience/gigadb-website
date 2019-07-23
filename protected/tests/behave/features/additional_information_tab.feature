# Created by serhi at 6/3/2019
Feature: Add Additional Information page

  Scenario: I click No for Public data archive links
    Given I am on "site/login" and I login
    Given url address "/datasetSubmission/additionalManagement/id/322"
    When I click No button for Public data archive links
    Then "Related GigaDB Datasets" block appears


  Scenario: I click Yes for Public data archive links
    Given I am on "site/login" and I login
    Given url address "/datasetSubmission/additionalManagement/id/322"
    When I click Yes button for Public data archive links
    Then Database dropdown menu appears

  Scenario: And a database is selected from dropdown Accession number field appears on "Public data archive links" block
    Given I am on "site/login" and I login
    Given url address "/datasetSubmission/additionalManagement/id/322"
    When I click Yes button for Public data archive links
    And choose №"2" from dropdown list
    Then "Accession number" field appears


  Scenario: I click No button for Related GigaDB Datasets
    Given I am on "site/login" and I login
    Given url address "/datasetSubmission/additionalManagement/id/322"
    When I click No button for Public data archive links
    And I click 'no' button for Related GigaDB Datasets
    Then "Collaboration links" block appears


  Scenario: I click Yes button for Related GigaDB Datasets
    Given I am on "site/login" and I login
    Given url address "/datasetSubmission/additionalManagement/id/322"
    When I click No button for Public data archive links
    And I click 'yes' button for Related GigaDB Datasets
    Then "Add Related Doi" button appears on Related GigaDB Datasets block

  Scenario: I click No button for Project links
    Given I am on "site/login" and I login
    Given url address "/datasetSubmission/additionalManagement/id/322"
    When I click No button for Public data archive links
    And I click 'no' button for Related GigaDB Datasets
    And I click 'no' button for Project links
    Then "Other links" block appears


  Scenario: I click Yes button for Project links
    Given I am on "site/login" and I login
    Given url address "/datasetSubmission/additionalManagement/id/322"
    When I click No button for Public data archive links
    And I click 'no' button for Related GigaDB Datasets
    And I click 'yes' button for Project links
    Then 'Add Project' button with 'project' dropdown menu appears

  Scenario: I click No button for all Other links I click Next button
    Given I am on "site/login" and I login
    Given url address "/datasetSubmission/additionalManagement/id/322"
    When I click No button for Public data archive links
    And I click 'no' button for Related GigaDB Datasets
    And I click 'no' button for Project links
    And I click "no" button for "A published manuscript that uses this data"
    And I click "no" button for "Protocols.io link to methods used to generate this data"
    And I click "no" button for "SketchFab 3d-Image viewer links"
    And I click "no" button for "Actionable code in CodeOceans"
    And I click "no" button for "or any other URL to a stable source of data and files directly related to this dataset"
    Then Next button class 'btn btn-green js-save-additional' becomes active
    And I click Next button on Additional Information tab
    Then the user is redirected to "Add Fundings" page



  Scenario: I click yes button to provide SketchFab Link and check if it is saved into DB
    Given I am on "site/login" and I login
    Given url address "/datasetSubmission/additionalManagement/id/322"
    When I delete the saved link from DB 'external_link' where dataset id is '322'
    When I click No button for Public data archive links
    And I click 'no' button for Related GigaDB Datasets
    And I click 'no' button for Project links
    And I click "no" button for "A published manuscript that uses this data"
    And I click "no" button for "Protocols.io link to methods used to generate this data"
    And I click "yes" button for "SketchFab 3d-Image viewer links"
    And I provide "https://skfb.ly/69wDV" SketchFab Link
    And I click "no" button for "Actionable code in CodeOceans"
    And I click "no" button for "or any other URL to a stable source of data and files directly related to this dataset"
    And I click Add Link button
    Then the sketch fab url is added and External Link Type is "3d image"
    When I click Save button on Additional Information tab
    Then the link 'url' is saved to DB 'external_link' where dataset id is '322'
    And I delete the saved link from DB 'external_link' where dataset id is '322'

  Scenario: I click yes button to provide CodeOceans “Embed code widget” and check if it is saved into DB
    Given I am on "site/login" and I login
    When I delete the saved link from DB 'external_link' where dataset id is '322'
    Given url address "/datasetSubmission/additionalManagement/id/322"
    When I click No button for Public data archive links
    And I click 'no' button for Related GigaDB Datasets
    And I click 'no' button for Project links
    And I click "no" button for "A published manuscript that uses this data"
    And I click "no" button for "Protocols.io link to methods used to generate this data"
    And I click "no" button for "SketchFab 3d-Image viewer links"
    And I click "yes" button for "Actionable code in CodeOceans"
    And I provide "<script src="https://codeocean.com/widget.js?id=0a812d9b-0ff3-4eb7-825f-76d3cd049a43" async></script>" CodeOceans
    And I click "no" button for "or any other URL to a stable source of data and files directly related to this dataset"
    And I click Add Link button
    Then the CodeOcean is added and External Link Type is "code"
    When I click Save button on Additional Information tab
    Then the link 'url' is saved to DB 'external_link' where dataset id is '322'
    And I delete the saved link from DB 'external_link' where dataset id is '322'


  Scenario: I click yes button to  provide the DOI or URL on Other links block and check if it is saved into DB
    Given I am on "site/login" and I login
    When I delete the saved link from DB 'external_link' where dataset id is '322'
    Given url address "/datasetSubmission/additionalManagement/id/322"
    When I click No button for Public data archive links
    And I click 'no' button for Related GigaDB Datasets
    And I click 'no' button for Project links
    And I click "no" button for "A published manuscript that uses this data"
    And I click "no" button for "Protocols.io link to methods used to generate this data"
    And I click "no" button for "SketchFab 3d-Image viewer links"
    And I click "no" button for "Actionable code in CodeOceans"
    And I click "yes" button for "or any other URL to a stable source of data and files directly related to this dataset"
    And I provide the DOI or URL: "doi:12.3456/789012.3"
    And I enter short description "test short description" for DOI or URL
    And I click Add Link button
    Then the DOI or URL is added, Short Description is added and External Link Type is "source"
    When I click Save button on Additional Information tab
    Then the link 'url' is saved to DB 'external_link' where dataset id is '322'
    And I delete the saved link from DB 'external_link' where dataset id is '322'


  Scenario: Add an Accession number for Public data archive links
    Given I am on "site/login" and I login
    Given url address "/datasetSubmission/additionalManagement/id/322"
    When I click Yes button for Public data archive links
    And choose №"2" from dropdown list
    And I enter "SRS012345" an accession number of Public data archive links block
    And I click Add Link button to add Access number
    Then Link Type and Link are added to the table


  Scenario: delete added Accession number on Public data archive links block
    Given I am on "site/login" and I login
    Given url address "/datasetSubmission/additionalManagement/id/322"
    When I click Yes button for Public data archive links
    And choose №"2" from dropdown list
    And I enter "SRS012345" an accession number of Public data archive links block
    And I click Add Link button to add Access number
    And I click Delete this row "1" button
    Then An alert appears "Are you sure you want to delete this item?"
    And I click OK button on the alert pop-up
    Then The table No"1" is empty and contains "No results found." on Additional Info tab


  Scenario: Add Related DOI and Relationship to Related GigaDB Datasets
    Given I am on "site/login" and I login
    Given url address "/datasetSubmission/additionalManagement/id/322"
    When I click No button for Public data archive links
    And I click 'yes' button for Related GigaDB Datasets
    And I choose the item №"2" from relationship dropdown list on Related GigaDB Datasets block
    And I choose dataset (DOI) "2" from relation doi dropdown list on Related GigaDB Datasets block
    And I click Add Related Doi button on Related GigaDB Datasets block
    Then Related DOI and Relationship are added to the table


  Scenario: Delete Related DOI and Relationship to Related GigaDB Datasets
    Given I am on "site/login" and I login
    Given url address "/datasetSubmission/additionalManagement/id/322"
    When I click No button for Public data archive links
    And I click 'yes' button for Related GigaDB Datasets
    And I choose the item №"2" from relationship dropdown list on Related GigaDB Datasets block
    And I choose dataset (DOI) "2" from relation doi dropdown list on Related GigaDB Datasets block
    And I click Add Related Doi button on Related GigaDB Datasets block
    And I click Delete this row "1" button
    Then An alert appears "Are you sure you want to delete this item?"
    And I click OK button on the alert pop-up
    Then The table No"2" is empty and contains "No results found." on Additional Info tab


  Scenario: add the project selected to the table linking the dataset to that project on Project links tab
    Given I am on "site/login" and I login
    Given url address "/datasetSubmission/additionalManagement/id/322"
    When I click No button for Public data archive links
    And I click 'no' button for Related GigaDB Datasets
    And I click 'yes' button for Project links
    And I choose project option "2" from dropdown list on Project links block
    And I click Add Project button on Project links block
    Then the project is added to the table


  Scenario: delete the added project from the table linking the dataset to that project on Project links tab
    Given I am on "site/login" and I login
    Given url address "/datasetSubmission/additionalManagement/id/322"
    When I click No button for Public data archive links
    And I click 'no' button for Related GigaDB Datasets
    And I click 'yes' button for Project links
    And I choose project option "2" from dropdown list on Project links block
    And I click Add Project button on Project links block
    And I click Delete this row "1" button
    Then An alert appears "Are you sure you want to delete this item?"
    And I click OK button on the alert pop-up
    Then The table No"3" is empty and contains "No results found." on Additional Info tab

  Scenario: I add manuscript link and check if it is saved into DB by clicking Next button
    Given I am on "site/login" and I login
    When I delete the saved link from DB 'manuscript' where dataset id is '322'
    Given url address "/datasetSubmission/additionalManagement/id/322"
    When I click No button for Public data archive links
    And I click 'no' button for Related GigaDB Datasets
    And I click 'no' button for Project links
    And I click "yes" button for "A published manuscript that uses this data"
    And I enter "doi:10.1093/gigascience/giy095" manuscript link
    And I click "no" button for "Protocols.io link to methods used to generate this data"
    And I click "no" button for "SketchFab 3d-Image viewer links"
    And I click "no" button for "Actionable code in CodeOceans"
    And I click "no" button for "or any other URL to a stable source of data and files directly related to this dataset"
    And I click Add Link button
    Then the manuscript url is added and External Link Type is "manuscript"
    When I click Next button on Additional Information tab
    Then the link 'identifier' is saved to DB 'manuscript' where dataset id is '322'
    And I delete the saved link from DB 'manuscript' where dataset id is '322'

  Scenario: I add Protocols.io DOI and check if it is saved into DB by clicking Next button
    Given I am on "site/login" and I login
    When I delete the saved link from DB 'external_link' where dataset id is '322'
    Given url address "/datasetSubmission/additionalManagement/id/322"
    When I click No button for Public data archive links
    And I click 'no' button for Related GigaDB Datasets
    And I click 'no' button for Project links
    And I click "no" button for "A published manuscript that uses this data"
    And I click "yes" button for "Protocols.io link to methods used to generate this data"
    And I provide "doi:10.17504/protocols.io.gk8buzw" Protocols.io DOI
    And I click "no" button for "SketchFab 3d-Image viewer links"
    And I click "no" button for "Actionable code in CodeOceans"
    And I click "no" button for "or any other URL to a stable source of data and files directly related to this dataset"
    And I click Add Link button
    Then the protocol url is added and External Link Type is "protocol"
    When I click Next button on Additional Information tab
    Then the link 'url' is saved to DB 'external_link' where dataset id is '322'
    And I delete the saved link from DB 'external_link' where dataset id is '322'


  Scenario: I add SketchFab Link and check if it is saved into DB by clicking Next button
    Given I am on "site/login" and I login
    Given url address "/datasetSubmission/additionalManagement/id/322"
    When I delete the saved link from DB 'external_link' where dataset id is '322'
    When I click No button for Public data archive links
    And I click 'no' button for Related GigaDB Datasets
    And I click 'no' button for Project links
    And I click "no" button for "A published manuscript that uses this data"
    And I click "no" button for "Protocols.io link to methods used to generate this data"
    And I click "yes" button for "SketchFab 3d-Image viewer links"
    And I provide "https://skfb.ly/69wDV" SketchFab Link
    And I click "no" button for "Actionable code in CodeOceans"
    And I click "no" button for "or any other URL to a stable source of data and files directly related to this dataset"
    And I click Add Link button
    Then the sketch fab url is added and External Link Type is "3d image"
    When I click Next button on Additional Information tab
    Then the link 'url' is saved to DB 'external_link' where dataset id is '322'
    And I delete the saved link from DB 'external_link' where dataset id is '322'

  Scenario: I Add CodeOceans “Embed code widget” and check if it is saved into DB by clicking Next
    Given I am on "site/login" and I login
    When I delete the saved link from DB 'external_link' where dataset id is '322'
    Given url address "/datasetSubmission/additionalManagement/id/322"
    When I click No button for Public data archive links
    And I click 'no' button for Related GigaDB Datasets
    And I click 'no' button for Project links
    And I click "no" button for "A published manuscript that uses this data"
    And I click "no" button for "Protocols.io link to methods used to generate this data"
    And I click "no" button for "SketchFab 3d-Image viewer links"
    And I click "yes" button for "Actionable code in CodeOceans"
    And I provide "<script src="https://codeocean.com/widget.js?id=0a812d9b-0ff3-4eb7-825f-76d3cd049a43" async></script>" CodeOceans
    And I click "no" button for "or any other URL to a stable source of data and files directly related to this dataset"
    And I click Add Link button
    Then the CodeOcean is added and External Link Type is "code"
    When I click Next button on Additional Information tab
    Then the link 'url' is saved to DB 'external_link' where dataset id is '322'
    And I delete the saved link from DB 'external_link' where dataset id is '322'


  Scenario: I click yes button to  provide the DOI or URL on Other links block and check if it is saved into DB by clicking NEXT button
    Given I am on "site/login" and I login
    When I delete the saved link from DB 'external_link' where dataset id is '322'
    Given url address "/datasetSubmission/additionalManagement/id/322"
    When I click No button for Public data archive links
    And I click 'no' button for Related GigaDB Datasets
    And I click 'no' button for Project links
    And I click "no" button for "A published manuscript that uses this data"
    And I click "no" button for "Protocols.io link to methods used to generate this data"
    And I click "no" button for "SketchFab 3d-Image viewer links"
    And I click "no" button for "Actionable code in CodeOceans"
    And I click "yes" button for "or any other URL to a stable source of data and files directly related to this dataset"
    And I provide the DOI or URL: "doi:12.3456/789012.3"
    And I enter short description "test short description" for DOI or URL
    And I click Add Link button
    Then the DOI or URL is added, Short Description is added and External Link Type is "source"
    When I click Next button on Additional Information tab
    Then the link 'url' is saved to DB 'external_link' where dataset id is '322'
    And I delete the saved link from DB 'external_link' where dataset id is '322'


  Scenario: I click yes button to add manuscript link and check if it is saved into DB
    Given I am on "site/login" and I login
    When I delete the saved link from DB 'manuscript' where dataset id is '322'
    Given url address "/datasetSubmission/additionalManagement/id/322"
    When I click No button for Public data archive links
    And I click 'no' button for Related GigaDB Datasets
    And I click 'no' button for Project links
    And I click "yes" button for "A published manuscript that uses this data"
    And I enter "doi:10.1093/gigascience/giy095" manuscript link
    And I click "no" button for "Protocols.io link to methods used to generate this data"
    And I click "no" button for "SketchFab 3d-Image viewer links"
    And I click "no" button for "Actionable code in CodeOceans"
    And I click "no" button for "or any other URL to a stable source of data and files directly related to this dataset"
    And I click Add Link button
    Then the manuscript url is added and External Link Type is "manuscript"
    When I click Save button on Additional Information tab
    Then the link 'identifier' is saved to DB 'manuscript' where dataset id is '322'
    And I delete the saved link from DB 'manuscript' where dataset id is '322'



  Scenario: I click yes button to provide the Protocols.io DOI and check if it is saved into DB
    Given I am on "site/login" and I login
    When I delete the saved link from DB 'external_link' where dataset id is '322'
    Given url address "/datasetSubmission/additionalManagement/id/322"
    When I click No button for Public data archive links
    And I click 'no' button for Related GigaDB Datasets
    And I click 'no' button for Project links
    And I click "no" button for "A published manuscript that uses this data"
    And I click "yes" button for "Protocols.io link to methods used to generate this data"
    And I provide "doi:10.17504/protocols.io.gk8buzw" Protocols.io DOI
    And I click "no" button for "SketchFab 3d-Image viewer links"
    And I click "no" button for "Actionable code in CodeOceans"
    And I click "no" button for "or any other URL to a stable source of data and files directly related to this dataset"
    And I click Add Link button
    Then the protocol url is added and External Link Type is "protocol"
    When I click Save button on Additional Information tab
    Then the link 'url' is saved to DB 'external_link' where dataset id is '322'
    And I delete the saved link from DB 'external_link' where dataset id is '322'
