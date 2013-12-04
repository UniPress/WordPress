<?php

namespace WordPress\Admin;

use WordPress\Admin\WPUpgraderSkin;

/**
 * Translation Upgrader Skin for WordPress Translation Upgrades.
 *
 * @package WordPress
 * @subpackage Upgrader
 * @since 3.7.0
 */
class LanguagePackUpgraderSkin extends WPUpgraderSkin
{
    var $language_update = null;
    var $done_header = false;
    var $display_footer_actions = true;

    function __construct($args = array())
    {
        $defaults = array(
            'url' => '',
            'nonce' => '',
            'title' => __('Update Translations'),
            'skip_header_footer' => false
        );
        $args = wp_parse_args($args, $defaults);
        if ($args['skip_header_footer']) {
            $this->done_header = true;
            $this->display_footer_actions = false;
        }
        parent::__construct($args);
    }

    function before()
    {
        $name = $this->upgrader->get_name_for_update($this->language_update);

        echo '<div class="update-messages lp-show-latest">';

        printf(
            '<h4>' . __('Updating translations for %1$s (%2$s)&#8230;') . '</h4>',
            $name,
            $this->language_update->language
        );
    }

    function error($error)
    {
        echo '<div class="lp-error">';
        parent::error($error);
        echo '</div>';
    }

    function after()
    {
        echo '</div>';
    }

    function bulk_footer()
    {
        $update_actions = array();
        $update_actions['updates_page'] = '<a href="' . self_admin_url('update-core.php') . '" title="' . esc_attr__(
                'Go to WordPress Updates page'
            ) . '" target="_parent">' . __('Return to WordPress Updates') . '</a>';
        $update_actions = apply_filters('update_translations_complete_actions', $update_actions);

        if ($update_actions && $this->display_footer_actions) {
            $this->feedback(implode(' | ', $update_actions));
        }

        parent::footer();
    }
}