<?php

namespace WordPress;

/**
 * A setting that is used to filter a value, but will not save the results.
 *
 * Results should be properly handled using another setting or callback.
 *
 * @package WordPress
 * @subpackage Customize
 * @since 3.4.0
 */
class WPCustomizeFilterSetting extends WPCustomizeSetting {

    /**
     * @since 3.4.0
     */
    public function update( $value ) {}
}
