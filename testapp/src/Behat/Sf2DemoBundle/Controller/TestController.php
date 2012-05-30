<?php

namespace Behat\Sf2DemoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TestController extends Controller
{
    public function indexAction(Request $request)
    {
        $type = $request->get('type') ?: 'stranger';

        return new Response(<<<RESPONSE
<html>
    <body>
        <h1>Hello, {$type}</h1>

        You are?:
        <a href="?type=human">Human</a>
        <a href="?type=orc">Orc</a>
        <a href="?type=elf">Elf</a>
    </body>
</html>
RESPONSE
        );
    }
}
