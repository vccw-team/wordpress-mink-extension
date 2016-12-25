Feature: Get current theme

  Scenario: Get current theme

    When I login as the "administrator" role
    And save env $WP_THEME as {WP_THEME}
    Then the "{WP_THEME}" theme should be activated

  @mink:goutte
  Scenario: Get current theme

    When I login as the "administrator" role
    And save env $WP_THEME as {WP_THEME}
    Then the "{WP_THEME}" theme should be activated
