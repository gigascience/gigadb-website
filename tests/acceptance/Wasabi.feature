Feature:
  As a developer
  I want to validate the access policies to Wasabi
  So that unaccounted for usage cannot happen

  @ok @wasabi @storage
  Scenario: Group Developers can see list of buckets in console
    Given I configure rclone with a Developer account
    When I run the command to list buckets
    Then I should see the list of buckets

  @ok @wasabi @storage
  Scenario: Group Developers can read data in dev environment
    Given I configure rclone with a Developer account
    When I run the command to download file "test.txt" from the "dev" environment
    Then I can see "dev/test.txt" on my local filesystem

  @ok @wasabi @storage
  Scenario: Group Developers can read data in CI environment
    Given I configure rclone with a Developer account
    When I run the command to download file "test.txt" from the "CI" environment
    Then I can see "CI/test.txt" on my local filesystem

  @ok @wasabi @storage
  Scenario: Group Developers can read data in staging environment
    Given I configure rclone with a Developer account
    When I run the command to download file "test.txt" from the "staging" environment
    Then I can see "staging/test.txt" on my local filesystem

  @ok @wasabi @storage
  Scenario: Group Developers can read data in live environment
    Given I configure rclone with a Developer account
    When I run the command to download file "test.txt" from the "live" environment
    Then I can see "live/test.txt" on my local filesystem

  Scenario: Group Developers can write data in dev environment
  Scenario: Group Developers can write data in CI environment
  Scenario: Group Developers can write data in staging environment
  Scenario: Group Developers can write data in live environment

  Scenario: Group Developers cannot manage IAM Users and keys
  Scenario: Group Developers cannot delete data in live environment

  Scenario: User Migration can read data in live environment
  Scenario: User Migration can write data in live environment
  Scenario: User Migration can read data in staging environment
  Scenario: User Migration can write data in staging environment

  Scenario: User Migration cannot write data in CI environment
  Scenario: User Migration cannot write data in dev environment

  Scenario: User Migration cannot delete data in live environment

  Scenario: Role Admin can delete data in live environment
  Scenario: Role Admin can manage IAM Users and keys


  Scenario: Group Curators can read data in live environment
  Scenario: Group Curators can write data in live environment
  Scenario: Group Curators can read data in staging environment
  Scenario: Group Curators can write data in staging environment

  Scenario: Group Curators cannot write data in CI environment
  Scenario: Group Curators cannot write data in dev environment

  Scenario: Group Curators cannot delete data in live environment
