<?php

/*
 * This file is part of the Behat Symfony2Extension.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Behat\Symfony2Extension\Suite;

use Behat\Testwork\Suite\Exception\SuiteConfigurationException;
use Behat\Testwork\Suite\Generator\SuiteGenerator;
use InvalidArgumentException;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * @author Christophe Coevoet <stof@notk.org>
 */
class SymfonySuiteGenerator implements SuiteGenerator
{
    private $kernel;
    private $pathSuffix;
    private $contextClassSuffix;

    public function __construct(KernelInterface $kernel, $pathSuffix = 'Features', $contextClassSuffix = 'Features\\Context\\FeatureContext')
    {
        $this->kernel = $kernel;
        $this->pathSuffix = '/' . ltrim($pathSuffix, '/' . DIRECTORY_SEPARATOR);
        $this->contextClassSuffix = '\\' . ltrim($contextClassSuffix, '\\');
    }

    /**
     * Checks if generator support provided suite type and settings.
     *
     * @param string $type
     * @param array  $settings
     *
     * @return Boolean
     */
    public function supportsTypeAndSettings($type, array $settings)
    {
        return 'symfony_bundle' === $type;
    }

    /**
     * {@inheritdoc}
     */
    public function generateSuite($suiteName, array $settings)
    {
        if (!isset($settings['bundle'])) {
            throw new SuiteConfigurationException('The "bundle" setting is mandatory for "symfony_bundle" suites', $suiteName);
        }

        try {
            $bundle = $this->kernel->getBundle($settings['bundle']);
        } catch (InvalidArgumentException $e) {
            throw new SuiteConfigurationException(
                sprintf('The bundle "%s" does not exist in the project', $settings['bundle']),
                $suiteName,
                $e
            );
        }

        return new SymfonyBundleSuite($suiteName, $bundle, $this->mergeDefaultSettings($bundle, $settings));
    }

    private function mergeDefaultSettings(BundleInterface $bundle, array $settings)
    {
        if (empty($settings['contexts'])) {
            $settings['contexts'] = array($bundle->getNamespace() . $this->contextClassSuffix);
        }

        if (empty($settings['paths'])) {
            $settings['paths'] = array($bundle->getPath() . $this->pathSuffix);
        }

        return $settings;
    }
}
