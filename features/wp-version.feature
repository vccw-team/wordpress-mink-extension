Feature: I wait something happen

  @javascript
  Scenario: I wait the specific element be loaded
    Given save env $WP_VERSION as {WP_VERSION}
    And the WordPress version should be "{WP_VERSION}"

    When I am on "/"
    Then I should see "Welcome to the WordPress"
