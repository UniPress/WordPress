<?php

namespace WordPress;

/**
 * Customize Control Class
 *
 * @package WordPress
 * @subpackage Customize
 * @since 3.4.0
 */
class WP_Customize_Control {
    /**
     * @access public
     * @var WP_Customize_Manager
     */
    public $manager;

    /**
     * @access public
     * @var string
     */
    public $id;

    /**
     * All settings tied to the control.
     *
     * @access public
     * @var array
     */
    public $settings;

    /**
     * The primary setting for the control (if there is one).
     *
     * @access public
     * @var string
     */
    public $setting = 'default';

    /**
     * @access public
     * @var int
     */
    public $priority          = 10;

    /**
     * @access public
     * @var string
     */
    public $section           = '';

    /**
     * @access public
     * @var string
     */
    public $label             = '';

    /**
     * @todo: Remove choices
     *
     * @access public
     * @var array
     */
    public $choices           = array();

    /**
     * @access public
     * @var array
     */
    public $json = array();

    /**
     * @access public
     * @var string
     */
    public $type = 'text';


    /**
     * Constructor.
     *
     * If $args['settings'] is not defined, use the $id as the setting ID.
     *
     * @since 3.4.0
     *
     * @param WP_Customize_Manager $manager
     * @param string $id
     * @param array $args
     */
    function __construct( $manager, $id, $args = array() ) {
        $keys = array_keys( get_object_vars( $this ) );
        foreach ( $keys as $key ) {
            if ( isset( $args[ $key ] ) )
                $this->$key = $args[ $key ];
        }

        $this->manager = $manager;
        $this->id = $id;


        // Process settings.
        if ( empty( $this->settings ) )
            $this->settings = $id;

        $settings = array();
        if ( is_array( $this->settings ) ) {
            foreach ( $this->settings as $key => $setting ) {
                $settings[ $key ] = $this->manager->get_setting( $setting );
            }
        } else {
            $this->setting = $this->manager->get_setting( $this->settings );
            $settings['default'] = $this->setting;
        }
        $this->settings = $settings;
    }

    /**
     * Enqueue control related scripts/styles.
     *
     * @since 3.4.0
     */
    public function enqueue() {}


    /**
     * Fetch a setting's value.
     * Grabs the main setting by default.
     *
     * @since 3.4.0
     *
     * @param string $setting_key
     * @return mixed The requested setting's value, if the setting exists.
     */
    public final function value( $setting_key = 'default' ) {
        if ( isset( $this->settings[ $setting_key ] ) )
            return $this->settings[ $setting_key ]->value();
    }

    /**
     * Refresh the parameters passed to the JavaScript via JSON.
     *
     * @since 3.4.0
     */
    public function to_json() {
        $this->json['settings'] = array();
        foreach ( $this->settings as $key => $setting ) {
            $this->json['settings'][ $key ] = $setting->id;
        }

        $this->json['type'] = $this->type;
    }

    /**
     * Check if the theme supports the control and check user capabilities.
     *
     * @since 3.4.0
     *
     * @return bool False if theme doesn't support the control or user doesn't have the required permissions, otherwise true.
     */
    public final function check_capabilities() {
        foreach ( $this->settings as $setting ) {
            if ( ! $setting->check_capabilities() )
                return false;
        }

        $section = $this->manager->get_section( $this->section );
        if ( isset( $section ) && ! $section->check_capabilities() )
            return false;

        return true;
    }

    /**
     * Check capabilities and render the control.
     *
     * @since 3.4.0
     * @uses WP_Customize_Control::render()
     */
    public final function maybe_render() {
        if ( ! $this->check_capabilities() )
            return;

        do_action( 'customize_render_control', $this );
        do_action( 'customize_render_control_' . $this->id, $this );

        $this->render();
    }

    /**
     * Render the control. Renders the control wrapper, then calls $this->render_content().
     *
     * @since 3.4.0
     */
    protected function render() {
        $id    = 'customize-control-' . str_replace( '[', '-', str_replace( ']', '', $this->id ) );
        $class = 'customize-control customize-control-' . $this->type;

        ?><li id="<?php echo esc_attr( $id ); ?>" class="<?php echo esc_attr( $class ); ?>">
        <?php $this->render_content(); ?>
        </li><?php
    }

    /**
     * Get the data link parameter for a setting.
     *
     * @since 3.4.0
     *
     * @param string $setting_key
     * @return string Data link parameter, if $setting_key is a valid setting, empty string otherwise.
     */
    public function get_link( $setting_key = 'default' ) {
        if ( ! isset( $this->settings[ $setting_key ] ) )
            return '';

        return 'data-customize-setting-link="' . esc_attr( $this->settings[ $setting_key ]->id ) . '"';
    }

    /**
     * Render the data link parameter for a setting
     *
     * @since 3.4.0
     * @uses WP_Customize_Control::get_link()
     *
     * @param string $setting_key
     */
    public function link( $setting_key = 'default' ) {
        echo $this->get_link( $setting_key );
    }

    /**
     * Render the control's content.
     *
     * Allows the content to be overriden without having to rewrite the wrapper.
     *
     * @since 3.4.0
     */
    protected function render_content() {
        switch( $this->type ) {
            case 'text':
                ?>
                <label>
                    <span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
                    <input type="text" value="<?php echo esc_attr( $this->value() ); ?>" <?php $this->link(); ?> />
                </label>
                <?php
                break;
            case 'checkbox':
                ?>
                <label>
                    <input type="checkbox" value="<?php echo esc_attr( $this->value() ); ?>" <?php $this->link(); checked( $this->value() ); ?> />
                    <?php echo esc_html( $this->label ); ?>
                </label>
                <?php
                break;
            case 'radio':
                if ( empty( $this->choices ) )
                    return;

                $name = '_customize-radio-' . $this->id;

                ?>
                <span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
                <?php
                foreach ( $this->choices as $value => $label ) :
                    ?>
                    <label>
                        <input type="radio" value="<?php echo esc_attr( $value ); ?>" name="<?php echo esc_attr( $name ); ?>" <?php $this->link(); checked( $this->value(), $value ); ?> />
                        <?php echo esc_html( $label ); ?><br/>
                    </label>
                <?php
                endforeach;
                break;
            case 'select':
                if ( empty( $this->choices ) )
                    return;

                ?>
                <label>
                    <span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
                    <select <?php $this->link(); ?>>
                        <?php
                        foreach ( $this->choices as $value => $label )
                            echo '<option value="' . esc_attr( $value ) . '"' . selected( $this->value(), $value, false ) . '>' . $label . '</option>';
                        ?>
                    </select>
                </label>
                <?php
                break;
            case 'dropdown-pages':
                $dropdown = wp_dropdown_pages(
                    array(
                        'name'              => '_customize-dropdown-pages-' . $this->id,
                        'echo'              => 0,
                        'show_option_none'  => __( '&mdash; Select &mdash;' ),
                        'option_none_value' => '0',
                        'selected'          => $this->value(),
                    )
                );

                // Hackily add in the data link parameter.
                $dropdown = str_replace( '<select', '<select ' . $this->get_link(), $dropdown );

                printf(
                    '<label class="customize-control-select"><span class="customize-control-title">%s</span> %s</label>',
                    $this->label,
                    $dropdown
                );
                break;
        }
    }
}