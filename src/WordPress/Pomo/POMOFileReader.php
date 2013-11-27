<?php

namespace WordPress\Pomo;


class POMOFileReader extends POMOReader {
    function POMO_FileReader($filename) {
        parent::POMO_Reader();
        $this->_f = fopen($filename, 'rb');
    }

    function read($bytes) {
        return fread($this->_f, $bytes);
    }

    function seekto($pos) {
        if ( -1 == fseek($this->_f, $pos, SEEK_SET)) {
            return false;
        }
        $this->_pos = $pos;
        return true;
    }

    function is_resource() {
        return is_resource($this->_f);
    }

    function feof() {
        return feof($this->_f);
    }

    function close() {
        return fclose($this->_f);
    }

    function read_all() {
        $all = '';
        while ( !$this->feof() )
            $all .= $this->read(4096);
        return $all;
    }
}