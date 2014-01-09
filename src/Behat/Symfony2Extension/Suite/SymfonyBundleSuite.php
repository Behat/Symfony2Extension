<?php

/*
 * This file is part of the Behat Symfony2Extension.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Behat\Symfony2Extension\Suite;

use Behat\Testwork\Suite\GenericSuite;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

/**
 * @author Christophe Coevoet <stof@notk.org>
 */
class SymfonyBundleSuite extends GenericSuite
{
    /**
     * @var BundleInterface
     */
    private $bundle;

    public function __construct($name, BundleInterface $bundle, array $settings)
    {
        parent::__construct($name, $settings);
        $this->bundle = $bundle;
    }

    /**
     * @return BundleInterface
     */
    public function getBundle()
    {
        return $this->bundle;
    }
}
