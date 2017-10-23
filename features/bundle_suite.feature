Feature: Bundle suites
  In order to define suites easily in a bundle
  As a Symfony feature tester
  I should be able to rely on some bundle conventions

  Scenario: Features should be loaded from the bundle
    When I run "behat -s simple --no-colors"
    Then it should pass with:
      """
      2 scenarios (2 passed)
      """

  Scenario: Features should be loaded from all bundle suites
    When I run "behat --no-colors"
    Then it should pass with:
      """
      4 scenarios (4 passed)
      """
