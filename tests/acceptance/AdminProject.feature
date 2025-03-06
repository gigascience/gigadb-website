@ok-can-offline
Feature: form to manage project
  As a curator
  I want to be able to update a projects details without bugs
  So that I can keep the project details upto date

  Background:
    Given I have signed in as admin

  @ok
  Scenario: Can update image location
    Given I am on "/adminProject/update/id/2"
    When I fill in the field of "name" "Project[image_location]" with "https://test/images/projects/genome_10k/G10Klogo.jpg"
    And I press the button "Save"
    Then I should see "View Project #2"
