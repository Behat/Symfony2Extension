<?php

/*
 * This file is part of the Behat Symfony2Extension.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Behat\Symfony2Extension\Suite;

use Behat\Testwork\Suite\Suite;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Behat\Testwork\Suite\Exception\ParameterNotFoundException;

/**
 * @author Christophe Coevoet <stof@notk.org>
 */
class SymfonyBundleSuite implements Suite
{
    /**
     * @var string
     */
    private $name;
    /**
     * @var BundleInterface
     */
    private $bundle;
    /**
     * @var array
     */
    private $settings = array();

    /**
     * Initiailzes suite.
     *
     * @param string          $name
     * @param BundleInterface $bundle
     * @param array           $settings
     */
    public function __construct($name, BundleInterface $bundle, array $settings)
    {
        $this->name = $name;
        $this->bundle = $bundle;
        $this->settings = $settings;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns suite bundle.
     *
     * @return BundleInterface
     */
    public function getBundle()
    {
        return $this->bundle;
    }

    /**
     * {@inheritdoc}
     */
    public function getSettings()
    {
        return $this->settings;
    }

    /**
     * {@inheritdoc}
     */
    public function hasSetting($key)
    {
        return isset($this->settings[$key]);
    }

    /**
     * {@inheritdoc}
     *
     * @throws ParameterNotFoundException If setting is not set
     */
    public function getSetting($key)
    {
        if (!$this->hasSetting($key)) {
            throw new ParameterNotFoundException(sprintf(
                '`%s` suite does not have a `%s` setting.',
                $this->getName(),
                $key
            ), $this->getName(), $key);
        }

        return $this->settings[$key];
    }
}
