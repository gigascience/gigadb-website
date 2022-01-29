Feature: User profile page
  As an author
  I want a form to edit my user details
  So that I can update my contact details on the GigaDB web site
  
@ok
Scenario: View user profile
  Given I sign in as a user
  When I am on "/user/view_profile"
  Then I should see "Your profile page"
  And I should see "Personal details"
  And I should see "Email"
  And I should see "user@gigadb.org"
  And I should see "Last Name"
  And I should see "Smith"
  And I should see "Mailing list subscriber"
  And I should see "No"
  And I should see a "Edit" button

@ok
Scenario: Ensure mailing list checkbox is checkable after clicking Edit button
  Given I sign in as a user
  And I am on "/user/view_profile"
  And I press the button "Edit"
  When I check "EditProfileForm[newsletter]" checkbox
  Then I should see "EditProfileForm[newsletter]" checkbox is checked

@ok
Scenario: Ensure pressing Cancel button reverts profile page back to original user details
  Given I sign in as a user
  And I am on "/user/view_profile"
  And I should see "Mailing list subscriber"
  And I should see "No"
  And I press the button "Edit"
  And I check "EditProfileForm[newsletter]" checkbox
  When I press the button "Cancel"
  Then I should see "Mailing list subscriber"
  And I should see "No"
  And I should not see "Yes"

@ok
Scenario: Ensure mailing list subscriber displays Yes after pressing Save button
  Given I sign in as a user
  When I am on "/user/view_profile"
  And I press the button "Edit"
  And I check "EditProfileForm[newsletter]" checkbox
  And I press the button "Save"
  Then I should see "Yes"
