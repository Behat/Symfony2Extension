<?php

namespace spec\Behat\Symfony2Extension\Context\Argument;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use ReflectionClass;
use stdClass;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class ServiceArgumentResolverSpec extends ObjectBehavior
{
    function let(KernelInterface $kernel, ContainerInterface $container)
    {
        $kernel->getContainer()->willReturn($container);

        $this->beConstructedWith($kernel);
    }

    function it_resolves_arguments_starting_from_at_sign_if_they_point_to_existing_service(
        ReflectionClass $reflectionClass,
        ContainerInterface $container
    ) {
        $container->has('service')->willReturn(true);
        $container->get('service')->willReturn($service = new stdClass());

        $this->resolveArguments($reflectionClass, array('service' => '@service'))->shouldReturn(
            array('service' => $service)
        );
    }

    function it_does_not_resolve_arguments_starting_from_at_sign_if_they_do_not_point_to_existing_service(
        ReflectionClass $reflectionClass,
        ContainerInterface $container
    ) {
        $container->has('service')->willReturn(false);
        $container->get(Argument::any())->shouldNotBeCalled();

        $this->resolveArguments($reflectionClass, array('service' => '@service'))->shouldReturn(
            array('service' => '@service')
        );
    }

    function it_does_not_resolve_arguments_not_starting_from_at_sign(
        ReflectionClass $reflectionClass,
        ContainerInterface $container
    ) {
        $container->get(Argument::any())->shouldNotBeCalled();

        $this->resolveArguments($reflectionClass, array('service' => 'my_service'))->shouldReturn(
            array('service' => 'my_service')
        );
    }
}
