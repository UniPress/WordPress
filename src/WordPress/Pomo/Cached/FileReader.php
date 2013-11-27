<?php

namespace WordPress\Pomo\Cached;

/**
 * Reads the contents of the file in the beginning.
 */
class FileReader extends StringReader {
    function __construct($filename) {
        parent::StringReader();
        $this->_str = file_get_contents($filename);
        if (false === $this->_str)
            return false;
        $this->_pos = 0;
    }
}