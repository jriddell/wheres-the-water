<?php

require_once('../lib/GrabSepa.php');

use PHPUnit\Framework\TestCase;

final class GrabSepaTest extends TestCase
{
    public function testDoGrab()
    {
        $grabSepa = new GrabSepa();
        $grabSepa->doGrab();
        $this->assertEquals(
            'data/SEPA_River_Levels_Web.csv',
            $grabSepa->sepaFile
        );
        unlink($grabSepa->sepaFile);
        $grabSepa2 = new GrabSepa();
        $grabSepa2->doGrab();
        $this->assertEquals(
            'data/SEPA_River_Levels_Web.csv',
            $grabSepa->sepaFile
        );
        
    }
}
