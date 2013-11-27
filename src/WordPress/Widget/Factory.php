<?php

namespace WordPress\Widget;

/**
* Singleton that registers and instantiates WP_Widget classes.
 *
 * @package WordPress
* @subpackage Widgets
* @since 2.8
*/
class Factory {
    var $widgets = array();

    function WP_Widget_Factory() {
        add_action( 'widgets_init', array( $this, '_register_widgets' ), 100 );
    }

    function register($widget_class) {
        $this->widgets[$widget_class] = new $widget_class();
    }

    function unregister($widget_class) {
        if ( isset($this->widgets[$widget_class]) )
            unset($this->widgets[$widget_class]);
    }

    function _register_widgets() {
        global $wp_registered_widgets;
        $keys = array_keys($this->widgets);
        $registered = array_keys($wp_registered_widgets);
        $registered = array_map('_get_widget_id_base', $registered);

        foreach ( $keys as $key ) {
            // don't register new widget if old widget with the same id is already registered
            if ( in_array($this->widgets[$key]->id_base, $registered, true) ) {
                unset($this->widgets[$key]);
                continue;
            }

            $this->widgets[$key]->_register();
        }
    }
}