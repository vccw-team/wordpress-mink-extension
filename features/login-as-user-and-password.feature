Feature: I login as the specfic role

  @javascript
  Scenario: Login as user "admin" with password "admin"

    Given the screen size is 1440x900
    Given I login as "admin" with password "admin"

    When I am on "/"
    Then I should see "Howdy, admin"
    And I should see "Welcome to the WordPress" in the "h1.site-title" element

    When I logout
    And I am on "/"
    Then I should not see "Howdy, admin"
    And I should see "Welcome to the WordPress" in the "h1.site-title" element
