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

use Behat\Behat\Gherkin\Specification\Locator\FilesystemFeatureLocator;
use Behat\Symfony2Extension\Suite\SymfonyBundleSuite;
use Behat\Testwork\Specification\NoSpecificationsIterator;
use Behat\Testwork\Suite\Suite;

/**
 * @author Christophe Coevoet <stof@notk.org>
 */
class BundleFeatureLocator extends FilesystemFeatureLocator
{
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

        return parent::locateSpecifications($suite, $bundle->getPath() . '/Features' . $locatorSuffix);
    }
}
