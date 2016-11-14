Feature: WordPress version feature

  Scenario: WordPress version should be equal with $WP_VERSION

    When save env $WP_VERSION as {WP_VERSION}
    Then the WordPress version should be "{WP_VERSION}"
