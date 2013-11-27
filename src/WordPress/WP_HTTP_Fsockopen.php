<?php

/**
 * Deprecated HTTP Transport method which used fsockopen.
 *
 * This class is not used, and is included for backwards compatibility only.
 * All code should make use of WP_HTTP directly through it's API.
 *
 * @see WP_HTTP::request
 *
 * @package WordPress
 * @subpackage HTTP
 *
 * @since 2.7.0
 * @deprecated 3.7.0 Please use WP_HTTP::request() directly
 */
class WP_HTTP_Fsockopen extends WP_HTTP_Streams {
    // For backwards compatibility for users who are using the class directly
}