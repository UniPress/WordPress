<?php

namespace WordPress\Admin;

use WordPress\Admin\BulkUpgraderSkin;

class BulkPluginUpgraderSkin extends BulkUpgraderSkin
{
    var $plugin_info = array(); // PluginUpgrader::bulk() will fill this in.

    function __construct($args = array())
    {
        parent::__construct($args);
    }

    function add_strings()
    {
        parent::add_strings();
        $this->upgrader->strings['skin_before_update_header'] = __('Updating Plugin %1$s (%2$d/%3$d)');
    }

    function before($title = '')
    {
        parent::before($this->plugin_info['Title']);
    }

    function after($title = '')
    {
        parent::after($this->plugin_info['Title']);
    }

    function bulk_footer()
    {
        parent::bulk_footer();
        $update_actions = array(
            'plugins_page' => '<a href="' . self_admin_url('plugins.php') . '" title="' . esc_attr__(
                    'Go to plugins page'
                ) . '" target="_parent">' . __('Return to Plugins page') . '</a>',
            'updates_page' => '<a href="' . self_admin_url('update-core.php') . '" title="' . esc_attr__(
                    'Go to WordPress Updates page'
                ) . '" target="_parent">' . __('Return to WordPress Updates') . '</a>'
        );
        if (!current_user_can('activate_plugins')) {
            unset($update_actions['plugins_page']);
        }

        $update_actions = apply_filters('update_bulk_plugins_complete_actions', $update_actions, $this->plugin_info);
        if (!empty($update_actions)) {
            $this->feedback(implode(' | ', (array)$update_actions));
        }
    }
}