<?php

namespace Behat\Symfony2Extension\Context;

use Symfony\Bundle\FrameworkBundle\HttpKernel;

/*
 * This file is part of the Behat\Symfony2Extension.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Kernel support methods for Symfony2Extension.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
trait KernelDictionary
{
    private $kernel;

    /**
     * Sets HttpKernel instance.
     *
     * @param HttpKernel $kernel HttpKernel
     */
    public function setKernel(HttpKernel $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * Returns HttpKernel instance.
     *
     * @return HttpKernel
     */
    public function getKernel()
    {
        return $this->kernel;
    }

    /**
     * Returns HttpKernel service container.
     *
     * @return ServiceContainer
     */
    public function getContainer()
    {
        return $this->kernel->getContainer();
    }
}
