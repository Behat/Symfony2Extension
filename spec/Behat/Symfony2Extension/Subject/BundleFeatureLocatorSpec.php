<?php

namespace spec\Behat\Symfony2Extension\Subject;

use Behat\Gherkin\Gherkin;
use Behat\Symfony2Extension\Suite\SymfonyBundleSuite;
use Behat\Testwork\Suite\Suite;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

class BundleFeatureLocatorSpec extends ObjectBehavior
{
    function let(Gherkin $gherkin, SymfonyBundleSuite $suite, BundleInterface $bundle)
    {
        $this->beConstructedWith($gherkin, __DIR__);

        $suite->getBundle()->willReturn($bundle);
        $bundle->getName()->willReturn('AcmeDemoBundle');
        $bundle->getPath()->willReturn(__DIR__ . '/src/Acme/DemoBundle');
    }

    function it_is_a_subject_locator()
    {
        $this->shouldHaveType('Behat\Testwork\Subject\Locator\SubjectLocator');
    }

    function it_does_not_support_non_bundle_suite(Suite $nonBundleSuite)
    {
        $iterator = $this->locateSubjects($nonBundleSuite, '@MyBundle');
        $iterator->shouldBeAnInstanceOf('Behat\Testwork\Subject\EmptySubjectIterator');
        $iterator->getSuite()->shouldBe($nonBundleSuite);
    }

    function it_does_not_support_non_bundle_locator(SymfonyBundleSuite $suite)
    {
        $iterator = $this->locateSubjects($suite, 'src/Acme/DemoBundle/');
        $iterator->shouldBeAnInstanceOf('Behat\Testwork\Subject\EmptySubjectIterator');
        $iterator->getSuite()->shouldBe($suite);
    }

    function it_does_not_support_other_bundle_locator(SymfonyBundleSuite $suite)
    {
        $iterator = $this->locateSubjects($suite, '@MyBundle');
        $iterator->shouldBeAnInstanceOf('Behat\Testwork\Subject\EmptySubjectIterator');
        $iterator->getSuite()->shouldBe($suite);
    }
}
