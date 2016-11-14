Feature: Hover over the element

  Scenario: I hover over the specific element

    Given the screen size is 1440x900
    And I login as the "administrator" role

    When I am on "/"
    And I should be logged in
    And I wait the "#wp-admin-bar-my-account" element be loaded
    And I hover over the "#wp-admin-bar-my-account" element
    And I wait for 3 seconds
    Then I should see "Edit My Profile"
    And I should see "Log Out"

    When I logout
    And I am on "/"
    Then I should not see "Howdy,"
