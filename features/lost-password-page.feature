@issue-876
Feature:
  As a website user
  I want to reset my password
  So that I can sign in to GigaDB website again

  Background:
    Given Gigadb web site is loaded with production-like data

    @ok
    Scenario: Go to log in page and lost password link is there
      Given I am not logged in to Gigadb web site
      When I am on "/site/login"
      Then I should see "Lost Password"

    @ok
    Scenario: Click lost password link and will be on reset password page
      Given I am not logged in to Gigadb web site
      When I am on "/site/login"
      And I click on the "Lost Password" button
      Then I should be on "/user/reset/username//style/float%3Aright"
      And I should see "If you have lost your password, enter your email and we will send a new password to the email address associated with your account."
      And I should see "Email *"
      And I should see a button input "Reset"

    @ok
    Scenario: Click lost password and tick box is not found
      Given I am not logged in to Gigadb web site
      When I am on "/site/login"
      And I click on the "Lost Password" button
      Then I should be on "/user/reset/username//style/float%3Aright"
      And I should not see "Mailing list"
      And I should not see a checkbox for the "User_newsletter"
      And I should not see "Please tick here to join the GigaDB mailing list to receive news, updates and quarterly newsletters about GigaDB"
      And I should not see "Terms and Conditions *"
      And I should not see a checkbox for the "User_terms"
      And I should not see "Please tick here to confirm you have read and understood our Terms of use and Privacy Policy"