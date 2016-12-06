@web
Feature: Web definitions
  In order to describe/test web part of the feature suite
  As features tester
  I need to be able to use Mink definitions and Symfony2 test driver

  Scenario: Accessing default test controller
    When I go to "/"
    Then I should see "Hello, stranger"

  Scenario: Clicking on links
    Given I am on "/"
    When I follow "Orc"
    Then I should see "Hello, orc"

  Scenario: Container services injected into Context classes should be the same as the application for the whole scenario
    Given I start with a correct session object
    When I am on "/"
    And I follow "Orc"
    Then I should have the same instance of session
