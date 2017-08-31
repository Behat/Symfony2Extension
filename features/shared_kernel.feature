Feature: Sharing Behat's kernel with the application if config stipulates

    In order to be able to re-use in-memory services between scenario steps
    As a Symfony feature tester
    I need to be able to share Behat's DI container with the app

    Scenario: Fails to retain service when shared kernel set to false
        Given I have not configured behat to use shared kernel
        When I run "behat -s web -p no-shared-kernel --no-colors '@BehatSf2DemoBundle/services.feature'"
        Then it should fail
        And the output should contain:
          """
          -'Jim'
          """

    Scenario: Does retain service when shared kernel set to true
        Given I have configured behat to use shared kernel
        When I run "behat -s web -p shared-kernel --no-colors '@BehatSf2DemoBundle/services.feature'"
        Then it should pass with:
          """
          1 scenario (1 passed)
          """
