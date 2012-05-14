<?php

namespace Behat\MinkExtension\Compiler;

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
 * Kernel loader compilation pass. Loads kernel file.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class KernelLoaderPass implements CompilerPassInterface
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

        $basePath   = $container->getParameter('behat.paths.base');
        $kernelPath = $container->getParameter('behat.symfony2_extension.kernel.path');
        if (file_exists($kernel = $basePath.DIRECTORY_SEPARATOR.$kernelPath)) {
            require($kernel);
        } else {
            require($kernelPath);
        }
    }
}
