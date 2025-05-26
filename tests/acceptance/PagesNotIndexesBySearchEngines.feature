Feature:
  As a website user
  I do not want all search engines to recognise staging and new live GigaDB page urls
  So that pages under development would not be released

  @ok
  Scenario: Search engines cannot index and follow faq page
    When I am on "site/faq"
    Then the meta tag should contain "robots" with "noindex, nofollow"
    And the meta tag should contain "googlebot" with "noindex, nofollow"

  @ok
  Scenario: Search engines cannot index and follow main page
    When I am on "/"
    Then the meta tag should contain "robots" with "noindex, nofollow"
    And the meta tag should contain "googlebot" with "noindex, nofollow"

  @ok
  Scenario: Search engines cannot index and follow dataset page
    When I am on "/100006"
    Then the meta tag should contain "robots" with "noindex, nofollow"
    And the meta tag should contain "googlebot" with "noindex, nofollow"
