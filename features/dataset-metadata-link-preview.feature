@issue-513
Feature: Add the metadata schema on dataset page to allow other web sites to make link previews of our web site
  As an operator of a partner website
  I want to extract preview metadata from links to GigaDB datasets
  So I can present preview information to my visitors interested in those links

  Background:
    Given Gigadb web site is loaded with "gigadb_testdata.pgdmp" data

  @ok
    Scenario: Can be parsed by preview tools that use HTML meta-tags (e.g: search engines)
    Given I am not logged in to Gigadb web site
    When I am on "/dataset/100004"
    Then there should be a "name" meta tag "title" with value "GigaDB Dataset - DOI 10.5524/100004 - Data and software to accompany the paper: Applying compressed sensing to genome-wide association studies."
    And there should be a "name" meta tag "description" with value "The aim of a genome-wide association study (GWAS) is to isolate DNA markers for variants affecting phenotypes of interest. Linear regression is employed for this purpose, and in recent years a signal-processing paradigm known as compressed sensing (CS) has coalesced around a particular class of regression techniques. CS is not a method in its own right, but rather a body of theory regarding signal recovery when the number of predictor variables (i.e., genotyped markers) exceeds the sample size. The paper shows the applicability of compressed sensing (CS) theory to genome-wide association studies (GWAS), where the purpose is to ﬁnd trait-associated tagging markers (genetic variants). Analysis scripts are contained in the compressed CS file. Mock data and scripts are found in the compressed GD file. The example scripts found in the CS repository require the GD files to be unpacked in a separate folder. Please look at accompanying readme pdfs for both repositories and annotations in the example scripts before using."

  @ok
    Scenario: Can be parsed by preview tools that use Twitter/OGP (e.g:Twitter)
    Given I am not logged in to Gigadb web site
    When I am on "/dataset/100004"
    Then there should be a "property" meta tag "twitter:title" with value "GigaDB Dataset - DOI 10.5524/100004 - Data and software to accompany the paper: Applying compressed sensing to genome-wide association studies."
    And there should be a "property" meta tag "twitter:url" with value "https://doi.org/10.5072/100004"
    And there should be a "property" meta tag "twitter:image" with value "http://gigadb.org/images/uploads/image_upload/Images_147.png"
    And there should be a "property" meta tag "twitter:description" with value "The aim of a genome-wide association study (GWAS) is to isolate DNA markers for variants affecting phenotypes of interest. Linear regression is employed for this purpose, and in recent years a signal-processing paradigm known as compressed sensing (CS) has coalesced around a particular class of regression techniques. CS is not a method in its own right, but rather a body of theory regarding signal recovery when the number of predictor variables (i.e., genotyped markers) exceeds the sample size. The paper shows the applicability of compressed sensing (CS) theory to genome-wide association studies (GWAS), where the purpose is to ﬁnd trait-associated tagging markers (genetic variants). Analysis scripts are contained in the compressed CS file. Mock data and scripts are found in the compressed GD file. The example scripts found in the CS repository require the GD files to be unpacked in a separate folder. Please look at accompanying readme pdfs for both repositories and annotations in the example scripts before using."

  @ok
    Scenario: can be parsed by preview tools that use OGP (e.g: Facebook)
    Given I am not logged in to Gigadb web site
    When I am on "/dataset/100004"
    Then there should be a "property" meta tag "og:title" with value "GigaDB Dataset - DOI 10.5524/100004 - Data and software to accompany the paper: Applying compressed sensing to genome-wide association studies."
    And there should be a "property" meta tag "og:url" with value "https://doi.org/10.5072/100004"
    And there should be a "property" meta tag "og:image" with value "http://gigadb.org/images/uploads/image_upload/Images_147.png"
    And there should be a "property" meta tag "og:description" with value "The aim of a genome-wide association study (GWAS) is to isolate DNA markers for variants affecting phenotypes of interest. Linear regression is employed for this purpose, and in recent years a signal-processing paradigm known as compressed sensing (CS) has coalesced around a particular class of regression techniques. CS is not a method in its own right, but rather a body of theory regarding signal recovery when the number of predictor variables (i.e., genotyped markers) exceeds the sample size. The paper shows the applicability of compressed sensing (CS) theory to genome-wide association studies (GWAS), where the purpose is to ﬁnd trait-associated tagging markers (genetic variants). Analysis scripts are contained in the compressed CS file. Mock data and scripts are found in the compressed GD file. The example scripts found in the CS repository require the GD files to be unpacked in a separate folder. Please look at accompanying readme pdfs for both repositories and annotations in the example scripts before using."

  @ok
    Scenario: Separated lines in description can be parsed by preview tools
    Given I am not logged in to Gigadb web site
    When I am on "/dataset/100002"
    Then there should be a "name" meta tag "description" with lines:
    """
    The Adelie penguin (Pygoscelis adeliae) is an iconic penguin of moderate stature and a tuxedo of black and white feathers. The penguins are only found in the Antarctic region and surrounding islands. Being very sensitive to climate change, and due to changes in their behavior based on minor shifts in climate, they are often used as a barometer of the Antarctic.
    With its status as one of the adorable and cuddly flightless birds of Antarctica, they serve as an example for conservation, and as a result they are now categorised at low risk for endangerment. The sequence of the penguin can be of use in understanding the genetic underpinnings of its evolutionary traits and adaptation to its extreme environment; its unique system of feathers; its prowess as a diver; and its sensitivity to climate change. We hope that this genome data will further our understanding of one of the most remarkable creatures to waddle the planet Earth.
    We sequenced the genome of an adult male from Inexpressible Island, Ross Sea, Antartica (provided by David Lambert) to a depth of approximately 60X with short reads from a series of libraries with various insert sizes (200bp- 20kb). The assembled scaffolds of high quality sequences total 1.23 Gb, with the contig and scaffold N50 values of 19 kb and 5 mb respectively. We identified 15,270 protein-coding genes with a mean length of 21.3 kb.
    """
    And there should be a "property" meta tag "twitter:description" with lines:
    """
    The Adelie penguin (Pygoscelis adeliae) is an iconic penguin of moderate stature and a tuxedo of black and white feathers. The penguins are only found in the Antarctic region and surrounding islands. Being very sensitive to climate change, and due to changes in their behavior based on minor shifts in climate, they are often used as a barometer of the Antarctic.
    With its status as one of the adorable and cuddly flightless birds of Antarctica, they serve as an example for conservation, and as a result they are now categorised at low risk for endangerment. The sequence of the penguin can be of use in understanding the genetic underpinnings of its evolutionary traits and adaptation to its extreme environment; its unique system of feathers; its prowess as a diver; and its sensitivity to climate change. We hope that this genome data will further our understanding of one of the most remarkable creatures to waddle the planet Earth.
    We sequenced the genome of an adult male from Inexpressible Island, Ross Sea, Antartica (provided by David Lambert) to a depth of approximately 60X with short reads from a series of libraries with various insert sizes (200bp- 20kb). The assembled scaffolds of high quality sequences total 1.23 Gb, with the contig and scaffold N50 values of 19 kb and 5 mb respectively. We identified 15,270 protein-coding genes with a mean length of 21.3 kb.
    """
    And there should be a "property" meta tag "og:description" with lines:
    """
    The Adelie penguin (Pygoscelis adeliae) is an iconic penguin of moderate stature and a tuxedo of black and white feathers. The penguins are only found in the Antarctic region and surrounding islands. Being very sensitive to climate change, and due to changes in their behavior based on minor shifts in climate, they are often used as a barometer of the Antarctic.
    With its status as one of the adorable and cuddly flightless birds of Antarctica, they serve as an example for conservation, and as a result they are now categorised at low risk for endangerment. The sequence of the penguin can be of use in understanding the genetic underpinnings of its evolutionary traits and adaptation to its extreme environment; its unique system of feathers; its prowess as a diver; and its sensitivity to climate change. We hope that this genome data will further our understanding of one of the most remarkable creatures to waddle the planet Earth.
    We sequenced the genome of an adult male from Inexpressible Island, Ross Sea, Antartica (provided by David Lambert) to a depth of approximately 60X with short reads from a series of libraries with various insert sizes (200bp- 20kb). The assembled scaffolds of high quality sequences total 1.23 Gb, with the contig and scaffold N50 values of 19 kb and 5 mb respectively. We identified 15,270 protein-coding genes with a mean length of 21.3 kb.
    """