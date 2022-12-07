Feature:
  As a developer
  I want to validate the access policies to Wasabi
  So that unaccounted for usage cannot happen

  @ok @wasabi @storage
  Scenario: Group Developers can see list of buckets in console
    Given I configure rclone with a "Developer" account
    When I run the command to list buckets
    Then I should see the list of buckets

  @ok @wasabi @storage
  Scenario: Group Developers can read data in dev environment
    Given I configure rclone with a "Developer" account
    When I run the command to download file "test.txt" from the "dev" environment
    Then I can see "dev/test.txt" on my local filesystem

  @ok @wasabi @storage
  Scenario: Group Developers can read data in CI environment
    Given I configure rclone with a "Developer" account
    When I run the command to download file "test.txt" from the "CI" environment
    Then I can see "CI/test.txt" on my local filesystem

  @ok @wasabi @storage
  Scenario: Group Developers can read data in staging environment
    Given I configure rclone with a "Developer" account
    When I run the command to download file "test.txt" from the "staging" environment
    Then I can see "staging/test.txt" on my local filesystem

  @ok @wasabi @storage
  Scenario: Group Developers can read data in live environment
    Given I configure rclone with a "Developer" account
    When I run the command to download file "DoNotDelete.txt" from the "live" environment
    Then I can see "live/DoNotDelete.txt" on my local filesystem


  @ok @wasabi @storage
  Scenario: Group Developers can write data in dev environment
    Given I configure rclone with a "Developer" account
    When I run the command to upload a file to the "dev" environment
    Then I can see that file on the remote filesystem under "gigadb-datasets/dev"
  @ok @wasabi @storage
  Scenario: Group Developers can write data in CI environment
    Given I configure rclone with a "Developer" account
    When I run the command to upload a file to the "CI" environment
    Then I can see that file on the remote filesystem under "gigadb-datasets/CI"

  @ok @wasabi @storage
  Scenario: Group Developers can write data in staging environment
    Given I configure rclone with a "Developer" account
    When I run the command to upload a file to the "staging" environment
    Then I can see that file on the remote filesystem under "gigadb-datasets/staging"

  @ok @wasabi @storage
  Scenario: Group Developers can write data in live environment
    Given I configure rclone with a "Developer" account
    When I run the command to upload a file to the "live" environment
    Then I can see that file on the remote filesystem under "gigadb-datasets/live"

  @ok @wasabi @storage
  Scenario: Group Developers can delete data in dev environment
    Given I configure rclone with a "Developer" account
    And I run the command to upload a file to the "dev" environment
    When I run the command to delete the file uploaded to the "dev" environment
    Then the file is deleted from the "dev" environment

  @ok @wasabi @storage
  Scenario: Group Developers can delete data in CI environment
    Given I configure rclone with a "Developer" account
    And I run the command to upload a file to the "CI" environment
    When I run the command to delete the file uploaded to the "CI" environment
    Then the file is deleted from the "CI" environment

  @ok @wasabi @storage
  Scenario: Group Developers can delete data in staging environment
    Given I configure rclone with a "Developer" account
    And I run the command to upload a file to the "staging" environment
    When I run the command to delete the file uploaded to the "staging" environment
    Then the file is deleted from the "staging" environment

  @ok @wasabi @storage
  Scenario: Group Developers cannot delete data in live environment
    Given I configure rclone with a "Developer" account
    When I run the command to delete existing file
    Then the file is not deleted

  @ok @wasabi @storage
  Scenario: Developer assuming the Admin Role can delete data in live environment
    Given I assume the Admin role
    And I run the command to upload a file to the "live" environment
    When I run the command to delete a file on the "live" environment
    Then the file is deleted from the "live" environment

  @ok @wasabi @storage
  Scenario: User Migration can read data in live environment
    Given I configure rclone with a "Migration user" account
    When I run the command to download file "DoNotDelete.txt" from the "live" environment
    Then I can see "live/DoNotDelete.txt" on my local filesystem

  @ok @wasabi @storage
  Scenario: User Migration can write data in live environment
    Given I configure rclone with a "Migration user" account
    When I run the command to upload a file to the "live" environment
    Then I can see that file on the remote filesystem under "gigadb-datasets/live"

  @ok @wasabi @storage
  Scenario: User Migration can read data in staging environment
    Given I configure rclone with a "Migration user" account
    When I run the command to download file "test.txt" from the "staging" environment
    Then I can see "staging/test.txt" on my local filesystem

  @ok @wasabi @storage
  Scenario: User Migration can write data in staging environment
    Given I configure rclone with a "Migration user" account
    When I run the command to upload a file to the "staging" environment
    Then I can see that file on the remote filesystem under "gigadb-datasets/staging"

  @ok @wasabi @storage
  Scenario: User Migration cannot write data in CI environment
    Given I configure rclone with a "Migration user" account
    When I run the command to upload a file to the "CI" environment
    Then I cannot see that file on the remote filesystem under "gigadb-datasets/CI"

  @ok @wasabi @storage
  Scenario: User Migration cannot write data in dev environment
    Given I configure rclone with a "Migration user" account
    When I run the command to upload a file to the "dev" environment
    Then I cannot see that file on the remote filesystem under "gigadb-datasets/dev"

  @ok @wasabi @storage
  Scenario: User Migration cannot delete data in live environment
    Given I configure rclone with a "Migration user" account
    When I run the command to delete existing file
    Then the file is not deleted

  @ok @wasabi @storage
  Scenario: Group Curators can read data in live environment
    Given I configure rclone with a "Curator" account
    When I run the command to download file "DoNotDelete.txt" from the "live" environment
    Then I can see "live/DoNotDelete.txt" on my local filesystem


  Scenario: Group Curators can write data in live environment
  Scenario: Group Curators can read data in staging environment
  Scenario: Group Curators can write data in staging environment

  Scenario: Group Curators cannot write data in CI environment
  Scenario: Group Curators cannot write data in dev environment

  Scenario: Group Curators cannot delete data in live environment
