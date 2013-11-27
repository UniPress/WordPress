<?php

//include __DIR__.'/../wp-includes/formatting.php';
//include __DIR__.'/../wp-includes/taxonomy.php';

/**
 * Check whether variable is a WordPress Error.
 *
 * Returns true if $thing is an object of the WPError class.
 *
 * @since 2.1.0
 *
 * @param mixed $thing Check if unknown variable is a WPError object.
 * @return bool True, if WPError. False, if not WPError.
 */
function is_wp_error($thing) {
    if ( is_object($thing) && is_a($thing, 'WPError') )
        return true;
    return false;
}

\WordPress\WPEmbed::amenophisLoad();
