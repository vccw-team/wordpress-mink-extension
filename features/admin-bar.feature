Feature: Admin bar

  Scenario: Showing the Homepage

    When I am on "/"
    Then I should see "Welcome to the WordPress"
    And I should see "Welcome to the WordPress" in the "h1.site-title" element

  @javascript
  Scenario: Showing the Homepage with login

    Given the screen size is 1440x900
    And I login as "admin" with password "admin"

    When I am on "/"
    And I wait the "#wpadminbar" element be loaded
    Then I should see "Howdy,"

    When I hover over the "#wp-admin-bar-my-account" element
    And I wait for 1 seconds
    Then I should see "Edit My Profile"
    And I should see "Log Out"

    When I logout
    Then I should not see "Howdy,"
