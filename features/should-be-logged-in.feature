Feature: I should be logged in or not

  Scenario: Login as the "administrator" role then logout

    Given the screen size is 1440x900

    When I login as the "administrator" role
    Then I should be logged in

    When I logout
    Then I should not be logged in

  @mink:goutte
  Scenario: Login as the "administrator" role then logout with goutte driver

    When I login as the "administrator" role
    Then I should be logged in

    When I logout
    Then I should not be logged in
