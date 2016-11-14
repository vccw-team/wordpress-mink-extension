Feature: I login as the specfic role

  Scenario: Login as user "admin" with password "admin"

    Given the screen size is 1440x900

    When I login as "admin" with password "admin"
    Then I should be logged in
    And I should see "Dashboard"

  Scenario: Login as user "admin" with incorrect password

    When I try to login as "admin" with password "1111"
    Then I should see "ERROR:"
