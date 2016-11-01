Feature: Logout from the WordPress

  @javascript
  Scenario: Logout

    Given the screen size is 1440x900
    Given I login as the "administrator" role

    When I logout
    Then I should see "You are now logged out."

  @javascript
  Scenario: I am a anonymous

    Given the screen size is 1440x900

    When I am not logged in
    And I am on "/"
    Then I should not see "Howdy, admin"
