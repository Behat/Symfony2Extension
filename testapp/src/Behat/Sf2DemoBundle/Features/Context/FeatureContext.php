<?php

namespace Behat\Sf2DemoBundle\Features\Context;

use Symfony\Component\HttpKernel\KernelInterface;
use Behat\Symfony2Extension\Context\KernelAwareInterface;
use Behat\MinkExtension\Context\MinkContext;

use Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;

require_once 'PHPUnit/Autoload.php';
require_once 'PHPUnit/Framework/Assert/Functions.php';

class FeatureContext extends BehatContext implements KernelAwareInterface
{
    private $kernel;
    private $parameters;
    private $containerParameters;
    private $parameterKey;

    /**
     * Initializes context with parameters from behat.yml.
     *
     * @param array $parameters
     */
    public function __construct(array $parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * Sets HttpKernel instance.
     * This method will be automatically called by Symfony2Extension ContextInitializer.
     *
     * @param KernelInterface $kernel
     */
    public function setKernel(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * @Given /^I have a kernel instance$/
     */
    public function iHaveAKernelInstance()
    {
        assertInstanceOf('Symfony\\Component\\HttpKernel\\KernelInterface', $this->kernel);
    }

    /**
     * @When /^I get container parameters from it$/
     */
    public function iGetContainerParametersFromIt()
    {
        $this->containerParameters = $this->kernel->getContainer()->getParameterBag()->all();
    }

    /**
     * @Then /^there should be "([^"]*)" parameter$/
     */
    public function thereShouldBeParameter($key)
    {
        assertArrayHasKey($key, $this->containerParameters);
        $this->parameterKey = $key;
    }

    /**
     * @Then /^there should not be "([^"]*)" parameter$/
     */
    public function thereShouldNotBeParameter($key)
    {
        assertArrayNotHasKey($key, $this->containerParameters);
    }

    /**
     * @Given /^it should be set to "([^"]*)" value$/
     */
    public function itShouldBeSetToValue($val)
    {
        assertSame($val, $this->containerParameters[$this->parameterKey]);
    }
}
