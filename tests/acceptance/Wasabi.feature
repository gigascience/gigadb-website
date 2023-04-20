Feature:
  As a developer
  I want to validate the access policies to Wasabi
  So that unaccounted for usage cannot happen

  @ok @wasabi @storage @AllowListBuckets
  Scenario: Group Developers can see list of buckets in console
    Given I configure rclone with a "Developer" account
    When I run the command to list buckets
    Then I should see the bucket "gigadb-datasets"
    And I should see the bucket "test-gigadb-datasets"

  @ok @wasabi @storage @AllowReadWriteContentOnDev
  Scenario: Group Developers can read data in dev environment
    Given I configure rclone with a "Developer" account
    When I run the command to download file "test.txt" from the "dev" environment
    Then I can see "dev/test.txt" on my local filesystem

  @ok @wasabi @storage @AllowReadWriteContentOnCI
  Scenario: Group Developers can read data in CI environment
    Given I configure rclone with a "Developer" account
    When I run the command to download file "test.txt" from the "CI" environment
    Then I can see "CI/test.txt" on my local filesystem

  @ok @wasabi @storage @AllowReadWriteContentOnStaging
  Scenario: Group Developers can read data in staging environment
    Given I configure rclone with a "Developer" account
    When I run the command to download file "test.txt" from the "staging" environment
    Then I can see "staging/test.txt" on my local filesystem

  @ok @wasabi @storage @AllowReadContentOnLive
  Scenario: Group Developers can read data in live environment
    Given I configure rclone with a "Developer" account
    When I run the command to download file "DoNotDelete.txt" from the "live" environment
    Then I can see "live/DoNotDelete.txt" on my local filesystem


  @ok @wasabi @storage @AllowReadWriteContentOnDev
  Scenario: Group Developers can write data in dev environment
    Given I configure rclone with a "Developer" account
    When I run the command to upload file "Developer_dev_writable_test.txt" to the "dev" environment
    Then I can see the file "Developer_dev_writable_test.txt" on the "dev" environment

  @ok @wasabi @storage @AllowReadWriteContentOnCI
  Scenario: Group Developers can write data in CI environment
    Given I configure rclone with a "Developer" account
    When I run the command to upload file "Developer_CI_writable_test.txt" to the "CI" environment
    Then I can see the file "Developer_CI_writable_test.txt" on the "CI" environment

  @ok @wasabi @storage @AllowReadWriteContentOnStaging
  Scenario: Group Developers can write data in staging environment
    Given I configure rclone with a "Developer" account
    When I run the command to upload file "Developer_staging_writable_test.txt" to the "staging" environment
    Then I can see the file "Developer_staging_writable_test.txt" on the "staging" environment

  @ok @wasabi @storage
  Scenario: Group Developers cannot write data in live environment
    Given I configure rclone with a "Developer" account
    When I run the command to upload file "Developer_live_unwritable_test.txt" to the "live" environment
    Then I cannot see the file "Developer_live_unwritable_test.txt" on the "CI" environment

  @ok @wasabi @storage @AllowDeleteContentOnDev
  Scenario: Group Developers can delete data in dev environment
    Given I configure rclone with a "Developer" account
    And I run the command to upload file "Developer_dev_deletable_test.txt" to the "dev" environment
    When I run the command to delete the file "Developer_dev_deletable_test.txt" uploaded to the "dev" environment
    Then the file "Developer_dev_deletable_test.txt" is deleted from the "dev" environment

  @ok @wasabi @storage @AllowDeleteContentOnCI
  Scenario: Group Developers can delete data in CI environment
    Given I configure rclone with a "Developer" account
    And I run the command to upload file "Developer_CI_deletable_test.txt" to the "CI" environment
    When I run the command to delete the file "Developer_CI_deletable_test.txt" uploaded to the "CI" environment
    Then the file "Developer_CI_deletable_test.txt" is deleted from the "CI" environment

  @ok @wasabi @storage @AllowDeleteContentOnStaging
  Scenario: Group Developers can delete data in staging environment
    Given I configure rclone with a "Developer" account
    And I run the command to upload file "Developer_staging_deletable_test.txt" to the "staging" environment
    When I run the command to delete the file "Developer_staging_deletable_test.txt" uploaded to the "staging" environment
    Then the file "Developer_staging_deletable_test.txt" is deleted from the "staging" environment

  @ok @wasabi @storage @DenyDeleteContentOnLive
  Scenario: Group Developers cannot delete data in live environment
    Given I configure rclone with a "Developer" account
    When I run the command to delete existing file
    Then the file is not deleted

  @ok @wasabi @storage @AdminRole
  Scenario: Developer assuming the Admin Role can delete data in live environment
    Given I assume the Admin role
    And I run the command to upload file "DeveloperAsAdmin_live_deletable_test.txt" to the "live" environment
    When I run the command to delete the file "DeveloperAsAdmin_live_deletable_test.txt" on the "live" environment
    Then the file "DeveloperAsAdmin_live_deletable_test.txt" is deleted from the "live" environment

  @ok @wasabi @storage  @AllowReadWriteContentOnLive
  Scenario: User Migration can read data in live environment
    Given I configure rclone with a "Migration user" account
    When I run the command to download file "DoNotDelete.txt" from the "live" environment
    Then I can see "live/DoNotDelete.txt" on my local filesystem

  @ok @wasabi @storage @AllowReadWriteContentOnLive
  Scenario: User Migration can write data in live environment
    Given I configure rclone with a "Migration user" account
    When I run the command to upload file "Migration_live_writable_test.txt" to the "live" environment
    Then I can see the file "Migration_live_writable_test.txt" on the "live" environment

  @ok @wasabi @storage @AllowReadWriteContentOnStaging
  Scenario: User Migration can read data in staging environment
    Given I configure rclone with a "Migration user" account
    When I run the command to download file "test.txt" from the "staging" environment
    Then I can see "staging/test.txt" on my local filesystem

  @ok @wasabi @storage @AllowReadWriteContentOnStaging
  Scenario: User Migration can write data in staging environment
    Given I configure rclone with a "Migration user" account
    When I run the command to upload file "Migration_staging_writable_test.txt" to the "staging" environment
    Then I can see the file "Migration_staging_writable_test.txt" on the "staging" environment

  @ok @wasabi @storage
  Scenario: User Migration cannot write data in CI environment
    Given I configure rclone with a "Migration user" account
    When I run the command to upload file "Migration_CI_unwritable_test.txt" to the "CI" environment
    Then I cannot see the file "Migration_CI_unwritable_test.txt" on the "CI" environment

  @ok @wasabi @storage
  Scenario: User Migration cannot write data in dev environment
    Given I configure rclone with a "Migration user" account
    When I run the command to upload file "Migration_dev_unwritable_test.txt" to the "dev" environment
    Then I cannot see the file "Migration_dev_unwritable_test.txt" on the "dev" environment

  @ok @wasabi @storage @DenyDeleteContentOnLive
  Scenario: User Migration cannot delete data in live environment
    Given I configure rclone with a "Migration user" account
    When I run the command to delete existing file
    Then the file is not deleted

  @ok @wasabi @storage @AllowReadWriteContentOnLive
  Scenario: Group Curators can read data in live environment
    Given I configure rclone with a "Curator" account
    When I run the command to download file "DoNotDelete.txt" from the "live" environment
    Then I can see "live/DoNotDelete.txt" on my local filesystem

  @ok @wasabi @storage @AllowReadWriteContentOnLive
  Scenario: Group Curators can write data in live environment
    Given I configure rclone with a "Curator" account
    When I run the command to upload file "Curator_live_writable_test.txt" to the "live" environment
    Then I can see the file "Curator_live_writable_test.txt" on the "live" environment

  @ok @wasabi @storage @AllowReadWriteContentOnStaging
  Scenario: Group Curators can read data in staging environment
    Given I configure rclone with a "Curator" account
    When I run the command to download file "test.txt" from the "staging" environment
    Then I can see "staging/test.txt" on my local filesystem

  @ok @wasabi @storage @AllowReadWriteContentOnStaging
  Scenario: Group Curators can write data in staging environment
    Given I configure rclone with a "Curator" account
    When I run the command to upload file "Curator_staging_writable_test.txt" to the "staging" environment
    Then I can see the file "Curator_staging_writable_test.txt" on the "staging" environment

  @ok @wasabi @storage
  Scenario: Group Curators cannot write data in CI environment
    Given I configure rclone with a "Curator" account
    When I run the command to upload file "Curator_CI_unwritable_test.txt" to the "CI" environment
    Then I cannot see the file "Curator_CI_unwritable_test.txt" on the "CI" environment

  @ok @wasabi @storage
  Scenario: Group Curators cannot write data in dev environment
    Given I configure rclone with a "Curator" account
    When I run the command to upload file "Curator_dev_unwritable_test.txt" to the "dev" environment
    Then I cannot see the file "Curator_dev_unwritable_test.txt" on the "dev" environment

  @ok @wasabi @storage @DenyDeleteContentOnLive
  Scenario: Group Curators cannot delete data in live environment
    Given I configure rclone with a "Curator" account
    When I run the command to delete existing file
    Then the file is not deleted

  @ok @wasabi @storage @AllowListBuckets
  Scenario: Group Curators can see list of buckets in console
    Given I configure rclone with a "Curator" account
    When I run the command to list buckets
    Then I should see the bucket "gigadb-datasets"
    And I should see the bucket "test-gigadb-datasets"

  @ok @wasabi @storage @AllowCreateGigadbBucket
  Scenario: Group Curators can create bucket for gigadb user
    Given I configure rclone with a "Curator" account
    When I run the command to create bucket "bucket-giga-d-23-12345"
    Then I should see the bucket "bucket-giga-d-23-12345"
    
  @ok @wasabi @storage @AllowCreateGigabyteBucket
  Scenario: Group Curators can create bucket for gigabyte user
    Given I configure rclone with a "Curator" account
    When I run the command to create bucket "bucket-drr-123456-12"
    And I run the command to create bucket "bucket-trr-654321-67"
    Then I should see the bucket "bucket-drr-123456-12"
    And I should see the bucket "bucket-trr-654321-67"
    
