<?php

/*
 * This file is part of the Behat Symfony2Extension
 *
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Behat\Symfony2Extension\Driver;

use Behat\Mink\Driver\BrowserKitDriver;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Kernel driver for Mink.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class KernelDriver extends BrowserKitDriver
{
    public function __construct(KernelInterface $kernel, $baseUrl = null)
    {
        parent::__construct($kernel->getContainer()->get('test.client'), $baseUrl);
    }
}
