<?php

require_once('../lib/GrabSepa.php');

use PHPUnit\Framework\TestCase;

final class GrabSepaTest extends TestCase
{
    public function testVariable()
    {
        $grabSepa = new GrabSepa();
        $grabSepa->set_variable('hello');
        $this->assertEquals(
            'hello',
            $grabSepa->getVariable()
        );
    }
}
