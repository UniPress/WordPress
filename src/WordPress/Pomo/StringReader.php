<?php

namespace WordPress\Pomo;

use WordPress\Pomo\Reader;

/**
 * Provides file-like methods for manipulating a string instead
 * of a physical file.
 */
class StringReader extends Reader {

    var $_str = '';

    function POMO_StringReader($str = '') {
        parent::Reader();
        $this->_str = $str;
        $this->_pos = 0;
    }


    function read($bytes) {
        $data = $this->substr($this->_str, $this->_pos, $bytes);
        $this->_pos += $bytes;
        if ($this->strlen($this->_str) < $this->_pos) $this->_pos = $this->strlen($this->_str);
        return $data;
    }

    function seekto($pos) {
        $this->_pos = $pos;
        if ($this->strlen($this->_str) < $this->_pos) $this->_pos = $this->strlen($this->_str);
        return $this->_pos;
    }

    function length() {
        return $this->strlen($this->_str);
    }

    function read_all() {
        return $this->substr($this->_str, $this->_pos, $this->strlen($this->_str));
    }

}
