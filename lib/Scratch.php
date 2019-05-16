<?php

/*
PHP which checks if .json is older than 1 min
if so downloads SEPA CSV and writes
reads CSV and converts to json
writes json
*/

class Scratch {
    const SEPA_CSV = 'SEPA_River_Levels_Web.csv';
    const DATADIR = 'DATADIR';
    const SEPA_DOWNLOAD_PERIOD = 60 * 10;

    private $variable;

    function setVariable($value) {
        $this->variable = $value;
    }
 
    public function __toString(): string {
        return $this->variable;
    }

    public function getVariable(): string {
        return $this->variable;
    }
}
/*
$grabSepa = new GrabSepa;
$grabSepa->setVariable('hello');
print "<p>" . $grabSepa->getVariable();
*/

/*
if (time()-filemtime(datadir + SEPA_CSV) > sepa_download_period) {
  // file older than 2 hours
  //grab file
  //check it's valid
  //parse to variable
  //write
} else {
  // read value
}
*/
