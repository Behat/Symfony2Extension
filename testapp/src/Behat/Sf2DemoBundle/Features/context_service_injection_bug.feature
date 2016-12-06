@web
Feature: Context service injection bug
  Once a request has been made with $contianer->get('test.client')->request()
  the Kernel is shutdown, and subsequent requests have to re-initialize the
  container & bundles.
  This means that services injected into the constructor of a Context file are
  not the same as those found in $contianer->get('test.client')->getContainer()

  Scenario: Container services injected into Context classes should be equal to those in the test.client
    Given I start with a correct session object
    When I am on "/"
    And I follow "Orc"
    Then I should have the same instance of session
