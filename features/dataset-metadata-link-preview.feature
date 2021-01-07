@issue-513
Feature: Add the metadata schema on dataset page to allow other web sites to make link previews of our web site
  As an operator of a partner website
  I want to extract preview metadata from links to GigaDB datasets
  So I can present preview information to my visitors interested in those links

  Background:
    Given Gigadb web site is loaded with "gigadb_testdata.pgdmp" data

  @wip @issue-513 @javascript
    Scenario: Can be parsed by preview tools that use HTML meta-tags (e.g: search engines)
    Given I am not logged in to Gigadb web site
    When I am on "/dataset/100004"
    Then there is a meta tag "title" with value "GigaDB Dataset - DOI 10.5524/100004 - Data and software to accompany the paper: Applying compressed sensing to genome-wide association studies."
    And the page meta description should be "The aim of a genome-wide association study (GWAS) is to isolate DNA markers for variants affecting phenotypes of interest. Linear regression is employed for this purpose, and in recent years a signal-processing paradigm known as compressed sensing (CS) has coalesced around a particular class of regression techniques. CS is not a method in its own right, but rather a body of theory regarding signal recovery when the number of predictor variables (i.e., genotyped markers) exceeds the sample size. The paper shows the applicability of compressed sensing (CS) theory to genome-wide association studies (GWAS), where the purpose is to ﬁnd trait-associated tagging markers (genetic variants). Analysis scripts are contained in the compressed CS file. Mock data and scripts are found in the compressed GD file. The example scripts found in the CS repository require the GD files to be unpacked in a separate folder. Please look at accompanying readme pdfs for both repositories and annotations in the example scripts before using."