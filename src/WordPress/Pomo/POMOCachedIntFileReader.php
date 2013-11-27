<?php

namespace WordPress\Pomo;

use WordPress\Pomo\POMOCachedFileReader;

/**
 * Reads the contents of the file in the beginning.
 */
class POMO_CachedIntFileReader extends POMOCachedFileReader {
    function POMO_CachedIntFileReader($filename) {
        parent::POMO_CachedFileReader($filename);
    }
}