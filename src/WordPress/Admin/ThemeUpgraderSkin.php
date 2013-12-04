<?php

namespace WordPress\Admin;

/**
 * Theme Upgrader Skin for WordPress Theme Upgrades.
 *
 * @package WordPress
 * @subpackage Upgrader
 * @since 2.8.0
 */
class ThemeUpgraderSkin extends WPUpgraderSkin
{
    var $theme = '';

    function __construct($args = array())
    {
        $defaults = array('url' => '', 'theme' => '', 'nonce' => '', 'title' => __('Update Theme'));
        $args = wp_parse_args($args, $defaults);

        $this->theme = $args['theme'];

        parent::__construct($args);
    }

    function after()
    {

        $update_actions = array();
        if (!empty($this->upgrader->result['destination_name']) && $theme_info = $this->upgrader->theme_info()) {
            $name = $theme_info->display('Name');
            $stylesheet = $this->upgrader->result['destination_name'];
            $template = $theme_info->get_template();

            $preview_link = add_query_arg(
                array(
                    'preview' => 1,
                    'template' => urlencode($template),
                    'stylesheet' => urlencode($stylesheet),
                ),
                trailingslashit(home_url())
            );

            $activate_link = add_query_arg(
                array(
                    'action' => 'activate',
                    'template' => urlencode($template),
                    'stylesheet' => urlencode($stylesheet),
                ),
                admin_url('themes.php')
            );
            $activate_link = wp_nonce_url($activate_link, 'switch-theme_' . $stylesheet);

            if (get_stylesheet() == $stylesheet) {
                if (current_user_can('edit_theme_options')) {
                    $update_actions['preview'] = '<a href="' . wp_customize_url(
                            $stylesheet
                        ) . '" class="hide-if-no-customize load-customize" title="' . esc_attr(
                            sprintf(__('Customize &#8220;%s&#8221;'), $name)
                        ) . '">' . __('Customize') . '</a>';
                }
            } elseif (current_user_can('switch_themes')) {
                $update_actions['preview'] = '<a href="' . esc_url(
                        $preview_link
                    ) . '" class="hide-if-customize" title="' . esc_attr(
                        sprintf(__('Preview &#8220;%s&#8221;'), $name)
                    ) . '">' . __('Preview') . '</a>';
                $update_actions['preview'] .= '<a href="' . wp_customize_url(
                        $stylesheet
                    ) . '" class="hide-if-no-customize load-customize" title="' . esc_attr(
                        sprintf(__('Preview &#8220;%s&#8221;'), $name)
                    ) . '">' . __('Live Preview') . '</a>';
                $update_actions['activate'] = '<a href="' . esc_url(
                        $activate_link
                    ) . '" class="activatelink" title="' . esc_attr(
                        sprintf(__('Activate &#8220;%s&#8221;'), $name)
                    ) . '">' . __('Activate') . '</a>';
            }

            if (!$this->result || is_wp_error($this->result) || is_network_admin()) {
                unset($update_actions['preview'], $update_actions['activate']);
            }
        }

        $update_actions['themes_page'] = '<a href="' . self_admin_url('themes.php') . '" title="' . esc_attr__(
                'Return to Themes page'
            ) . '" target="_parent">' . __('Return to Themes page') . '</a>';

        $update_actions = apply_filters('update_theme_complete_actions', $update_actions, $this->theme);
        if (!empty($update_actions)) {
            $this->feedback(implode(' | ', (array)$update_actions));
        }
    }
}