Feature: Logout from the WordPress

  Scenario: Logout

    Given the screen size is 1440x900
    And I login as the "administrator" role

    When I logout
    Then I should see "You are now logged out."
    And I am not logged in
