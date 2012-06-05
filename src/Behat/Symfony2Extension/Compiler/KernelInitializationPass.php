<?php

namespace Behat\Symfony2Extension\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder,
    Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/*
 * This file is part of the Behat\MinkExtension
 *
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

/**
 * Kernel initialization pass.
 * Loads kernel file and initializes kernel.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class KernelInitializationPass implements CompilerPassInterface
{
    /**
     * Loads kernel file.
     *
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasParameter('behat.symfony2_extension.kernel.path')) {
            return;
        }
        // get base path
        $basePath = $container->getParameter('behat.paths.base');

        // find and require bootstrap
        $bootstrapPath = $container->getParameter('behat.symfony2_extension.kernel.bootstrap');
        if ($bootstrapPath) {
            if (file_exists($bootstrap = $basePath.DIRECTORY_SEPARATOR.$bootstrapPath)) {
                require_once($bootstrap);
            } elseif (file_exists($bootstrapPath)) {
                require_once($bootstrapPath);
            }
        }

        // find and require kernel
        $kernelPath = $container->getParameter('behat.symfony2_extension.kernel.path');
        if (file_exists($kernel = $basePath.DIRECTORY_SEPARATOR.$kernelPath)) {
            require_once($kernel);
        } elseif (file_exists($kernelPath)) {
            require_once($kernelPath);
        }

        // boot kernel
        $kernel = $container->get('behat.symfony2_extension.kernel');
        $kernel->boot();

        // if bundle name specified - direct behat.paths.features to it
        if ($bundleName = $container->getParameter('behat.symfony2_extension.bundle')) {
            $bundle = $kernel->getBundle($bundleName);
            $container->setParameter(
                'behat.paths.features',
                $bundle->getPath().DIRECTORY_SEPARATOR.
                    $container->getParameter('behat.symfony2_extension.context.path_suffix')
            );
        }
    }
}
