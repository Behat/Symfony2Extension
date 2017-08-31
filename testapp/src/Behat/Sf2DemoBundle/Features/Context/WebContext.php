<?php

namespace Behat\Sf2DemoBundle\Features\Context;

use Behat\MinkExtension\Context\MinkContext;
use Behat\Sf2DemoBundle\Service\NameService;
use Behat\Symfony2Extension\Context\KernelAwareContext;
use PHPUnit\Framework\Assert;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\KernelInterface;

class WebContext extends MinkContext implements KernelAwareContext
{
    private $kernel;
    
    /**
     * @var NameService
     */
    private $nameService;

    public function __construct(Session $session, $simpleParameter, $simpleArg, array $services, array $params, NameService $nameService)
    {
        $this->nameService = $nameService;
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
     * @Given I have a service set to :name
     */
    public function iHaveANameService($name)
    {
        $this->nameService->setName($name);
    }

    /**
     * @When make a request setting name to :name
     */
    public function makeARequestSettingNameTo($name)
    {
        $this->visit('set-name/'.$name);
    }

    /**
     * @Then the service value should have remained :name
     * @Then the service value should have changed to :name
     */
    public function theServiceValueShouldRemain($name)
    {
        Assert::assertEquals($name, $this->nameService->getName());
    }
}
