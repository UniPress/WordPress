<?php

namespace WordPress\Atom;

/**
 * Structure that store common Atom Feed Properties
 *
 * @package AtomLib
 */

/**
 * Structure that store Atom Entry Properties
 *
 * @package AtomLib
 */
class Entry {
    /**
     * Stores Links
     * @var array
     * @access public
     */
    var $links = array();
    /**
     * Stores Categories
     * @var array
     * @access public
     */
    var $categories = array();
}