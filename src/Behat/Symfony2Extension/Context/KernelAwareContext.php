<?php

/*
 * This file is part of the Behat Symfony2Extension
 *
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Behat\Symfony2Extension\Context;

use Behat\Behat\Context\Context;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * HttpKernel aware interface for contexts.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
interface KernelAwareContext extends Context
{
    /**
     * Sets Kernel instance.
     *
     * @param KernelInterface $kernel
     */
    public function setKernel(KernelInterface $kernel);
}
