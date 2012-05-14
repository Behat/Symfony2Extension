<?php

namespace Behat\Symfony2Extension\Context\ClassGuesser;

use Behat\Behat\Context\ClassGuesser\ClassGuesserInterface;

/*
 * This file is part of the Behat\Symfony2Extension.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Bundle context class guesser.
 * Provides Bundle context class if found.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class BundleContextClassGuesser implements ClassGuesserInterface
{
    private $classSuffix;
    private $namespace;

    /**
     * Initializes guesser.
     *
     * @param string $classSuffix
     */
    public function __construct($classSuffix = 'Features\\Context\\FeatureContext')
    {
        $this->classSuffix = $classSuffix;
    }

    /**
     * Sets bundle namespace to use for guessing.
     *
     * @param string $namespace
     */
    public function setBundleNamespace($namespace)
    {
        $this->namespace = $namespace;
    }

    /**
     * Tries to guess context classname.
     *
     * @return string
     */
    public function guess()
    {
        if (class_exists($class = $this->namespace.'\\'.$this->classSuffix)) {
            return $class;
        }
    }
}
