<?php

namespace WordPress\Admin;

/**
 * Language pack upgrader, for updating translations of plugins, themes, and core.
 *
 * @package WordPress
 * @subpackage Upgrader
 * @since 3.7.0
 */
class LanguagePackUpgrader extends WPUpgrader {

    var $result;
    var $bulk = true;

    static function async_upgrade( $upgrader = false ) {
        // Avoid recursion.
        if ( $upgrader && $upgrader instanceof LanguagePackUpgrader )
            return;

        // Nothing to do?
        $language_updates = wp_get_translation_updates();
        if ( ! $language_updates )
            return;

        $skin = new LanguagePackUpgraderSkin( array(
            'skip_header_footer' => true,
        ) );

        $lp_upgrader = new LanguagePackUpgrader( $skin );
        $lp_upgrader->upgrade();
    }

    function upgrade_strings() {
        $this->strings['starting_upgrade'] = __( 'Some of your translations need updating. Sit tight for a few more seconds while we update them as well.' );
        $this->strings['up_to_date'] = __( 'The translation is up to date.' ); // We need to silently skip this case
        $this->strings['no_package'] = __( 'Update package not available.' );
        $this->strings['downloading_package'] = __( 'Downloading translation from <span class="code">%s</span>&#8230;' );
        $this->strings['unpack_package'] = __( 'Unpacking the update&#8230;' );
        $this->strings['process_failed'] = __( 'Translation update failed.' );
        $this->strings['process_success'] = __( 'Translation updated successfully.' );
    }

    function upgrade( $update = false, $args = array() ) {
        if ( $update )
            $update = array( $update );
        $results = $this->bulk_upgrade( $update, $args );
        return $results[0];
    }

    function bulk_upgrade( $language_updates = array(), $args = array() ) {
        global $wp_filesystem;

        $defaults = array(
            'clear_update_cache' => true,
        );
        $parsed_args = wp_parse_args( $args, $defaults );

        $this->init();
        $this->upgrade_strings();

        if ( ! $language_updates )
            $language_updates = wp_get_translation_updates();

        if ( empty( $language_updates ) ) {
            $this->skin->header();
            $this->skin->before();
            $this->skin->set_result( true );
            $this->skin->feedback( 'up_to_date' );
            $this->skin->after();
            $this->skin->bulk_footer();
            $this->skin->footer();
            return true;
        }

        if ( 'upgrader_process_complete' == current_filter() )
            $this->skin->feedback( 'starting_upgrade' );

        add_filter( 'upgrader_source_selection', array( &$this, 'check_package' ), 10, 3 );

        $this->skin->header();

        // Connect to the Filesystem first.
        $res = $this->fs_connect( array( WP_CONTENT_DIR, WP_LANG_DIR ) );
        if ( ! $res ) {
            $this->skin->footer();
            return false;
        }

        $results = array();

        $this->update_count = count( $language_updates );
        $this->update_current = 0;

        // The filesystem's mkdir() is not recursive. Make sure WP_LANG_DIR exists,
        // as we then may need to create a /plugins or /themes directory inside of it.
        $remote_destination = $wp_filesystem->find_folder( WP_LANG_DIR );
        if ( ! $wp_filesystem->exists( $remote_destination ) )
            if ( ! $wp_filesystem->mkdir( $remote_destination, FS_CHMOD_DIR ) )
                return new WordPress\WPError( 'mkdir_failed_lang_dir', $this->strings['mkdir_failed'], $remote_destination );

        foreach ( $language_updates as $language_update ) {

            $this->skin->language_update = $language_update;

            $destination = WP_LANG_DIR;
            if ( 'plugin' == $language_update->type )
                $destination .= '/plugins';
            elseif ( 'theme' == $language_update->type )
                $destination .= '/themes';

            $this->update_current++;

            $options = array(
                'package' => $language_update->package,
                'destination' => $destination,
                'clear_destination' => false,
                'abort_if_destination_exists' => false, // We expect the destination to exist.
                'clear_working' => true,
                'is_multi' => true,
                'hook_extra' => array(
                    'language_update_type' => $language_update->type,
                    'language_update' => $language_update,
                )
            );

            $result = $this->run( $options );

            $results[] = $this->result;

            // Prevent credentials auth screen from displaying multiple times.
            if ( false === $result )
                break;
        }

        $this->skin->bulk_footer();

        $this->skin->footer();

        // Clean up our hooks, in case something else does an upgrade on this connection.
        remove_filter( 'upgrader_source_selection', array( &$this, 'check_package' ), 10, 2 );

        if ( $parsed_args['clear_update_cache'] ) {
            wp_clean_themes_cache( true );
            wp_clean_plugins_cache( true );
            delete_site_transient( 'update_core' );
        }

        return $results;
    }

    function check_package( $source, $remote_source ) {
        global $wp_filesystem;

        if ( is_wp_error( $source ) )
            return $source;

        // Check that the folder contains a valid language.
        $files = $wp_filesystem->dirlist( $remote_source );

        // Check to see if a .po and .mo exist in the folder.
        $po = $mo = false;
        foreach ( (array) $files as $file => $filedata ) {
            if ( '.po' == substr( $file, -3 ) )
                $po = true;
            elseif ( '.mo' == substr( $file, -3 ) )
                $mo = true;
        }

        if ( ! $mo || ! $po )
            return new WordPress\WPError( 'incompatible_archive_pomo', $this->strings['incompatible_archive'],
                __( 'The language pack is missing either the <code>.po</code> or <code>.mo</code> files.' ) );

        return $source;
    }

    function get_name_for_update( $update ) {
        switch ( $update->type ) {
            case 'core':
                return 'WordPress'; // Not translated
                break;
            case 'theme':
                $theme = wp_get_theme( $update->slug );
                if ( $theme->exists() )
                    return $theme->Get( 'Name' );
                break;
            case 'plugin':
                $plugin_data = get_plugins( '/' . $update->slug );
                $plugin_data = array_shift( $plugin_data );
                if ( $plugin_data )
                    return $plugin_data['Name'];
                break;
        }
        return '';
    }

}