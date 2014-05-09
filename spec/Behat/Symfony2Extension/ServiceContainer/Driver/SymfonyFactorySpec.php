<?php

namespace spec\Behat\Symfony2Extension\ServiceContainer\Driver;

use Behat\Symfony2Extension\ServiceContainer\Symfony2Extension;
use PhpSpec\ObjectBehavior;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

class SymfonyFactorySpec extends ObjectBehavior
{
    function it_is_a_driver_factory()
    {
        $this->shouldHaveType('Behat\MinkExtension\ServiceContainer\Driver\DriverFactory');
    }

    function it_is_named_symfony2()
    {
        $this->getDriverName()->shouldReturn('symfony2');
    }

    function it_does_not_support_javascript()
    {
        $this->supportsJavascript()->shouldBe(false);
    }

    function it_does_not_have_any_specific_configuration(ArrayNodeDefinition $builder)
    {
        $this->configure($builder);
    }

    function it_creates_a_kernel_driver_definition()
    {
        $definition = $this->buildDriver(array());

        $definition->shouldBeAnInstanceOf('Symfony\Component\DependencyInjection\Definition');
        $definition->getClass()->shouldBe('Behat\Symfony2Extension\Driver\KernelDriver');
        $args = $definition->getArguments();
        $args[0]->shouldBeAnInstanceOf('Symfony\Component\DependencyInjection\Reference');
        $args[0]->__toString()->shouldBe(Symfony2Extension::KERNEL_ID);
    }
}
