<?php

/*
 * This file is part of the Behat
 *
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

require __DIR__.'/src/Behat/Symfony2Extension/Compiler/KernelLoaderPass.php';
require __DIR__.'/src/Behat/Symfony2Extension/Console/Processor/InitProcessor.php';
require __DIR__.'/src/Behat/Symfony2Extension/Console/Processor/LocatorProcessor.php';
require __DIR__.'/src/Behat/Symfony2Extension/Context/ClassGuesser/BundleContextClassGuesser.php';
require __DIR__.'/src/Behat/Symfony2Extension/Context/Initializer/KernelAwareInitializer.php';
require __DIR__.'/src/Behat/Symfony2Extension/Context/KernelAwareInterface.php';
require __DIR__.'/src/Behat/Symfony2Extension/Context/KernelDictionary.php';
require __DIR__.'/src/Behat/Symfony2Extension/Driver/HttpKernelDriver.php';
require __DIR__.'/src/Behat/Symfony2Extension/Driver/Extension.php';

return new Behat\Symfony2Extension\Extension;
