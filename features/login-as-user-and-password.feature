Feature: I login as the specfic role

  Scenario: Login as user "admin" with password "admin"

    Given the screen size is 1440x900

    When I login as "admin" with password "admin"
    Then I should be logged in
    And I should see "Dashboard"

    When I am on "/"
    Then I should see "Howdy, admin"
    And I should see "Welcome to the WordPress" in the "h1.site-title" element

    When I logout
    And I am on "/"
    Then I should not see "Howdy, admin"
    And I should see "Welcome to the WordPress" in the "h1.site-title" element

  Scenario: Login as user "admin" with incorrect password

    When I try to login as "admin" with password "1111"
    Then I should see "ERROR:"
