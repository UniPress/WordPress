<?php

namespace WordPress;

/**
 * @package WordPress
 * @subpackage Customize
 * @since 3.4.0
 */
final class WPCustomizeBackgroundImageSetting extends WPCustomizeSetting {
    public $id = 'background_image_thumb';

    /**
     * @since 3.4.0
     * @uses remove_theme_mod()
     *
     * @param $value
     */
    public function update( $value ) {
        remove_theme_mod( 'background_image_thumb' );
    }
}