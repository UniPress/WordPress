<?php

namespace WordPress\Admin;

class BulkThemeUpgraderSkin extends BulkUpgraderSkin
{
    var $theme_info = array(); // ThemeUpgrader::bulk() will fill this in.

    function __construct($args = array())
    {
        parent::__construct($args);
    }

    function add_strings()
    {
        parent::add_strings();
        $this->upgrader->strings['skin_before_update_header'] = __('Updating Theme %1$s (%2$d/%3$d)');
    }

    function before($title = '')
    {
        parent::before($this->theme_info->display('Name'));
    }

    function after($title = '')
    {
        parent::after($this->theme_info->display('Name'));
    }

    function bulk_footer()
    {
        parent::bulk_footer();
        $update_actions = array(
            'themes_page' => '<a href="' . self_admin_url('themes.php') . '" title="' . esc_attr__(
                    'Go to themes page'
                ) . '" target="_parent">' . __('Return to Themes page') . '</a>',
            'updates_page' => '<a href="' . self_admin_url('update-core.php') . '" title="' . esc_attr__(
                    'Go to WordPress Updates page'
                ) . '" target="_parent">' . __('Return to WordPress Updates') . '</a>'
        );
        if (!current_user_can('switch_themes') && !current_user_can('edit_theme_options')) {
            unset($update_actions['themes_page']);
        }

        $update_actions = apply_filters('update_bulk_theme_complete_actions', $update_actions, $this->theme_info);
        if (!empty($update_actions)) {
            $this->feedback(implode(' | ', (array)$update_actions));
        }
    }
}