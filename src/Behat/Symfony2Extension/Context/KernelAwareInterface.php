<?php

namespace Behat\Symfony2Extension\Context;

use Symfony\Component\HttpKernel\KernelInterface;

/*
 * This file is part of the Behat\Symfony2Extension.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * HttpKernel aware interface for contexts.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
interface KernelAwareInterface
{
    /**
     * Sets Kernel instance.
     *
     * @param KernelInterface $kernel HttpKernel instance
     */
    public function setKernel(KernelInterface $kernel);
}
