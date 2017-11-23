# UPGRADE FROM 2.x to 3.0

* Arguments from your Symfony application container need to be double quoted.
  
  Before:
  ```yaml
  default:
      suites:
          web:
              contexts:
                  - Behat\Sf2DemoBundle\Features\Context\WebContext:
                        simpleParameter: "%%custom_app%%"
  ```
  
  After:
  ```yaml
  default:
      suites:
          web:
              contexts:
                  - Behat\Sf2DemoBundle\Features\Context\WebContext:
                        simpleParameter: "%%%%custom_app%%%%"
  ```
