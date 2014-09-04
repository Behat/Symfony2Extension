<?php

namespace spec\Behat\Symfony2Extension\Context\Initializer;

use Behat\Behat\Context\Context;
use Behat\Symfony2Extension\Context\KernelAwareContext;
use PhpSpec\ObjectBehavior;
use Symfony\Component\HttpKernel\KernelInterface;

class KernelAwareInitializerSpec extends ObjectBehavior
{
    function let(KernelInterface $kernel)
    {
        $this->beConstructedWith($kernel);
    }

    function it_is_a_context_initializer()
    {
        $this->shouldHaveType('Behat\Behat\Context\Initializer\ContextInitializer');
    }

    function it_is_an_event_subscriber()
    {
        $this->shouldHaveType('Symfony\Component\EventDispatcher\EventSubscriberInterface');
    }

    function it_subscribes_to_events()
    {
        $this->getSubscribedEvents()->shouldHaveCount(2);
    }

    function it_does_nothing_for_non_kernel_aware_contexts(Context $context)
    {
        $this->initializeContext($context);
    }

    function it_injects_the_kernel_in_kernel_aware_contexts(KernelAwareContext $context, $kernel)
    {
        $context->setKernel($kernel)->shouldBeCalled();
        $this->initializeContext($context);
    }
}
