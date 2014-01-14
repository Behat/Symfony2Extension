<?php

namespace spec\Behat\Symfony2Extension;

use Behat\MinkExtension\Extension as MinkExtension;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ExtensionSpec extends ObjectBehavior
{
    function it_is_a_testwork_extension()
    {
        $this->shouldHaveType('Behat\Testwork\ServiceContainer\Extension');
    }

    function it_is_named_symfony2()
    {
        $this->getConfigKey()->shouldReturn('symfony2');
    }

    function it_registers_its_driver_factory_when_mink_is_available(ExtensionManager $extensionManager, MinkExtension $minkExtension)
    {
        $extensionManager->getExtension('mink')->willReturn($minkExtension);
        $minkExtension->registerDriverFactory(Argument::type('Behat\Symfony2Extension\ServiceContainer\Driver\SymfonyFactory'))->shouldBeCalled();

        $this->initialize($extensionManager);
    }

    function it_does_not_register_its_driver_factory_when_mink_is_not_available(ExtensionManager $extensionManager)
    {
        $extensionManager->getExtension('mink')->willReturn(null);

        $this->initialize($extensionManager);
    }
}
