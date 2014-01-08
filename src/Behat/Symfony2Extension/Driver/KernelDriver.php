<?php

namespace Behat\Symfony2Extension\Driver;

use Symfony\Component\HttpKernel\KernelInterface;

use Behat\Mink\Driver\BrowserKitDriver;

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
class KernelDriver extends BrowserKitDriver
{
    public function __construct(KernelInterface $kernel, $baseUrl = null)
    {
        $client = $kernel->getContainer()->get('test.client');
        if ($baseUrl !== null) {
            $client->setServerParameter('SCRIPT_FILENAME', parse_url($baseUrl, PHP_URL_PATH));
        }

        parent::__construct($client);
    }
}
