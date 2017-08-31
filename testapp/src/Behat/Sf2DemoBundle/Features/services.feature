Feature: Retaining container between requests

  @shared-kernel
  Scenario: Service retains value between request and assertion for duration of scenario
    Given I have a service set to "Jack"
    And I am on "/"
    When make a request setting name to "Jim"
    Then the service value should have changed to "Jim"
