Feature: I wait something happen

  @javascript
  Scenario: I wait the specific element be loaded

    Given the screen size is 1440x900
    And I login as the "administrator" role

    When I am on "/"
    And I wait the "#wpadminbar" element be loaded
    Then I should see "Howdy,"

    When I am on "/"
    And I wait for a second
    Then I should see "Welcome"

    When I am on "/"
    And I wait for 5 seconds
    Then I should see "Welcome"
