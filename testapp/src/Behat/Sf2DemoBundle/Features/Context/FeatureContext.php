<?php

namespace Behat\Sf2DemoBundle\Features\Context;

use Symfony\Component\HttpKernel\KernelInterface;
use Behat\Symfony2Extension\Context\KernelAwareContext;
use PHPUnit\Framework\Assert;

// BC Layer for older PHPUnit versions.
if (!class_exists('PHPUnit\Framework\Assert')) {
    class_alias('PHPUnit_Framework_Assert', 'PHPUnit\Framework\Assert');
}

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
        Assert::assertInstanceOf('Symfony\\Component\\HttpKernel\\KernelInterface', $this->kernel);
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
        Assert::assertArrayHasKey($key, $this->containerParameters);
        $this->parameterKey = $key;
    }

    /**
     * @Then /^there should not be "([^"]*)" parameter$/
     */
    public function thereShouldNotBeParameter($key)
    {
        Assert::assertArrayNotHasKey($key, $this->containerParameters);
    }

    /**
     * @Given /^it should be set to "([^"]*)" value$/
     */
    public function itShouldBeSetToValue($val)
    {
        Assert::assertSame($val, $this->containerParameters[$this->parameterKey]);
    }

    /**
     * @Then the value should be an array
     */
    public function theValueShouldBeAnArray()
    {
        Assert::assertInternalType('array', $this->containerParameters[$this->parameterKey]);
    }

    /**
     * @Then the array should contain only the values :arg
     * @param  string $arg Comma delimited string, to represent an array's values
     */
    public function theArrayShouldContainOnlyTheValues($arg)
    {
        $values = explode(',', $arg);

        Assert::assertSame($values, $this->containerParameters[$this->parameterKey]);
    }
}
