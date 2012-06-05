<?php

namespace Behat\Symfony2Extension;

use Symfony\Component\Config\FileLocator,
    Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition,
    Symfony\Component\DependencyInjection\ContainerBuilder,
    Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

use Behat\Behat\Extension\ExtensionInterface;

/*
 * This file is part of the Behat\Symfony2Extension
 *
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

/**
 * Symfony2 extension for Behat class.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class Extension implements ExtensionInterface
{
    /**
     * Loads a specific configuration.
     *
     * @param array            $config    Extension configuration hash (from behat.yml)
     * @param ContainerBuilder $container ContainerBuilder instance
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/services'));
        $loader->load('core.xml');

        // starting from Behat 2.4.1, we can check for activated extensions
        $extensions = $container->hasParameter('behat.extension.classes')
                    ? $container->getParameter('behat.extension.classes')
                    : array();

        if (isset($config['bundle'])) {
            $bundleName = preg_replace('/^\@/', '', $config['bundle']);
            $container->setParameter('behat.symfony2_extension.bundle', $bundleName);
        }
        if (isset($config['kernel'])) {
            foreach ($config['kernel'] as $key => $val) {
                $container->setParameter('behat.symfony2_extension.kernel.'.$key, $val);
            }
        }
        if (isset($config['context'])) {
            foreach ($config['context'] as $key => $val) {
                $container->setParameter('behat.symfony2_extension.context.'.$key, $val);
            }
        }

        if ($config['mink_driver']) {
            if (!class_exists('Behat\\Mink\\Driver\\BrowserKitDriver')) {
                throw new \RuntimeException(
                    'Install MinkBrowserKitDriver in order to activate symfony2 session.'
                );
            }

            $loader->load('mink_driver.xml');
        } elseif (in_array('Behat\\MinkExtension\\Extension', $extensions) && class_exists('Behat\\Mink\\Driver\\BrowserKitDriver')) {
            $loader->load('mink_driver.xml');
        }
    }

    /**
     * Setups configuration for current extension.
     *
     * @param ArrayNodeDefinition $builder
     */
    public function getConfig(ArrayNodeDefinition $builder)
    {
        $builder->
            children()->
                scalarNode('bundle')->
                    defaultNull()->
                end()->
                arrayNode('kernel')->
                    children()->
                        scalarNode('bootstrap')->
                            defaultValue('app/autoload.php')->
                        end()->
                        scalarNode('path')->
                            defaultValue('app/AppKernel.php')->
                        end()->
                        scalarNode('class')->
                            defaultValue('AppKernel')->
                        end()->
                        scalarNode('env')->
                            defaultValue('test')->
                        end()->
                        booleanNode('debug')->
                            defaultTrue()->
                        end()->
                    end()->
                end()->
                arrayNode('context')->
                    children()->
                        scalarNode('path_suffix')->
                            defaultValue('Features')->
                        end()->
                        scalarNode('class_suffix')->
                            defaultValue('Features\\Context\\FeatureContext')->
                        end()->
                    end()->
                end()->
                booleanNode('mink_driver')->defaultFalse()->end()->
            end()->
        end();
    }

    /**
     * Returns compiler passes used by mink extension.
     *
     * @return array
     */
    public function getCompilerPasses()
    {
        return array(
            new Compiler\KernelInitializationPass()
        );
    }
}
