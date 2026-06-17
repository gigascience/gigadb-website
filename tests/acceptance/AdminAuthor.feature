@admins-attach-author-to-user @issue-56 @ok-docker
Feature: a curator can fill in user id in an author record
  As an curator
  I want to connect a user identity to an author record
  So that I can enable gigadb users direct access to the dataset they have authored

  Background:
    Given I have signed in as admin

  @ok @admin-author-form-add-user
  Scenario: populate user identity field when creating an author
    Given I am on "/adminAuthor/create"
    And I fill in the field of "id" "Author_surname" with "Tano"
    And I fill in the field of "id" "Author_first_name" with "Ahsoka"
    And I fill in the field of "id" "Author_middle_name" with "Fulcrum"
    And I fill in the field of "id" "Author_gigadb_user_id" with "345"
    And I press the button "Create"
    Then I should see "Gigadb User"
    And  I should see "345"
    And  I should see "Tano AF"

  @ok
  Scenario: when display name edited, save it instead of calculated value
    Given I am on "/adminAuthor/create"
    And I fill in the field of "id" "Author_surname" with "Tano"
    And I fill in the field of "id" "Author_first_name" with "Ahsoka"
    And I fill in the field of "id" "Author_middle_name" with "Fulcrum"
    And I fill in the field of "id" "Author_custom_name" with "AF"
    And I press the button "Create"
    Then I should see "AF"

  @ok
  Scenario: On author edit form, there is a button to start the merging with another author
    Given I am on "/adminAuthor/update/id/14"
    Then I should see "Merge with an author"

  @ok @admin-author-form-add-user
  Scenario: populate user identity field when updating an author
    Given I am on "/adminAuthor/update/id/14"
    And I fill in the field of "id" "Author_gigadb_user_id" with "345"
    And I press the button "Save"
    Then I should be on "/adminAuthor/view/id/14"
    And I should see "Gigadb User"
    And I should see "345"

  @ok @admin-author-form-add-user
  Scenario: populate author form with a user id already used triggers error
    Given I am on "/adminAuthor/update/id/14"
    And I fill in the field of "id" "Author_gigadb_user_id" with "345"
    And I press the button "Save"
    And I am on "/adminAuthor/update/id/651"
    And I fill in the field of "id" "Author_gigadb_user_id" with "345"
    And I press the button "Save"
    Then I should see "has already been taken"

  @ok @admin-link-author-from-user
  Scenario: loading the author list directly doesn't show the user specific controls for selecting author to link
    Given I am on "/adminAuthor/admin"
    Then I should not see "Click on a row to proceed with linking that author with user"
    And I should see "Manage Authors"

  @ok
  Scenario: Presssing the merge an author button leads to author table and then merging of an author
    Given I am on "/adminAuthor/update/id/14"
    When I follow "Merge with an author"
    And I wait "2" seconds
    And I click on row "2" column "6" and icon "4"
    And I wait "5" seconds
    And I should see "Confirm merging these two authors?"
    And I should see "ID:"
    And I should see "Surname:"
    And I should see "First name:"
    And I should see "Middle name:"
    And I should see "Orcid:"
    And I should see "14"
    And I should see "651"
    And I should see "Wang"
    And I should see "J"
    And I should see "Quan"
    And I should see "Zhiwu"
    And I follow "Yes, merge authors"
    And I wait "2" seconds
    Then I should be on "/adminAuthor/view/id/14"
    And I should see "this author is merged with author(s):"
    And I should see "651. Zhiwu Quan (Orcid: n/a)"

  @ok
  Scenario: cannot go through the workflow for linking author to user and for merging two authors at the same time (2)
    Given I am on "/adminAuthor/update/id/14"
    And I follow "Merge with an author"
    When I am on "/adminUser/update/id/8"
    And I follow "Link this user to an author"
    Then I should not see "with merging that author with"
    And I should see "with linking that author with user"

  @ok
  Scenario: cannot go through the workflow for linking author to user and for merging two authors at the same time (1)
    Given I am on "/adminUser/update/id/8"
    And I follow "Link this user to an author"
    And I wait "3" seconds
    And I should be on "/adminAuthor/admin"
    And I should see "with linking that author with user"
    When I am on "/adminAuthor/update/id/14"
    And I follow "Merge with an author"
    Then I should see "with merging that author with"
    And I should not see "with linking that author with user"

  @ok
  Scenario: On user profile, show the datasets of linked author and of authors merged to the linked author
    Given author "14" is merged with author row "5" column "6" icon "4"
    And user "22" name "John" lastname "Doe" is linked to author row "1" column "6" icon "4"
    And I have not signed in
    And I have signed in as user with email "user@mailinator.com"
    When I am on "/user/view_profile"
    And I follow "Your Authored Datasets"
    Then I should see "10.5524/100035"
    And I should see "10.5524/100020"

  @ok
  Scenario: If exists (A1 i_t A2), (A1 i_t A3) and (A2 i_t A5), on A3 edit form, pressing unmerge removes A3 from graph
    Given author "14" is merged with author row "2" column "6" icon "4"
     #659
    And author "14" is merged with author row "3" column "6" icon "4"
    And author "661" is merged with author row "5" column "6" icon "4"
    When I am on "/adminAuthor/update/id/659"
    And I follow "Unmerge author from those authors"
    And I wait "2" seconds
    Then I should be on "/adminAuthor/view/id/659"
    And I should see "author unmerged from other authors"
    And I should not see "651"
    And I should not see "662"
    And I should not see "Unmerge author from those authors"

  @ok
  Scenario: If exists (A1 i_t A2), (A1 i_t A3) and (A2 i_t A5), on A5 edit form: shows links and an unmerge button
    Given author "14" is merged with author row "2" column "6" icon "4"
   #659
    And author "14" is merged with author row "3" column "6" icon "4"
    And author "651" is merged with author row "5" column "6" icon "4"
    When I am on "/adminAuthor/update/id/662"
    Then I should see "this author is merged with author(s):"
    And I should see "14"
    And I should see "651"
    And I should see "659"
    And I should see "Unmerge author from those authors"

  @ok
  Scenario: If exists (A1 i_t A2), (A1 i_t A3) and (A2 i_t A5), on A1 edit form: shows links and an unmerge button
    Given author "14" is merged with author row "2" column "6" icon "4"
    #659
    And author "14" is merged with author row "3" column "6" icon "4"
    And author "651" is merged with author row "5" column "6" icon "4"
    When I am on "/adminAuthor/update/id/14"
    Then I should see "this author is merged with author(s):"
    And I should see "651"
    And I should see "659"
    And I should see "662"
    And I should see "Unmerge author from those authors"

  @ok
  Scenario: If exists (A1 i_t A2), (A1 i_t A3) and (A2 i_t A5), a graph of merged authors is shown properly on A5
    Given author "14" is merged with author row "2" column "6" icon "4"
    #659
    And author "14" is merged with author row "3" column "6" icon "4"
    And author "651" is merged with author row "5" column "6" icon "4"
    When I am on "/adminAuthor/view/id/662"
    Then I should see "this author is merged with author(s):"
    Then I should see "14"
    Then I should see "651"
    Then I should see "659"

  @ok
  Scenario: If exists (A1 i_t A2), (A1 i_t A3) and (A2 i_t A5), on A1 view: a graph of merged authors is shown properly
    Given author "14" is merged with author row "2" column "6" icon "4"
    #659
    And author "14" is merged with author row "3" column "6" icon "4"
    And author "651" is merged with author row "5" column "6" icon "4"
    When I am on "/adminAuthor/view/id/14"
    Then I should see "this author is merged with author(s):"
    Then I should see "651"
    Then I should see "659"
    Then I should see "662"

  @ok
  Scenario: If exists (A1 identical_to A4), A4 view shows link to A1
    Given author "14" is merged with author row "2" column "6" icon "4"
    When I am on "/adminAuthor/view/id/651"
    Then I should see "this author is merged with author(s):"
    Then I should see "14. J Wang (Orcid: n/a)"

  @ok
  Scenario: If exists (A1 identical_to A4), attempt to merge A4 with A1 should not be possible
    Given author "14" is merged with author row "2" column "6" icon "4"
    When I am on "/adminAuthor/update/id/651"
    And I follow "Merge with an author"
    And I wait "2" seconds
    And I click on row "1" column "6" and icon "4"
    And I wait "1" seconds
    And I should see "Confirm merging these two authors?"
    And I follow "Yes, merge authors"
    Then I should see "Authors already merged. Choose another author to merge with"

  @ok
  Scenario: If exists (A1 identical_to A4), attempt to merge A1 with A4 again should not be possible
    Given author "14" is merged with author row "2" column "6" icon "4"
    When I am on "/adminAuthor/update/id/14"
    When I follow "Merge with an author"
    And I wait "2" seconds
    And I click on row "2" column "6" and icon "4"
    And I wait "1" seconds
    And I should see "Confirm merging these two authors?"
    And I follow "Yes, merge authors"
    Then I should see "Authors already merged. Choose another author to merge with"

  @ok
  Scenario: Cannot merge an author with himself
    Given I am on "/adminAuthor/update/id/14"
    When I follow "Merge with an author"
    And I wait "2" seconds
    And I click on row "1" column "6" and icon "4"
    And I wait "1" seconds
    And I should see "Confirm merging these two authors?"
    And I follow "Yes, merge authors"
    Then I should see "Cannot merge with self. Choose another author to merge with"

  @ok
  Scenario: No unmerge button appears when there is no author merged to the one being edited
    When I am on "/adminAuthor/update/id/14"
    Then I should not see "this author is merged with author(s):"
    And I should not see "Unmerge author from those authors"

  @ok
  Scenario: There is an unmerge button to disconnect two authors from an author edit form
    Given author "14" is merged with author row "2" column "6" icon "4"
    When I am on "/adminAuthor/update/id/14"
    Then I should see "this author is merged with author(s):"
    And I should see "Zhiwu Quan "
    And I should see "Unmerge author from those authors"

  @ok
  Scenario: Abort a merge from the popup confirmation box
    Given I am on "/adminAuthor/update/id/14"
    When I follow "Merge with an author"
    And I wait "2" seconds
    And I click on row "2" column "6" and icon "4"
    And I wait "1" seconds
    And I should see "Confirm merging these two authors?"
    And I follow "No, abort and clear session"
    And I wait "1" seconds
    Then I should be on "/adminAuthor/view/id/14"
    And I should not see "merging authors completed successfully"

  @ok
  Scenario: Merging a new author into a graph of identical authors
    Given author "14" is merged with author row "2" column "6" icon "4"
    And author "14" is merged with author row "3" column "6" icon "4"
    And I am on "/adminAuthor/update/id/662"
    When I follow "Merge with an author"
    And I wait "3" seconds
    And I click on row "2" column "6" and icon "4"
    And I wait "3" seconds
    And I should see "Confirm merging these two authors?"
    And I should see "ID:"
    And I should see "Surname:"
    And I should see "First name:"
    And I should see "Middle name:"
    And I should see "Orcid:"
    And I should see "Already merged with:"
    And I should see "662"
    And I should see "651"
    And I should see "Dharmi"
    And I should see "Pawandeep"
    And I should see "Quan"
    And I should see "Zhiwu"
    And I should see "Wang J, Zhao Z"
    And I follow "Yes, merge authors"
    And I wait "3" seconds
    Then I should be on "/adminAuthor/view/id/662"
    And I should see "merging authors completed successfully"

  @ok
  Scenario: Merging a new author already in a graph with another author
    Given author "14" is merged with author row "2" column "6" icon "4"
    Given author "651" is merged with author row "3" column "6" icon "4"
    And I am on "/adminAuthor/update/id/14"
    When I follow "Merge with an author"
    And I wait "2" seconds
    And I click on row "4" column "6" and icon "4"
    And I wait "1" seconds
    And I should see "Confirm merging these two authors?"
    And I should see "ID:"
    And I should see "Surname:"
    And I should see "First name:"
    And I should see "Middle name:"
    And I should see "Orcid:"
    And I should see "Already merged with:"
    And I should see "14"
    And I should see "661"
    And I should see "Wang"
    And I should see "J"
    And I should see "Wilson"
    And I should see "Gareth"
    And I should see "A"
    And I should see "Quan Z,Zhao Z"
    And I follow "Yes, merge authors"
    And I wait "2" seconds
    Then I should be on "/adminAuthor/view/id/14"
    And I should see "merging authors completed successfully"

