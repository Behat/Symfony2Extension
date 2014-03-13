<?php

/*
 * This file is part of the Behat Symfony2Extension
 *
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Behat\Symfony2Extension\Specification;

use Behat\Symfony2Extension\Suite\SymfonyBundleSuite;
use Behat\Testwork\Specification\Locator\SpecificationLocator;
use Behat\Testwork\Specification\NoSpecificationsIterator;
use Behat\Testwork\Suite\Suite;

/**
 * @author Christophe Coevoet <stof@notk.org>
 */
final class BundleFeatureLocator implements SpecificationLocator
{
    /**
     * SpecificationLocator
     */
    private $baseLocator;

    /**
     * Initializes locator.
     *
     * @param SpecificationLocator $baseLocator
     */
    public function __construct(SpecificationLocator $baseLocator)
    {
        $this->baseLocator = $baseLocator;
    }

    /**
     * {@inheritdoc}
     */
    public function getLocatorExamples()
    {
        return array(
            "a Symfony2 bundle path <comment>(@BundleName/)</comment>"
        );
    }

    /**
     * {@inheritdoc}
     */
    public function locateSpecifications(Suite $suite, $locator)
    {
        if (!$suite instanceof SymfonyBundleSuite) {
            return new noSpecificationsIterator($suite);
        }

        $bundle = $suite->getBundle();

        if (0 !== strpos($locator, '@' . $bundle->getName())) {
            return new NoSpecificationsIterator($suite);
        }

        $locatorSuffix = substr($locator, strlen($bundle->getName()) + 1);

        return $this->baseLocator->locateSpecifications($suite, $bundle->getPath() . '/Features' . $locatorSuffix);
    }
}
