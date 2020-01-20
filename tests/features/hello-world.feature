Feature: We can make requests and inspect the responses

  Scenario: PSR-7 app can be queried
    When I go to "?name=Ciaran"
    Then I should see "Hello Ciaran"

  Scenario: PSR-7 app can be altered at runtime
    When I go to the injected url
    Then I should see "Injected!"

  # Adding a new route with the same path would fail if the app was the same across the whole feature.
  # This tests that each scenario gets its own newly bootstrapped application instance.
  Scenario: PSR-7 app can be altered at runtime a second time doing something that can only be normally done once
    When I go to the injected url
    Then I should see "Injected!"
