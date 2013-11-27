<?php

namespace WordPress;

class WPFeedCacheTransient {
    var $name;
    var $mod_name;
    var $lifetime = 43200; //Default lifetime in cache of 12 hours

    function __construct($location, $filename, $extension) {
        $this->name = 'feed_' . $filename;
        $this->mod_name = 'feed_mod_' . $filename;

        $lifetime = $this->lifetime;
        /**
         * Filter the transient lifetime of the feed cache.
         *
         * @since 2.8.0
         *
         * @param int    $lifetime Cache duration in seconds. Default is 43200 seconds (12 hours).
         * @param string $filename Unique identifier for the cache object.
         */
        $this->lifetime = apply_filters( 'wp_feed_cache_transient_lifetime', $lifetime, $filename);
    }

    function save($data) {
        if ( is_a($data, 'SimplePie') )
            $data = $data->data;

        set_transient($this->name, $data, $this->lifetime);
        set_transient($this->mod_name, time(), $this->lifetime);
        return true;
    }

    function load() {
        return get_transient($this->name);
    }

    function mtime() {
        return get_transient($this->mod_name);
    }

    function touch() {
        return set_transient($this->mod_name, time(), $this->lifetime);
    }

    function unlink() {
        delete_transient($this->name);
        delete_transient($this->mod_name);
        return true;
    }
}