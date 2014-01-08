@web @base_url
Feature: Web definitions
  In order to combine the Symfony2 test driver with other drivers
  As features tester
  I need to be able to use a base URL

  Scenario: Accessing default test controller
    When I go to "/"
    Then I should be on "http://localhost/foo/"
    And I should see "Hello, stranger"
