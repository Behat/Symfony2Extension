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
use Behat\Testwork\Suite\GenericSuite;
use InvalidArgumentException;
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
        return 'symfony-bundle' === $type;
    }

    /**
     * {@inheritdoc}
     */
    public function generateSuite($suiteName, array $settings)
    {
        return new GenericSuite($suiteName, $this->mergeDefaultSettings($suiteName, $settings));
    }

    private function mergeDefaultSettings($suiteName, array $settings)
    {
        if (!isset($settings['bundle'])) {
            throw new SuiteConfigurationException('The "bundle" setting is mandatory for "symfony-bundle" suites', $suiteName);
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

        $settings['bundle_instance'] = $bundle;

        if (!isset($settings['context']) && empty($settings['contexts'])) {
            $settings['context'] = $bundle->getNamespace() . $this->contextClassSuffix;
        }

        if (!isset($settings['path']) && empty($settings['paths'])) {
            $settings['path'] = $bundle->getPath() . $this->pathSuffix;
        }

        return $settings;
    }
}
