Feature: I login as the specfic role

  Scenario: Login as user "admin" with password "admin"

    When I login as "admin" with password "admin"
    Then I should be logged in
    And I should see "Dashboard"

  Scenario: Login as user "admin" with incorrect password

    When I try to login as "admin" with password "1111"
    Then I should see "ERROR:"

  @mink:goutte
  Scenario: Login as user "admin" with password "admin" with goutte driver

    When I login as "admin" with password "admin"
    Then I should be logged in
    And I should see "Dashboard"

  @mink:goutte
  Scenario: Login as user "admin" with incorrect password with goutte driver

    When I try to login as "admin" with password "1111"
    Then I should see "ERROR:"
