Feature: The screen size

  @javascript
  Scenario: The screen size is 1440x900
    Given I am on "/wp-admin/"
    And I login as the "administrator" role

    When the screen size is 1440x900
    Then I should see "Dashboard" in the "#adminmenu" element

    When the screen size is 320x400
    Then I should not see "Dashboard" in the "#adminmenu" element
