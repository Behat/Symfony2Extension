Feature: Bundle locator
  In order to filter the exercise easily
  As a Symfony feature tester
  I want to be able to use a bundle reference

  Scenario: Features should be loaded from the bundle
    When I run "behat --no-colors '@BehatSf2DemoBundle'"
    Then it should pass with:
      """
      4 scenarios (4 passed)
      """

  Scenario: Specific features should be loaded from the bundle
    When I run "behat --no-colors '@BehatSf2DemoBundle/web.feature'"
    Then it should pass with:
      """
      2 scenarios (2 passed)
      """
