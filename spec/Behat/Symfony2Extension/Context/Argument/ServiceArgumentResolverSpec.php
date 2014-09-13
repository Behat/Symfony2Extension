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

    function it_resolves_parameters_starting_and_ending_with_percentage_sign_if_they_point_to_parameter(
        ReflectionClass $reflectionClass,
        ContainerInterface $container
    ) {
        $container->hasParameter('parameter')->willReturn(true);
        $container->getParameter('parameter')->willReturn('param_value');

        $this->resolveArguments($reflectionClass, array('parameter' => '%parameter%'))->shouldReturn(
            array('parameter' => 'param_value')
        );
    }

    function it_resolves_arguments_starting_with_at_sign_if_they_point_to_existing_service(
        ReflectionClass $reflectionClass,
        ContainerInterface $container
    ) {
        $container->has('service')->willReturn(true);
        $container->get('service')->willReturn($service = new stdClass());

        $this->resolveArguments($reflectionClass, array('service' => '@service'))->shouldReturn(
            array('service' => $service)
        );
    }

    function it_does_not_resolve_plain_string_arguments(
        ReflectionClass $reflectionClass,
        ContainerInterface $container
    ) {
        $container->get(Argument::any())->shouldNotBeCalled();
        $container->getParameter(Argument::any())->shouldNotBeCalled();

        $this->resolveArguments($reflectionClass, array('service' => 'my_service'))->shouldReturn(
            array('service' => 'my_service')
        );
    }

    function it_unescapes_string_arguments_that_start_with_double_at_sign(ReflectionClass $reflectionClass)
    {
        $this->resolveArguments($reflectionClass, array('service' => '@@service'))->shouldReturn(
            array('service' => '@service')
        );
    }

    function it_unescapes_string_arguments_with_escaped_percentages(ReflectionClass $reflectionClass)
    {
        $this->resolveArguments($reflectionClass, array('parameter' => 'percent%%percent'))->shouldReturn(
            array('parameter' => 'percent%percent')
        );
    }
}
