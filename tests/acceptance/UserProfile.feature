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
  And I should see a check-box field "EditProfileForm_newsletter"
  And I should see "Add me to GigaDB's mailing list"
  And I should see a "Edit" button
  
@ok
Scenario: Ensure mailing list checkbox is not checkable on /user/view_profile
  Given I sign in as a user
  And I am on "/user/view_profile"
  And I should see "Your profile page"
  And I should see a check-box field "EditProfileForm_newsletter"
  And I should see "EditProfileForm[newsletter]" checkbox is not checked
  When I check "EditProfileForm[newsletter]" checkbox
  Then I should see "EditProfileForm[newsletter]" checkbox is not checked
  

#  @ok
#  Scenario: Filling in the form to create new user
#    Given I am on "/user/create"
#    And there is no user with email "martianmanhunter@mailinator.com"
#    When I fill in the field of "id" "User_email" with "martianmanhunter@mailinator.com"
#    And I fill in the field of "id" "User_first_name" with "J'onn"
#    And I fill in the field of "id" "User_last_name" with "J'onzz"
#    And I fill in the field of "id" "User_password" with "123456787"
#    And I fill in the field of "id" "User_password_repeat" with "123456787"
#    And I fill in the field of "id" "User_affiliation" with "GigaScience"
#    And I select "NCBI" from the field "User_preferred_link"
#    And I check the field "User_terms"
#    And I fill in the field of "id" "User_verifyCode" with "shazam"
#    And I press the button "Register"
#    Then I should see "Welcome!"
    