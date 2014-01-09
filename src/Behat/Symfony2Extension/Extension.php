<?php

/*
 * This file is part of the Behat Symfony2Extension
 *
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Behat\Symfony2Extension;

use Behat\Behat\Context\ServiceContainer\ContextExtension;
use Behat\MinkExtension\Extension as MinkExtension;
use Behat\Testwork\EventDispatcher\ServiceContainer\EventDispatcherExtension;
use Behat\Testwork\ServiceContainer\Extension as ExtensionInterface;
use Behat\Testwork\Suite\ServiceContainer\SuiteExtension;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Symfony2 extension for Behat class.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class Extension implements ExtensionInterface
{
    const KERNEL_ID = 'symfony2_extension.kernel';

    /**
     * {@inheritdoc}
     */
    public function load(ContainerBuilder $container, array $config)
    {
        $this->loadClassGenerator($container);
        $this->loadContextInitializer($container);
        $this->loadKernel($container, $config['kernel']);
        $this->loadSuiteGenerator($container, $config['context']);

        if ($config['mink_driver']) {
            $this->loadMinkDriver($container);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigKey()
    {
        return 'symfony2';
    }

    /**
     * Setups configuration for current extension.
     *
     * @param ArrayNodeDefinition $builder
     */
    public function configure(ArrayNodeDefinition $builder)
    {
        $boolFilter = function ($v) {
            $filtered = filter_var($v, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

            return (null === $filtered) ? $v : $filtered;
        };

        $builder
            ->addDefaultsIfNotSet()
            ->children()
                ->arrayNode('kernel')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('bootstrap')->defaultValue('app/autoload.php')->end()
                        ->scalarNode('path')->defaultValue('app/AppKernel.php')->end()
                        ->scalarNode('class')->defaultValue('AppKernel')->end()
                        ->scalarNode('env')->defaultValue('test')->end()
                        ->booleanNode('debug')
                            ->beforeNormalization()
                                ->ifString()->then($boolFilter)
                            ->end()
                            ->defaultTrue()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('context')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('path_suffix')
                            ->defaultValue('Features')
                        ->end()
                        ->scalarNode('class_suffix')
                            ->defaultValue('Features\Context\FeatureContext')
                        ->end()
                    ->end()
                ->end()
                ->booleanNode('mink_driver')
                    ->beforeNormalization()
                        ->ifString()->then($boolFilter)
                    ->end()
                    ->defaultValue(class_exists('Behat\Mink\Driver\BrowserKitDriver') && class_exists('Behat\MinkExtension\Extension'))
                ->end()
            ->end()
        ->end();
    }

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        // get base path
        $basePath = $container->getParameter('paths.base');

        // find and require bootstrap
        $bootstrapPath = $container->getParameter('symfony2_extension.kernel.bootstrap');
        if ($bootstrapPath) {
            if (file_exists($bootstrap = $basePath . '/' . $bootstrapPath)) {
                require_once($bootstrap);
            } elseif (file_exists($bootstrapPath)) {
                require_once($bootstrapPath);
            }
        }

        // find and require kernel
        $kernelPath = $container->getParameter('symfony2_extension.kernel.path');
        if (file_exists($kernel = $basePath . '/' . $kernelPath)) {
            $container->getDefinition(self::KERNEL_ID)->setFile($kernel);
        } elseif (file_exists($kernelPath)) {
            $container->getDefinition(self::KERNEL_ID)->setFile($kernelPath);
        }
    }

    private function loadClassGenerator(ContainerBuilder $container)
    {
        $definition = new Definition('Behat\Symfony2Extension\Context\ContextClass\KernelAwareClassGenerator');
        $definition->addTag(ContextExtension::CLASS_GENERATOR_TAG, array('priority' => 100));
        $container->setDefinition('symfony2_extension.class_generator.kernel_aware', $definition);
    }

    private function loadContextInitializer(ContainerBuilder $container)
    {
        $definition = new Definition('Behat\Symfony2Extension\Context\Initializer\KernelAwareInitializer', array(
            new Reference(self::KERNEL_ID),
        ));
        $definition->addTag(ContextExtension::INITIALIZER_TAG, array('priority' => 0));
        $definition->addTag(EventDispatcherExtension::SUBSCRIBER_TAG, array('priority' => 0));
        $container->setDefinition('symfony2_extension.context_initializer.kernel_aware', $definition);
    }

    private function loadKernel(ContainerBuilder $container, array $config)
    {
        $definition = new Definition($config['class'], array(
            $config['env'],
            $config['debug'],
        ));
        $definition->addMethodCall('boot');
        $container->setDefinition(self::KERNEL_ID, $definition);
        $container->setParameter(self::KERNEL_ID . '.path', $config['path']);
        $container->setParameter(self::KERNEL_ID . '.bootstrap', $config['bootstrap']);
    }

    private function loadMinkDriver(ContainerBuilder $container)
    {
        if (!class_exists('Behat\Mink\Driver\BrowserKitDriver')) {
            throw new \RuntimeException(
                'Install MinkBrowserKitDriver in order to activate the symfony2 session.'
            );
        }

        if (!class_exists('Behat\MinkExtension\Extension')) {
            throw new \RuntimeException(
                'Install MinkExtension in order to activate the symfony2 session.'
            );
        }

        $definition = new Definition('Behat\Mink\Session', array(
            new Definition('Behat\Symfony2Extension\Driver\KernelDriver', array(
                new Reference(self::KERNEL_ID)
            )),
            new Reference(MinkExtension::SELECTORS_HANDLER_ID),
        ));
        $definition->addTag(MinkExtension::SESSION_TAG, array('alias' => 'symfony2'));
        $container->setDefinition('symfony_extension.mink_session.symfony2', $definition);
    }

    private function loadSuiteGenerator(ContainerBuilder $container, array $config)
    {
        $definition = new Definition('Behat\Symfony2Extension\Suite\SymfonySuiteGenerator', array(
            new Reference(self::KERNEL_ID),
            $config['path_suffix'],
            $config['class_suffix'],
        ));
        $definition->addTag(SuiteExtension::GENERATOR_TAG, array('priority' => 100));
        $container->setDefinition('symfony2_extension.suite.generator', $definition);
    }
}
