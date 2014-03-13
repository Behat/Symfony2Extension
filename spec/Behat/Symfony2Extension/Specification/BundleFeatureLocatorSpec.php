<?php

namespace spec\Behat\Symfony2Extension\Specification;

use Behat\Gherkin\Gherkin;
use Behat\Symfony2Extension\Suite\SymfonyBundleSuite;
use Behat\Testwork\Specification\Locator\SpecificationLocator;
use Behat\Testwork\Specification\SpecificationIterator;
use Behat\Testwork\Suite\Suite;
use PhpSpec\ObjectBehavior;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

class BundleFeatureLocatorSpec extends ObjectBehavior
{
    function let(SpecificationLocator $baseLocator, SymfonyBundleSuite $suite, BundleInterface $bundle)
    {
        $this->beConstructedWith($baseLocator);

        $suite->getBundle()->willReturn($bundle);
        $bundle->getName()->willReturn('AcmeDemoBundle');
        $bundle->getPath()->willReturn('/src/Acme/DemoBundle');
    }

    function it_is_a_specification_locator()
    {
        $this->shouldHaveType('Behat\Testwork\Specification\Locator\SpecificationLocator');
    }

    function it_does_not_support_non_bundle_suite(Suite $nonBundleSuite)
    {
        $iterator = $this->locateSpecifications($nonBundleSuite, '@MyBundle');
        $iterator->shouldBeAnInstanceOf('Behat\Testwork\Specification\NoSpecificationsIterator');
        $iterator->getSuite()->shouldBe($nonBundleSuite);
    }

    function it_does_not_support_non_bundle_locator(SymfonyBundleSuite $suite)
    {
        $iterator = $this->locateSpecifications($suite, 'src/Acme/DemoBundle/');
        $iterator->shouldBeAnInstanceOf('Behat\Testwork\Specification\NoSpecificationsIterator');
        $iterator->getSuite()->shouldBe($suite);
    }

    function it_does_not_support_other_bundle_locator(SymfonyBundleSuite $suite)
    {
        $iterator = $this->locateSpecifications($suite, '@MyBundle');
        $iterator->shouldBeAnInstanceOf('Behat\Testwork\Specification\NoSpecificationsIterator');
        $iterator->getSuite()->shouldBe($suite);
    }

    function it_proxies_call_to_base_locator_if_bundle_locator_is_correct(SpecificationLocator $baseLocator, SymfonyBundleSuite $suite, SpecificationIterator $iterator)
    {
        $baseLocator->locateSpecifications($suite, '/src/Acme/DemoBundle/Features/my.feature')->willReturn($iterator);

        $this->locateSpecifications($suite, '@AcmeDemoBundle/my.feature')->shouldReturn($iterator);
    }
}
