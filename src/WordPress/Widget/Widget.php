<?php
namespace WordPress\Widget;

/**
 * This class must be extended for each widget and Widget::widget(), Widget::update()
 * and Widget::form() need to be over-ridden.
 *
 * @package WordPress
 * @subpackage Widgets
 * @since 2.8
 */
class Widget
{

    var $id_base; // Root id for all widgets of this type.
    var $name; // Name for this widget type.
    var $widget_options; // Option array passed to wp_register_sidebar_widget()
    var $control_options; // Option array passed to wp_register_widget_control()

    var $number = false; // Unique ID number of the current instance.
    var $id = false; // Unique ID string of the current instance (id_base-number)
    var $updated = false; // Set true when we update the data after a POST submit - makes sure we don't do it twice.

    // Member functions that you must over-ride.

    /** Echo the widget content.
     *
     * Subclasses should over-ride this function to generate their widget code.
     *
     * @param array $args Display arguments including before_title, after_title, before_widget, and after_widget.
     * @param array $instance The settings for the particular instance of the widget
     */
    function widget($args, $instance)
    {
        die('function \WordPress\Widget\Widget::widget() must be over-ridden in a sub-class.');
    }

    /** Update a particular instance.
     *
     * This function should check that $new_instance is set correctly.
     * The newly calculated value of $instance should be returned.
     * If "false" is returned, the instance won't be saved/updated.
     *
     * @param array $new_instance New settings for this instance as input by the user via form()
     * @param array $old_instance Old settings for this instance
     * @return array Settings to save or bool false to cancel saving
     */
    function update($new_instance, $old_instance)
    {
        return $new_instance;
    }

    /** Echo the settings update form
     *
     * @param array $instance Current settings
     */
    function form($instance)
    {
        echo '<p class="no-options-widget">' . __('There are no options for this widget.') . '</p>';
        return 'noform';
    }

    // Functions you'll need to call.

    /**
     * PHP5 constructor
     *
     * @param string $id_base Optional Base ID for the widget, lower case,
     * if left empty a portion of the widget's class name will be used. Has to be unique.
     * @param string $name Name for the widget displayed on the configuration page.
     * @param array $widget_options Optional Passed to wp_register_sidebar_widget()
     *     - description: shown on the configuration page
     *     - classname
     * @param array $control_options Optional Passed to wp_register_widget_control()
     *     - width: required if more than 250px
     *     - height: currently not used but may be needed in the future
     */
    function __construct($id_base, $name, $widget_options = array(), $control_options = array())
    {
        $this->id_base = empty($id_base) ? preg_replace(
            '/(wp_)?widget_/',
            '',
            strtolower(get_class($this))
        ) : strtolower($id_base);
        $this->name = $name;
        $this->option_name = 'widget_' . $this->id_base;
        $this->widget_options = wp_parse_args($widget_options, array('classname' => $this->option_name));
        $this->control_options = wp_parse_args($control_options, array('id_base' => $this->id_base));
    }

    /**
     * Constructs name attributes for use in form() fields
     *
     * This function should be used in form() methods to create name attributes for fields to be saved by update()
     *
     * @param string $field_name Field name
     * @return string Name attribute for $field_name
     */
    function get_field_name($field_name)
    {
        return 'widget-' . $this->id_base . '[' . $this->number . '][' . $field_name . ']';
    }

    /**
     * Constructs id attributes for use in form() fields
     *
     * This function should be used in form() methods to create id attributes for fields to be saved by update()
     *
     * @param string $field_name Field name
     * @return string ID attribute for $field_name
     */
    function get_field_id($field_name)
    {
        return 'widget-' . $this->id_base . '-' . $this->number . '-' . $field_name;
    }

    // Private Functions. Don't worry about these.

    function _register()
    {
        $settings = $this->get_settings();
        $empty = true;

        if (is_array($settings)) {
            foreach (array_keys($settings) as $number) {
                if (is_numeric($number)) {
                    $this->_set($number);
                    $this->_register_one($number);
                    $empty = false;
                }
            }
        }

        if ($empty) {
            // If there are none, we register the widget's existence with a
            // generic template
            $this->_set(1);
            $this->_register_one();
        }
    }

    function _set($number)
    {
        $this->number = $number;
        $this->id = $this->id_base . '-' . $number;
    }

    function _get_display_callback()
    {
        return array($this, 'display_callback');
    }

    function _get_update_callback()
    {
        return array($this, 'update_callback');
    }

    function _get_form_callback()
    {
        return array($this, 'form_callback');
    }

