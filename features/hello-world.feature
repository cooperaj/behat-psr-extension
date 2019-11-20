Feature: We can make requests and inspect the responses

  Scenario: PSR-7 app can be queried
    When I go to "?name=Ciaran"
    Then I should see "Hello Ciaran"

  Scenario: PSR-7 app can be altered at runtime
    When I go to the injected url "?name=Ciaran"
    Then I should see "Injected!"

  Scenario: PSR-7 app can be altered at runtime a second time doing something that can only be normally done once
    When I go to the injected url "?name=Adam"
    Then I should see "Injected!"
