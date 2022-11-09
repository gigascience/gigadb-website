Feature:
  As a developer
  I want to validate
  the access policies to Wasabi
  So that unaccounted for usage cannot happen


  Scenario: Group Developers can see list of buckets in console
  Scenario: Group Developers cannot manage IAM Users and keys

  Scenario: Group Developers can read data in dev environment
  Scenario: Group Developers can read data in CI environment
  Scenario: Group Developers can read data in staging environment
  Scenario: Group Developers can read data in live environment

  Scenario: Group Developers can write data in dev environment
  Scenario: Group Developers can write data in CI environment
  Scenario: Group Developers can write data in staging environment
  Scenario: Group Developers can write data in live environment

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
