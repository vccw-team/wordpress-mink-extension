Feature: Logout from the WordPress

  Scenario: Logout

    Given I login as the "administrator" role

    When I logout
    Then I should see "You are now logged out."
    And I am not logged in

  @mink:goutte
  Scenario: Logout with goutte driver

    Given I login as the "administrator" role

    When I logout
    Then I should see "You are now logged out."
    And I am not logged in
