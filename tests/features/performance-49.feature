@merging-two-authors @performance @javascript
Feature: Merging duplicate authors
	In order to reduce data duplication and to increase datasets interlinking
	As an admin user
	I want to merge author records that are identical

Background:
	Given Gigadb web site is loaded with production-like data
	And an admin user exists
	When I am on "/dataset/100039"
	Then I should see "Genomic data of the Puerto Rican Parrot"

@wip
Scenario: loading the author table
	Given I sign in as an admin
	And I started the timer
	When I go to "/adminAuthor/admin"
	Then I should see "Zhang"
	Then I should see "Bo"
	Then I should see "0000-0001-8890-8416"
	Then I should see "De La Cruz"
	Then I should see "German"
	Then I should see "Next"
	Then the timer is stopped
	# When I follow "2"
	# Then I should see "Chakrabarti"
	# Then I should see "Bolser"