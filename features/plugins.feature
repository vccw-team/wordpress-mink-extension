Feature: Get statuses of plugins

  Scenario: Get plugins

    When I login as the "administrator" role
    Then plugins should be:
      | slug               | status   |
      | akismet            | inactive |
      | hello-dolly        | inactive |
      | wordpress-importer | active   |

  Scenario Outline: Plugins should be installed.

    When I login as the "administrator" role
    Then the "<slug>" plugins should be installed

    Examples:
      | slug               |
      | akismet            |
      | hello-dolly        |
      | wordpress-importer |

  Scenario Outline: Plugins should be activated.

    When I login as the "administrator" role
    Then the "<slug>" plugins should be activated

    Examples:
      | slug               |
      | wordpress-importer |

  Scenario: Plugins should not be installed.

    When I login as the "administrator" role
    Then the "my-plugin" plugins should not be installed

  Scenario Outline: Plugins should not be activated.

    When I login as the "administrator" role
    Then the "<slug>" plugins should not be activated

    Examples:
      | slug               |
      | akismet            |
      | hello-dolly        |

  Scenario Outline: I activate plugin.
    When I login as the "administrator" role
    Then I activate the "<slug>" plugin

    Examples:
      | slug        |
      | akismet     |
      | hello-dolly |
