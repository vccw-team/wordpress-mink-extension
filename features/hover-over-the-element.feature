Feature: Hover over the element

  @javascript
  Scenario: I hover over the specific element

    Given the screen size is 1440x900
    Given I login as the "administrator" role

    When I am on "/"
    And I hover over the "#wp-admin-bar-my-account" element
    And I wait for a second
    Then I should see "Edit My Profile"
    And I should see "Log Out"

    When I logout
    And I am on "/"
    Then I should not see "Howdy,"
