<?php

namespace Behat\Sf2DemoBundle\Features\Context;

use Behat\MinkExtension\Context\MinkContext;
use Behat\Symfony2Extension\Context\KernelAwareContext;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\KernelInterface;

class WebContext extends MinkContext implements KernelAwareContext
{
    private $kernel;
    private $session;

    public function __construct(Session $session, $simpleArg)
    {
        $this->session = $session;
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
     * @Given /^I start with a correct session object$/
     * @Then /^I should have the same instance of session$/
     */
    public function iShouldHaveTheSameInstanceOfSession()
    {
        \PHPUnit_Framework_Assert::assertSame($this->session, $this->kernel->getContainer()->get('session'));
    }
}
