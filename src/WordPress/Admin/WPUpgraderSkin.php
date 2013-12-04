<?php

namespace WordPress\Admin;

/**
 * Generic Skin for the WordPress Upgrader classes. This skin is designed to be extended for specific purposes.
 *
 * @package WordPress
 * @subpackage Upgrader
 * @since 2.8.0
 */
class WPUpgraderSkin
{

    var $upgrader;
    var $done_header = false;
    var $result = false;

    function __construct($args = array())
    {
        $defaults = array('url' => '', 'nonce' => '', 'title' => '', 'context' => false);
        $this->options = wp_parse_args($args, $defaults);
    }

    function set_upgrader(&$upgrader)
    {
        if (is_object($upgrader)) {
            $this->upgrader =& $upgrader;
        }
        $this->add_strings();
    }

    function add_strings()
    {
    }

    function set_result($result)
    {
        $this->result = $result;
    }

    function request_filesystem_credentials($error = false)
    {
        $url = $this->options['url'];
        $context = $this->options['context'];
        if (!empty($this->options['nonce'])) {
            $url = wp_nonce_url($url, $this->options['nonce']);
        }
        return request_filesystem_credentials(
            $url,
            '',
            $error,
            $context
        ); //Possible to bring inline, Leaving as is for now.
    }

    function header()
    {
        if ($this->done_header) {
            return;
        }
        $this->done_header = true;
        echo '<div class="wrap">';
        screen_icon();
        echo '<h2>' . $this->options['title'] . '</h2>';
    }

    function footer()
    {
        echo '</div>';
    }

    function error($errors)
    {
        if (!$this->done_header) {
            $this->header();
        }
        if (is_string($errors)) {
            $this->feedback($errors);
        } elseif (is_wp_error($errors) && $errors->get_error_code()) {
            foreach ($errors->get_error_messages() as $message) {
                if ($errors->get_error_data() && is_string($errors->get_error_data())) {
                    $this->feedback($message . ' ' . esc_html($errors->get_error_data()));
                } else {
                    $this->feedback($message);
                }
            }
        }
    }

    function feedback($string)
    {
        if (isset($this->upgrader->strings[$string])) {
            $string = $this->upgrader->strings[$string];
        }

        if (strpos($string, '%') !== false) {
            $args = func_get_args();
            $args = array_splice($args, 1);
            if ($args) {
                $args = array_map('strip_tags', $args);
                $args = array_map('esc_html', $args);
                $string = vsprintf($string, $args);
            }
        }
        if (empty($string)) {
            return;
        }
        show_message($string);
    }

    function before()
    {
    }

    function after()
    {
    }

}