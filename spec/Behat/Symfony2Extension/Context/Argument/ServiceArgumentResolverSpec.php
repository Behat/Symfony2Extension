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
        $container->getParameter('parameter')->willReturn('param_value');

        $this->resolveArguments($reflectionClass, array('parameter' => '%parameter%'))->shouldReturn(
            array('parameter' => 'param_value')
        );
    }

    function it_resolves_parameters_inside_strings(
        ReflectionClass $reflectionClass,
        ContainerInterface $container
    ) {
        $container->getParameter('parameter')->willReturn('param_value');

        $this->resolveArguments($reflectionClass, array('parameter' => 'my_%parameter%_is_here'))->shouldReturn(
            array('parameter' => 'my_param_value_is_here')
        );
    }

    function it_can_handle_multiple_parameters(
        ReflectionClass $reflectionClass,
        ContainerInterface $container
    ) {
        $container->getParameter('parameter1')->willReturn('param_value_1');
        $container->getParameter('parameter2')->willReturn('param_value_2');

        $this->resolveArguments($reflectionClass, array('parameter' => 'first_%parameter1%_then_%parameter2%'))->shouldReturn(
            array('parameter' => 'first_param_value_1_then_param_value_2')
        );
    }

    function it_unescapes_string_arguments_with_escaped_percentages(ReflectionClass $reflectionClass)
    {
        $this->resolveArguments($reflectionClass, array('parameter' => 'percent%%percent'))->shouldReturn(
            array('parameter' => 'percent%percent')
        );
    }

    function it_does_not_match_arguments_that_are_escaped(
        ReflectionClass $reflectionClass,
        ContainerInterface $container
    ) {
        $container->getParameter('parameter')->willReturn('param_value');

        $this->resolveArguments($reflectionClass, array('parameter' => '%%parameter%%'))->shouldReturn(
            array('parameter' => '%parameter%')
        );
    }


    function it_resolves_arguments_starting_with_at_sign_if_they_point_to_existing_service(
        ReflectionClass $reflectionClass,
        ContainerInterface $container
    ) {
        $container->get('service')->willReturn($service = new stdClass());

        $this->resolveArguments($reflectionClass, array('service' => '@service'))->shouldReturn(
            array('service' => $service)
        );
    }

    function it_resolves_arguments_starting_with_at_sign_and_optional_dependency(
        ReflectionClass $reflectionClass,
        ContainerInterface $container
    ){
        $container->get('service')->willReturn($service = new stdClass());

        $this->resolveArguments($reflectionClass, array('service' => '@?service'))->shouldReturn(
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

    function it_does_not_escape_other_at_signs_in_arguments(ReflectionClass $reflectionClass)
    {
        $this->resolveArguments($reflectionClass, array('service' => 'service@@'))->shouldReturn(
            array('service' => 'service@@')
        );
    }

    function it_does_not_try_and_parse_arrays(ReflectionClass $reflectionClass)
    {
        $this->resolveArguments($reflectionClass, array('array' => array(1,2,3)))->shouldReturn(
            array('array' => array(1,2,3))
        );
    }
}
