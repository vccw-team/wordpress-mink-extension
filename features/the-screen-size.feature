Feature: The screen size

  @javascript
  Scenario: The screen size is 1440x900

    Given the screen size is 1440x900
    And I login as the "administrator" role

    When I am on "/wp-admin/"
    Then I should see "Dashboard" in the "#adminmenu" element

  @javascript
  Scenario: The screen size is 320x400

    Given the screen size is 320x400
    And I login as the "administrator" role
    And take the screenshot to "/Users/miyauchi/Desktop/1.png"

    When I am on "/wp-admin/"
    Then I should not see "Dashboard" in the "#adminmenu" element
