Feature: HTTP status

  Scenario: Check http status code

    When I am on "/"
    Then the HTTP status should be 200

    When I am on "/the-page-not-found"
    Then the HTTP status should be 404

