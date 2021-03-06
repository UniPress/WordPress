<?php

namespace WordPress\Atom;

/**
 * Structure that store common Atom Feed Properties
 *
 * @package AtomLib
 */
class Feed {
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
    /**
     * Stores Entries
     *
     * @var array
     * @access public
     */
    var $entries = array();
}