Feature: HTTP response

  @mink:goutte
  Scenario: Check http status code

    When I am on "/"
    Then the HTTP status should be 200

    When I am on "/the-page-not-found"
    Then the HTTP status should be 404

  @mink:goutte
  Scenario: Check http response headers

    When I am on "/"
    Then the HTTP headers should be:
      | header        | value                    |
      | Content-Type  | text/html; charset=UTF-8 |
      | Connection    | close                    |
      | Host          | 127.0.0.1:8080           |
