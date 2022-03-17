Feature: User profile submitted page
  As an author
  I want a form to update and delete my uploaded datasets
  So that I can update and delete my uploaded datasets

  @ok
  Scenario: View user profile
    Given I sign in as a user
    When I am on "/user/view_profile#submitted"
    Then I should see "Your profile page"
    And I should see "Your Uploaded Datasets"
    And I should see "Your Uploaded Datasets" tab with table on user view profile
    | DOI     | Title       | Subject | Dataset Type | Status  | Publication Date | Modification Date | File Count | Operation     |
    | unknown | Lorem ipsum |         |              | Request |                  |                   | 0          | Update Delete |

  @ok
  Scenario: Click Delete link on Your Uploaded Datasets table
    Given I sign in as a user
    When I am on "/user/view_profile#submitted"
    And I follow "Delete"
    And I accept popup
    And I wait "3" seconds
    Then I should not see "Lorem ipsum"

  @ok
  Scenario: Check deleted datasets user has deleted dataset on their view profile page
    Given I am on "/site/login"
    And I fill in the field of "id" "LoginForm_username" with "test@gigasciencejournal.com"
    And I fill in the field of "id" "LoginForm_password" with "gigadb"
    And I press the button "Login"
    When I am on "/user/view_profile#submitted"
    And I should see "Your Uploaded Datasets" tab with table on user view profile
      | DOI     | Title       | Subject | Dataset Type | Status  | Publication Date | Modification Date | File Count | Operation     |
      | unknown | Lorem ipsum |         |              | Request |                  |                   | 0          | Update Delete |
