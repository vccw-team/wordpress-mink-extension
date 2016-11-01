Feature: When I login as the specfic role

  @javascript
  Scenario: Login with administrator

    Given the screen size is 1440x900
    And I am on "/"

    When I login as the "administrator" role
    Then I should see "Howdy,"

    When I logout
    Then I should not see "Howdy,"
