<?php

namespace spec\Behat\Symfony2Extension\Suite;

use PhpSpec\ObjectBehavior;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class SymfonySuiteGeneratorSpec extends ObjectBehavior
{
    function let(KernelInterface $kernel)
    {
        $this->beConstructedWith($kernel);
    }

    function it_is_a_suite_generator()
    {
        $this->shouldHaveType('Behat\Testwork\Suite\Generator\SuiteGenerator');
    }

    function it_supports_symfony_bundle_suites_with_a_bundle_setting()
    {
        $this->supportsTypeAndSettings('symfony_bundle', array('bundle' => 'TestBundle'))->shouldBe(true);
    }

    function it_does_not_support_other_suite_types()
    {
        $this->supportsTypeAndSettings(null, array('bundle' => 'TestBundle'))->shouldBe(false);
    }

    function it_supports_symfony_bundle_suites_without_a_bundle_setting()
    {
        $this->supportsTypeAndSettings('symfony_bundle', array())->shouldBe(true);
    }

    function it_uses_suite_name_as_a_bundle_name_if_no_provided(KernelInterface $kernel, BundleInterface $bundle)
    {
        $kernel->getBundle('my_suite')->willReturn($bundle);

        $suite = $this->generateSuite('my_suite', array());

        $suite->shouldBeAnInstanceOf('Behat\Symfony2Extension\Suite\SymfonyBundleSuite');
        $suite->getBundle()->shouldReturn($bundle);
        $suite->getSetting('bundle')->shouldReturn('my_suite');
    }

    function it_fails_for_invalid_bundle_setting($kernel)
    {
        $kernel->getBundle('invalid')->willThrow('InvalidArgumentException');

        $this->shouldThrow('Behat\Testwork\Suite\Exception\SuiteConfigurationException')->duringGenerateSuite('my_suite', array('bundle' => 'invalid'));
    }

    function it_fails_for_invalid_bundle_taken_from_suite_name($kernel)
    {
        $kernel->getBundle('my_suite')->willThrow('InvalidArgumentException');

        $this->shouldThrow('Behat\Testwork\Suite\Exception\SuiteConfigurationException')->duringGenerateSuite('my_suite', array());
    }

    function it_generates_suites_with_conventional_settings(BundleInterface $bundle, $kernel)
    {
        $kernel->getBundle('test')->willReturn($bundle);
        $bundle->getNamespace()->willReturn('TestBundle');
        $bundle->getPath()->willReturn(__DIR__.'/TestBundle');

        $suite = $this->generateSuite('my_suite', array('bundle' => 'test'), array());

        $suite->shouldBeAnInstanceOf('Behat\Symfony2Extension\Suite\SymfonyBundleSuite');
        $suite->shouldHaveSetting('contexts');
        $suite->getSetting('contexts')->shouldReturn(array('TestBundle\Features\Context\FeatureContext'));
        $suite->shouldHaveSetting('paths');
        $suite->getSetting('paths')->shouldReturn(array(__DIR__.'/TestBundle/Features'));
        $suite->getBundle()->shouldBe($bundle);
        $suite->getSetting('bundle')->shouldReturn('test');
    }

    function it_does_not_overwrite_explicit_context(BundleInterface $bundle, $kernel)
    {
        $kernel->getBundle('test')->willReturn($bundle);
        $bundle->getPath()->willReturn(__DIR__.'/TestBundle');

        $suite = $this->generateSuite('my_suite', array('bundle' => 'test', 'contexts' => array('FeatureContext')));

        $suite->shouldBeAnInstanceOf('Behat\Symfony2Extension\Suite\SymfonyBundleSuite');
        $suite->shouldHaveSetting('contexts');
        $suite->getSetting('contexts')->shouldReturn(array('FeatureContext'));
        $suite->shouldHaveSetting('paths');
        $suite->getSetting('paths')->shouldReturn(array(__DIR__.'/TestBundle/Features'));
        $suite->getBundle()->shouldBe($bundle);
    }

    function it_does_not_overwrite_explicit_path(BundleInterface $bundle, $kernel)
    {
        $kernel->getBundle('test')->willReturn($bundle);
        $bundle->getNamespace()->willReturn('TestBundle');

        $suite = $this->generateSuite('my_suite', array('bundle' => 'test', 'paths' => array('features')));

        $suite->shouldBeAnInstanceOf('Behat\Symfony2Extension\Suite\SymfonyBundleSuite');
        $suite->shouldHaveSetting('contexts');
        $suite->getSetting('contexts')->shouldReturn(array('TestBundle\Features\Context\FeatureContext'));
        $suite->shouldHaveSetting('paths');
        $suite->getSetting('paths')->shouldReturn(array('features'));
        $suite->getBundle()->shouldBe($bundle);
    }
}
