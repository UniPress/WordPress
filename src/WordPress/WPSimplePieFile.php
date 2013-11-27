<?php

namespace WordPress;

class WPSimplePieFile extends \SimplePie_File {

    function __construct($url, $timeout = 10, $redirects = 5, $headers = null, $useragent = null, $force_fsockopen = false) {
        $this->url = $url;
        $this->timeout = $timeout;
        $this->redirects = $redirects;
        $this->headers = $headers;
        $this->useragent = $useragent;

        $this->method = SIMPLEPIE_FILE_SOURCE_REMOTE;

        if ( preg_match('/^http(s)?:\/\//i', $url) ) {
            $args = array(
                'timeout' => $this->timeout,
                'redirection' => $this->redirects,
            );

            if ( !empty($this->headers) )
                $args['headers'] = $this->headers;

            if ( SIMPLEPIE_USERAGENT != $this->useragent ) //Use default WP user agent unless custom has been specified
                $args['user-agent'] = $this->useragent;

            $res = wp_safe_remote_request($url, $args);

            if ( is_wp_error($res) ) {
                $this->error = 'WP HTTP Error: ' . $res->get_error_message();
                $this->success = false;
            } else {
                $this->headers = wp_remote_retrieve_headers( $res );
                $this->body = wp_remote_retrieve_body( $res );
                $this->status_code = wp_remote_retrieve_response_code( $res );
            }
        } else {
            $this->error = '';
            $this->success = false;
        }
    }
}