    /** Generate the actual widget content.
     *    Just finds the instance and calls widget().
     *    Do NOT over-ride this function. */
    function display_callback($args, $widget_args = 1)
    {
        if (is_numeric($widget_args)) {
            $widget_args = array('number' => $widget_args);
        }

        $widget_args = wp_parse_args($widget_args, array('number' => -1));
        $this->_set($widget_args['number']);
        $instance = $this->get_settings();

        if (array_key_exists($this->number, $instance)) {
            $instance = $instance[$this->number];
            // filters the widget's settings, return false to stop displaying the widget
            $instance = apply_filters('widget_display_callback', $instance, $this, $args);
            if (false !== $instance) {
                $this->widget($args, $instance);
            }
        }
    }

    /** Deal with changed settings.
     *    Do NOT over-ride this function. */
    function update_callback($widget_args = 1)
    {
        global $wp_registered_widgets;

        if (is_numeric($widget_args)) {
            $widget_args = array('number' => $widget_args);
        }

        $widget_args = wp_parse_args($widget_args, array('number' => -1));
        $all_instances = $this->get_settings();

        // We need to update the data
        if ($this->updated) {
            return;
        }

        $sidebars_widgets = wp_get_sidebars_widgets();

        if (isset($_POST['delete_widget']) && $_POST['delete_widget']) {
            // Delete the settings for this instance of the widget
            if (isset($_POST['the-widget-id'])) {
                $del_id = $_POST['the-widget-id'];
            } else {
                return;
            }

            if (isset($wp_registered_widgets[$del_id]['params'][0]['number'])) {
                $number = $wp_registered_widgets[$del_id]['params'][0]['number'];

                if ($this->id_base . '-' . $number == $del_id) {
                    unset($all_instances[$number]);
                }
            }
        } else {
            if (isset($_POST['widget-' . $this->id_base]) && is_array($_POST['widget-' . $this->id_base])) {
                $settings = $_POST['widget-' . $this->id_base];
            } elseif (isset($_POST['id_base']) && $_POST['id_base'] == $this->id_base) {
                $num = $_POST['multi_number'] ? (int)$_POST['multi_number'] : (int)$_POST['widget_number'];
                $settings = array($num => array());
            } else {
                return;
            }

            foreach ($settings as $number => $new_instance) {
                $new_instance = stripslashes_deep($new_instance);
                $this->_set($number);

                $old_instance = isset($all_instances[$number]) ? $all_instances[$number] : array();

                $instance = $this->update($new_instance, $old_instance);

                // filters the widget's settings before saving, return false to cancel saving (keep the old settings if updating)
                $instance = apply_filters('widget_update_callback', $instance, $new_instance, $old_instance, $this);
                if (false !== $instance) {
                    $all_instances[$number] = $instance;
                }

                break; // run only once
            }
        }

        $this->save_settings($all_instances);
        $this->updated = true;
    }

    /** Generate the control form.
     *    Do NOT over-ride this function. */
    function form_callback($widget_args = 1)
    {
        if (is_numeric($widget_args)) {
            $widget_args = array('number' => $widget_args);
        }

        $widget_args = wp_parse_args($widget_args, array('number' => -1));
        $all_instances = $this->get_settings();

        if (-1 == $widget_args['number']) {
            // We echo out a form where 'number' can be set later
            $this->_set('__i__');
            $instance = array();
        } else {
            $this->_set($widget_args['number']);
            $instance = $all_instances[$widget_args['number']];
        }

        // filters the widget admin form before displaying, return false to stop displaying it
        $instance = apply_filters('widget_form_callback', $instance, $this);

        $return = null;
        if (false !== $instance) {
            $return = $this->form($instance);
            // add extra fields in the widget form - be sure to set $return to null if you add any
            // if the widget has no form the text echoed from the default form method can be hidden using css
            do_action_ref_array('in_widget_form', array(&$this, &$return, $instance));
        }
        return $return;
    }

    /** Helper function: Registers a single instance. */
    function _register_one($number = -1)
    {
        wp_register_sidebar_widget(
            $this->id,
            $this->name,
            $this->_get_display_callback(),
            $this->widget_options,
            array('number' => $number)
        );
        _register_widget_update_callback(
            $this->id_base,
            $this->_get_update_callback(),
            $this->control_options,
            array('number' => -1)
        );
        _register_widget_form_callback(
            $this->id,
            $this->name,
            $this->_get_form_callback(),
            $this->control_options,
            array('number' => $number)
        );
    }

    function save_settings($settings)
    {
        $settings['_multiwidget'] = 1;
        update_option($this->option_name, $settings);
    }

    function get_settings()
    {
        $settings = get_option($this->option_name);

        if (false === $settings && isset($this->alt_option_name)) {
            $settings = get_option($this->alt_option_name);
        }

        if (!is_array($settings)) {
            $settings = array();
        }

        if (!empty($settings) && !array_key_exists('_multiwidget', $settings)) {
            // old format, convert if single widget
            $settings = wp_convert_widget_settings($this->id_base, $this->option_name, $settings);
        }

        unset($settings['_multiwidget'], $settings['__i__']);
        return $settings;
    }
}