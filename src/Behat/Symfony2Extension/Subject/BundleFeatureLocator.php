<?php

/*
 * This file is part of the Behat Symfony2Extension
 *
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Behat\Symfony2Extension\Subject;

use Behat\Behat\Gherkin\Subject\Locator\FilesystemFeatureLocator;
use Behat\Symfony2Extension\Suite\SymfonyBundleSuite;
use Behat\Testwork\Subject\EmptySubjectIterator;
use Behat\Testwork\Suite\Suite;

/**
 * @author Christophe Coevoet <stof@notk.org>
 */
class BundleFeatureLocator extends FilesystemFeatureLocator
{
    public function locateSubjects(Suite $suite, $locator)
    {
        if (!$suite instanceof SymfonyBundleSuite) {
            return new EmptySubjectIterator($suite);
        }

        $bundle = $suite->getBundle();

        if (0 !== strpos($locator, '@' . $bundle->getName())) {
            return new EmptySubjectIterator($suite);
        }

        $locatorSuffix = substr($locator, strlen($bundle->getName()) + 1);

        return parent::locateSubjects($suite, $bundle->getPath() . '/Features' . $locatorSuffix);
    }
}
