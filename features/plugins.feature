Feature: Get statuses of plugins

  Scenario: Get plugins

    When I login as the "administrator" role
    Then the plugins should be:
      | slug               | status   |
      | akismet            | inactive |
      | hello-dolly        | inactive |
      | wordpress-importer | active   |

  Scenario Outline: Plugin should be installed.

    When I login as the "administrator" role
    Then the "<slug>" plugin should be installed

    Examples:
      | slug               |
      | akismet            |
      | hello-dolly        |
      | wordpress-importer |

  Scenario Outline: Plugins should be activated.

    When I login as the "administrator" role
    Then the "<slug>" plugin should be activated

    Examples:
      | slug               |
      | wordpress-importer |

  Scenario: Plugin should not be installed.

    When I login as the "administrator" role
    Then the "my-plugin" plugin should not be installed

  Scenario Outline: Plugins should not be activated.

    When I login as the "administrator" role
    Then the "<slug>" plugin should not be activated

    Examples:
      | slug               |
      | akismet            |
      | hello-dolly        |

