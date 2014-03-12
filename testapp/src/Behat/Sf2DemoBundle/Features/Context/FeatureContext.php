<?php

namespace Behat\Sf2DemoBundle\Features\Context;

use Symfony\Component\HttpKernel\KernelInterface;
use Behat\Symfony2Extension\Context\KernelAwareContext;

class FeatureContext implements KernelAwareContext
{
    private $kernel;
    private $containerParameters;
    private $parameterKey;

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
        \PHPUnit_Framework_Assert::assertInstanceOf('Symfony\\Component\\HttpKernel\\KernelInterface', $this->kernel);
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
        \PHPUnit_Framework_Assert::assertArrayHasKey($key, $this->containerParameters);
        $this->parameterKey = $key;
    }

    /**
     * @Then /^there should not be "([^"]*)" parameter$/
     */
    public function thereShouldNotBeParameter($key)
    {
        \PHPUnit_Framework_Assert::assertArrayNotHasKey($key, $this->containerParameters);
    }

    /**
     * @Given /^it should be set to "([^"]*)" value$/
     */
    public function itShouldBeSetToValue($val)
    {
        \PHPUnit_Framework_Assert::assertSame($val, $this->containerParameters[$this->parameterKey]);
    }
}
