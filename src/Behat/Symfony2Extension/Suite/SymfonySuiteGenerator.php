<?php

/*
 * This file is part of the Behat Symfony2Extension.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Behat\Symfony2Extension\Suite;

use Behat\Behat\Gherkin\Suite\GherkinSuite;
use Behat\Testwork\Suite\Generator\SuiteGenerator;
use Behat\Testwork\Suite\Suite;
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
        $this->pathSuffix = '/'.ltrim($pathSuffix, '/'.DIRECTORY_SEPARATOR);
        $this->contextClassSuffix = '\\'.ltrim($contextClassSuffix, '\\');
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
        return isset($settings['bundle']);
    }

    /**
     * Generate suite with provided name, settings and parameters.
     *
     * @param string $suiteName
     * @param array  $settings
     * @param array  $parameters
     *
     * @return Suite
     */
    public function generateSuite($suiteName, array $settings, array $parameters)
    {
        return new GherkinSuite($suiteName, $this->mergeDefaultSettings($settings), $parameters);
    }

    private function mergeDefaultSettings(array $settings)
    {
        if (!isset($settings['context']) && empty($settings['contexts'])) {
            $bundle = $this->kernel->getBundle($settings['bundle']);

            $settings['context'] = $bundle->getNamespace() . $this->contextClassSuffix;
        }

        if (!isset($settings['path']) && empty($settings['paths'])) {
            $bundle = $this->kernel->getBundle($settings['bundle']);

            $settings['path'] = $bundle->getPath() . $this->pathSuffix;
        }

        return $settings;
    }
}
