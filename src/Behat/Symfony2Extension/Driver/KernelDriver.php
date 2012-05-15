<?php

namespace Behat\Symfony2Extension\Driver;

use Symfony\Component\HttpKernel\KernelInterface;

use Behat\Mink\Driver\HttpKernelDriver;

/*
 * This file is part of the Behat\Symfony2Extension
 *
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

/**
 * Kernel driver for Mink.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class KernelDriver extends HttpKernelDriver
{
    public function __construct(KernelInterface $kernel)
    {
        $kernel->boot();
        parent::__construct($kernel->getContainer()->get('test.client'));
    }
}
