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

  Scenario Outline: I activate plugin.
    When I login as the "administrator" role
    Then I activate the "<slug>" plugin

    Examples:
      | slug        |
      | akismet     |
      | hello-dolly |

  @mink:goutte
  Scenario: Get plugins with goutte driver

    When I login as the "administrator" role
    Then the plugins should be:
      | slug               | status   |
      | akismet            | inactive |
      | hello-dolly        | inactive |
      | wordpress-importer | active   |

  @mink:goutte
  Scenario Outline: Plugin should be installed with goutte driver

    When I login as the "administrator" role
    Then the "<slug>" plugin should be installed

    Examples:
      | slug               |
      | akismet            |
      | hello-dolly        |
      | wordpress-importer |

  @mink:goutte
  Scenario Outline: Plugins should be activated.

    When I login as the "administrator" role
    Then the "<slug>" plugin should be activated

    Examples:
      | slug               |
      | wordpress-importer |

  @mink:goutte
  Scenario: Plugin should not be installed.

    When I login as the "administrator" role
    Then the "my-plugin" plugin should not be installed

  @mink:goutte
  Scenario Outline: Plugins should not be activated.

    When I login as the "administrator" role
    Then the "<slug>" plugin should not be activated

    Examples:
      | slug               |
      | akismet            |
      | hello-dolly        |

  @mink:goutte
  Scenario Outline: I activate plugin.
    When I login as the "administrator" role
    Then I activate the "<slug>" plugin

    Examples:
      | slug        |
      | akismet     |
      | hello-dolly |
