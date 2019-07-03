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
use Symfony\Component\BrowserKit\CookieJar;
use Symfony\Component\BrowserKit\History;
use Symfony\Bundle\FrameworkBundle\Client;

/**
 * Kernel driver for Mink.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class KernelDriver extends BrowserKitDriver
{
    public function __construct(KernelInterface $kernel, $baseUrl = null)
    {
        $client = $this->getClientInstance($kernel);

        parent::__construct($client, $baseUrl);
    }

    private function getClientInstance(KernelInterface $kernel)
    {
        $clientName = 'test.client';
        if ($kernel->getContainer() && $kernel->getContainer()->has($clientName)) {
            return $kernel->getContainer()->get($clientName);
        }

        return new Client($kernel, array(), new History(), new CookieJar());
    }
}
