Feature: Take the screenshot

  @javascript
  Scenario: Take the screenshot of the current page

    Given the screen size is 1440x900
    And I login as the "administrator" role

    When I am on "/wp-admin/"
    Then take a screenshot and save it to "/tmp/test-1.png"
