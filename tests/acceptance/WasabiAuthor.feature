Feature:
  As a curator
  I want to create a new private user dropbox account on Wasabi
  So that I enable authors to upload the file for their submitted manuscripts

  @ok @wasabi @AllowCreateGigadbBucket
  Scenario: Group Curators can create bucket for gigadb user
    Given I configure rclone with a "Chris" account
    When I run the command to create bucket "bucket-giga-d-23-12345"
    Then I should see the bucket "bucket-giga-d-23-12345"

  @ok @wasabi @NotAllowDeleteGigadbBucket
  Scenario: Group Curators cannot delete bucket for gigadb user
    Given I configure rclone with a "Chris" account
    When I should see the bucket "bucket-giga-d-23-12345"
    Then I cannot delete the bucket "bucket-giga-d-23-12345"

  @wip @wasabi @AllowCreateGigabyteBucket
  Scenario: Group Curators can create bucket for gigabyte user
    Given I configure rclone with a "Chris" account
    When I run the command to create bucket "bucket-drr-123456-12"
    And I run the command to create bucket "bucket-trr-654321-67"
    Then I should see the bucket "bucket-drr-123456-12"
    And I should see the bucket "bucket-trr-654321-67"

  @wip @wasabi @NotAllowDeleteGigabyteBucket
  Scenario: Group Curators cannot delete bucket for gigabyte user
    Given I configure rclone with a "Chris" account
    When I should see the bucket "bucket-drr-123456-12"
    And I should see the bucket "bucket-trr-654321-67"
    Then I cannot delete the bucket "bucket-drr-123456-12"
    And I cannot delete the bucket "bucket-trr-654321-67"

  @wip @wasabi @NotAllowCreateBucketWithRandomName
  Scenario: Group Curators cannot create bucket with random name
    Given I configure rclone with a "Chris" account
    When I run the command to create bucket "giga-d-23-12345"
    And I run the command to create bucket "bucket-d-23-12345"
    And I run the command to create bucket "bucket-1234-d-23-12345"
    And I run the command to create bucket "bucket-giga-1-23-12345"
    And I run the command to create bucket "bucket-gi12-d-23-12345"
    And I run the command to create bucket "drr-123456-12"
    And I run the command to create bucket "trr-123456-12"
    And I run the command to create bucket "bucket-arr-123456-12"
    And I run the command to create bucket "bucket-drt-123456-12"
    And I run the command to create bucket "bucket-789-123456-12"
    Then I should not see the bucket "giga-d-23-12345"
    And I should not see the bucket "bucket-d-23-12345"
    And I should not see the bucket "bucket-1234-d-23-12345"
    And I should not see the bucket "bucket-giga-1-23-12345"
    And I should not see the bucket "bucket-gi12-d-23-12345"
    And I should not see the bucket "drr-123456-12"
    And I should not see the bucket "trr-123456-12"
    And I should not see the bucket "bucket-arr-123456-12"
    And I should not see the bucket "bucket-drt-123456-12"
    And I should not see the bucket "bucket-789-123456-12"


