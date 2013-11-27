<?php

namespace WordPress\Pomo;

use WordPress\Pomo\POMO_CachedFileReader;

/**
 * Reads the contents of the file in the beginning.
 */
class POMO_CachedIntFileReader extends POMO_CachedFileReader {
    function POMO_CachedIntFileReader($filename) {
        parent::POMO_CachedFileReader($filename);
    }
}