Feature: Get statuses of plugins

  Scenario: Get plugins

    When I login as the "administrator" role
    Then the plugins should be:
      | slug               | status   |
      | akismet            | inactive |
      | hello-dolly        | inactive |
      | wordpress-importer | active   |

  Scenario: Plugin should be installed.

    When I login as the "administrator" role
    Then the "akismet" plugin should be installed

  Scenario: Plugins should be activated.

    When I login as the "administrator" role
    Then the "wordpress-importer" plugin should be activated

  Scenario: Plugin should not be installed.

    When I login as the "administrator" role
    Then the "my-plugin" plugin should not be installed

  Scenario: Plugins should not be activated.

    When I login as the "administrator" role
    Then the "hello-dolly" plugin should not be activated

  Scenario: I activate plugin.
    When I login as the "administrator" role
    Then I activate the "hello-dolly" plugin

  Scenario: I deactivate plugin.
    When I login as the "administrator" role
    Then I deactivate the "hello-dolly" plugin

  @mink:goutte
  Scenario: Get plugins with goutte driver

    When I login as the "administrator" role
    Then the plugins should be:
      | slug               | status   |
      | akismet            | inactive |
      | hello-dolly        | inactive |
      | wordpress-importer | active   |

  @mink:goutte
  Scenario: Plugin should be installed.

    When I login as the "administrator" role
    Then the "akismet" plugin should be installed

  @mink:goutte
  Scenario: Plugins should be activated.

    When I login as the "administrator" role
    Then the "wordpress-importer" plugin should be activated

  @mink:goutte
  Scenario: Plugin should not be installed.

    When I login as the "administrator" role
    Then the "my-plugin" plugin should not be installed

  @mink:goutte
  Scenario: Plugins should not be activated.

    When I login as the "administrator" role
    Then the "hello-dolly" plugin should not be activated

  @mink:goutte
  Scenario: I activate plugin.
    When I login as the "administrator" role
    Then I activate the "hello-dolly" plugin
