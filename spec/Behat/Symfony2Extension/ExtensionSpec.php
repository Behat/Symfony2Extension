<?php

namespace spec\Behat\Symfony2Extension;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ExtensionSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Behat\Symfony2Extension\Extension');
    }

    function it_is_a_testwork_extension()
    {
        $this->shouldHaveType('Behat\Testwork\ServiceContainer\Extension');
    }

    function it_is_named_symfony2()
    {
        $this->getConfigKey()->shouldReturn('symfony2');
    }
}
