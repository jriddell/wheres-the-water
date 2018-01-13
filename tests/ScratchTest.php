<?php

require_once('../lib/Scratch.php');

use PHPUnit\Framework\TestCase;

final class ScratchTest extends TestCase
{
    public function testVariable() {
        $scratch = new Scratch();
        $scratch->setVariable('hello');
        $this->assertEquals(
            'hello',
            $scratch->getVariable()
        );
    }

    public function testConst() {
        $scratch = new Scratch();
        $this->assertEquals(
            $scratch::SEPA_CSV,
            'SEPA_River_Levels_Web.csv'
        );
    }
}
