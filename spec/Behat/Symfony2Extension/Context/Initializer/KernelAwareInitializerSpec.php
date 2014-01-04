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

    function it_is_initializable()
    {
        $this->shouldHaveType('Behat\Symfony2Extension\Context\Initializer\KernelAwareInitializer');
    }

    function it_is_a_context_initializer()
    {
        $this->shouldHaveType('Behat\Behat\Context\Initializer\ContextInitializer');
    }

    function it_is_an_event_subscriber()
    {
        $this->shouldHaveType('Symfony\Component\EventDispatcher\EventSubscriberInterface');
    }

    function it_supports_kernel_aware_contexts(KernelAwareContext $context)
    {
        $this->supportsContext($context)->shouldBe(true);
    }

    function it_does_not_support_basic_contexts(Context $context)
    {
        $this->supportsContext($context)->shouldBe(false);
    }

    function it_injects_the_kernel_in_kernel_aware_contexts(KernelAwareContext $context, $kernel)
    {
        $context->setKernel($kernel)->shouldBeCalled();
        $this->initializeContext($context);
    }
}
