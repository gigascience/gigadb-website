Feature:  account creation
AS a researcher,
I want to create an account on gigadb web site
SO THAT the dataset for my research papers are openly available online

	Given that I navigate to /user/create
	When I fill in the mandatory form fields
	And password  fields matches
	And captcha validation is successful
	And submit the form
	Then a new account will be created on gigadb web site with the email and password of my choice

Feature: signin with gigadb credentials
AS a researcher,
I want to sign in to my gigadb web site account with my Gigadb credentials
SO THAT I can upload the dataset for my research papers

	Given that I navigate to /site/login
	When I fill in the email and password I chose when I created a Gigadb account
	Then I’m logged in to the gigadb web site


Feature: signin with social credentials
AS a researcher,
I want to sign in to my gigadb web site account with my social media credentials
SO THAT I can upload the dataset for my research papers without inputting my gigadb web site credentials


	Scenario: signin with Facebook
	Given that I navigate to /site/chooseLogin
	When I click on the Facebook button
	And I authorise Gigadb for Facebook
	Then I’m logged in to the gigadb web site

	Scenario: signin with Google
	Given that I navigate to /site/chooseLogin
	When I click on the Facebook button
	And I authorise Gigadb for Google
	Then I’m logged in to the gigadb web site

	Scenario: signin with Twitter
	Given that I navigate to /site/chooseLogin
	When I click on the Twitter button
	And I authorise Gigadb for Twitter
	Then I’m logged in to the gigadb web site

	Scenario: signin with LinkedIn
	Given that I navigate to /site/chooseLogin
	When I click on the LinkedIn button
	And I authorise Gigadb for LinkedIn
	Then I’m logged in to the gigadb web site

