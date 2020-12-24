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
    When I am on "/dataset/100002"
    Then there is a meta tag "title" with value "GigaDB Dataset - DOI 10.5524/100002 - Genomic data from Adelie penguin (Pygoscelis adeliae)."
#    Then I take a screenshot named "test_description"
    And there is a meta tag "description" with value "The Adelie penguin (Pygoscelis adeliae) is an iconic penguin of moderate stature and a tuxedo of black and white feathers. The penguins are only found in the Antarctic region and surrounding islands. Being very sensitive to climate change, and due to changes in their behavior based on minor shifts in climate, they are often used as a barometer of the Antarctic.With its status as one of the adorable and cuddly flightless birds of Antarctica, they serve as an example for conservation, and as a result they are now categorised at low risk for endangerment. The sequence of the penguin can be of use in understanding the genetic underpinnings of its evolutionary traits and adaptation to its extreme environment; its unique system of feathers; its prowess as a diver; and its sensitivity to climate change. We hope that this genome data will further our understanding of one of the most remarkable creatures to waddle the planet Earth.We sequenced the genome of an adult male from Inexpressible Island, Ross Sea, Antartica (provided by David Lambert) to a depth of approximately 60X with short reads from a series of libraries with various insert sizes (200bp- 20kb). The assembled scaffolds of high quality sequences total 1.23 Gb, with the contig and scaffold N50 values of 19 kb and 5 mb respectively. We identified 15,270 protein-coding genes with a mean length of 21.3 kb."


