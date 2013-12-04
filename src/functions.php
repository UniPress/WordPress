<?php

include __DIR__.'/class_alias.php';

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
    if ( is_object($thing) && is_a($thing, '\WordPress\WPError') )
        return true;
    return false;
}

/**
 * Returns value of command line params.
 * Exits when a required param is not set.
 *
 * @param string $param
 * @param bool $required
 * @return mixed
 */
function get_cli_args( $param, $required = false ) {
    $args = $_SERVER['argv'];

    $out = array();

    $last_arg = null;
    $return = null;

    $il = sizeof( $args );

    for ( $i = 1, $il; $i < $il; $i++ ) {
        if ( (bool) preg_match( "/^--(.+)/", $args[$i], $match ) ) {
            $parts = explode( "=", $match[1] );
            $key = preg_replace( "/[^a-z0-9]+/", "", $parts[0] );

            if ( isset( $parts[1] ) ) {
                $out[$key] = $parts[1];
            } else {
                $out[$key] = true;
            }

            $last_arg = $key;
        } else if ( (bool) preg_match( "/^-([a-zA-Z0-9]+)/", $args[$i], $match ) ) {
            for ( $j = 0, $jl = strlen( $match[1] ); $j < $jl; $j++ ) {
                $key = $match[1]{$j};
                $out[$key] = true;
            }

            $last_arg = $key;
        } else if ( $last_arg !== null ) {
            $out[$last_arg] = $args[$i];
        }
    }

    // Check array for specified param
    if ( isset( $out[$param] ) ) {
        // Set return value
        $return = $out[$param];
    }

    // Check for missing required param
    if ( !isset( $out[$param] ) && $required ) {
        // Display message and exit
        echo "\"$param\" parameter is required but was not specified\n";
        exit();
    }

    return $return;
}