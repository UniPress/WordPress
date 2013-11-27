<?php

namespace WordPress;

/**
 * Customize Background Image Control Class
 *
 * @package WordPress
 * @subpackage Customize
 * @since 3.4.0
 */
class WP_Customize_Background_Image_Control extends WP_Customize_Image_Control {

    /**
     * Constructor.
     *
     * @since 3.4.0
     * @uses WP_Customize_Image_Control::__construct()
     *
     * @param WP_Customize_Manager $manager
     */
    public function __construct( $manager ) {
        parent::__construct( $manager, 'background_image', array(
                'label'    => __( 'Background Image' ),
                'section'  => 'background_image',
                'context'  => 'custom-background',
                'get_url'  => 'get_background_image',
            ) );

        if ( $this->setting->default )
            $this->add_tab( 'default',  __('Default'),  array( $this, 'tab_default_background' ) );
    }

    /**
     * @since 3.4.0
     */
    public function tab_uploaded() {
        $backgrounds = get_posts( array(
                'post_type'  => 'attachment',
                'meta_key'   => '_wp_attachment_is_custom_background',
                'meta_value' => $this->manager->get_stylesheet(),
                'orderby'    => 'none',
                'nopaging'   => true,
            ) );

        ?><div class="uploaded-target"></div><?php

        if ( empty( $backgrounds ) )
            return;

        foreach ( (array) $backgrounds as $background )
            $this->print_tab_image( esc_url_raw( $background->guid ) );
    }

    /**
     * @since 3.4.0
     * @uses WP_Customize_Image_Control::print_tab_image()
     */
    public function tab_default_background() {
        $this->print_tab_image( $this->setting->default );
    }
}