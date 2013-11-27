<?php

namespace WordPress\Pomo;

use WordPress\Pomo\Cached\FileReader;

/**
 * Reads the contents of the file in the beginning.
 */
class CachedIntFileReader extends FileReader {
    function __construct($filename) {
        parent::CachedFileReader($filename);
    }
}