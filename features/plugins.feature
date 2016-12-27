Feature: Get statuses of plugins

  Scenario: Tests for plugins

    When I login as the "administrator" role
    Then the plugins should be:
      | slug               | status   |
      | akismet            | inactive |
      | wordpress-importer | active   |
    And the "akismet" plugin should be installed
    And the "wordpress-importer" plugin should be activated
    And the "my-plugin" plugin should not be installed
    And the "akismet" plugin should not be activated
    And I activate the "hello-dolly" plugin
    And I deactivate the "hello-dolly" plugin

  @mink:goutte
  Scenario: Get plugins with goutte driver

    When I login as the "administrator" role
    Then the plugins should be:
      | slug               | status   |
      | hello-dolly        | inactive |
      | wordpress-importer | active   |
    And the "akismet" plugin should be installed
    And the "wordpress-importer" plugin should be activated
    And the "my-plugin" plugin should not be installed
    And the "akismet" plugin should not be activated
    And I activate the "hello-dolly" plugin
    And I deactivate the "hello-dolly" plugin
