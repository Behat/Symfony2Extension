Feature: Bundle suites
  In order to define suites easily in a bundle
  As a Symfony feature tester
  I should be able to rely on some bundle conventions

  Scenario: Features should be loaded from the bundle
    When I run "behat -s simple --no-colors"
    Then it should pass with:
      """
      1 scenario (1 passed)
      """

  Scenario: Features should be loaded from all bundle suites
    When I run "behat --no-colors"
    Then it should pass with:
      """
      3 scenarios (3 passed)
      """
