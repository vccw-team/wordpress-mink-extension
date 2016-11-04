Feature: I wait something happen

  @javascript
  Scenario: I wait the specific element be loaded

    Given the screen size is 1440x900
    And I login as the "administrator" role

    When I am on "/"
    And I wait the "#wpadminbar" element be loaded
    Then I should see "Howdy,"

    When I logout
    And I am on "/"
    Then I should not see "Howdy,"

  @javascript
  Scenario: I wait for a second

    Given the screen size is 1440x900
    And I login as the "administrator" role

    When I am on "/"
    And I hover over the "#wp-admin-bar-my-account" element
    And I wait for a second
    Then I should see "Edit My Profile"
    And I should see "Log Out"

  @javascript
  Scenario: I wait for 5 seconds

    Given the screen size is 1440x900
    And I login as the "administrator" role

    When I am on "/"
    And I hover over the "#wp-admin-bar-my-account" element
    And I wait for 5 seconds
    Then I should see "Edit My Profile"
    And I should see "Log Out"
