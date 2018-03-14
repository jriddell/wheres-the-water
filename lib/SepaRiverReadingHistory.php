<?php
/* Copyright 2018 Jonathan Riddell <jr@jriddell.org>
   May be copied under the GNU GPL version 3 (or later) only
*/

require_once 'config.php';

class SepaRiverReadingHistory {
    public $gauge_id;
    
    function __construct($gauge_id) {
        $this->dataDir = ROOT . '/' . self::DATADIR;
        $this->gauge_id = $gauge_id;
    }

}
