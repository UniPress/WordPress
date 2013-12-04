<?php
use WordPress\Admin\WPListTable;

/**
 * Helper functions for displaying a list of items in an ajaxified HTML table.
 *
 * @package WordPress
 * @subpackage List_Table
 * @since 3.1.0
 */

/**
 * Fetch an instance of a WPListTable class.
 *
 * @access private
 * @since 3.1.0
 *
 * @param string $class The type of the list table, which is the class name.
 * @param array $args Optional. Arguments to pass to the class. Accepts 'screen'.
 * @return object|bool Object on success, false if the class does not exist.
 */
function _get_list_table( $class, $args = array() ) {
	$core_classes = array(
		//Site Admin
		'\WordPress\Admin\WPPostsListTable' => 'posts',
		'\WordPress\Admin\WPMediaListTable' => 'media',
		'\WordPress\Admin\WPTermsListTable' => 'terms',
		'\WordPress\Admin\WPUsersListTable' => 'users',
		'\WordPress\Admin\WPCommentsListTable' => 'comments',
		'\WordPress\Admin\WPPostCommentsListTable' => 'comments',
		'\WordPress\Admin\WPLinksListTable' => 'links',
		'\WordPress\Admin\WPPluginInstallListTable' => 'plugin-install',
		'\WordPress\Admin\WPThemesListTable' => 'themes',
		'\WordPress\Admin\WPThemeInstallListTable' => array( 'themes', 'theme-install' ),
		'\WordPress\Admin\WPPluginsListTable' => 'plugins',
		// Network Admin
		'\WordPress\Admin\WPMSSitesListTable' => 'ms-sites',
		'\WordPress\Admin\WPMSUsersListTable' => 'ms-users',
		'\WordPress\Admin\WPMSThemesListTable' => 'ms-themes',
	);

	if ( isset( $core_classes[ $class ] ) ) {

		if ( isset( $args['screen'] ) )
			$args['screen'] = convert_to_screen( $args['screen'] );
		elseif ( isset( $GLOBALS['hook_suffix'] ) )
			$args['screen'] = get_current_screen();
		else
			$args['screen'] = null;

		return new $class( $args );
	}

	return false;
}

/**
 * Register column headers for a particular screen.
 *
 * @since 2.7.0
 *
 * @param string $screen The handle for the screen to add help to. This is usually the hook name returned by the add_*_page() functions.
 * @param array $columns An array of columns with column IDs as the keys and translated column names as the values
 * @see get_column_headers(), print_column_headers(), get_hidden_columns()
 */
function register_column_headers($screen, $columns) {
	$wp_list_table = new _WP_List_Table_Compat($screen, $columns);
}

/**
 * Prints column headers for a particular screen.
 *
 * @since 2.7.0
 */
function print_column_headers($screen, $id = true) {
	$wp_list_table = new _WP_List_Table_Compat($screen);

	$wp_list_table->print_column_headers($id);
}

/**
 * Helper class to be used only by back compat functions
 *
 * @since 3.1.0
 */
class _WP_List_Table_Compat extends WPListTable {
	var $_screen;
	var $_columns;

	function _WP_List_Table_Compat( $screen, $columns = array() ) {
		if ( is_string( $screen ) )
			$screen = convert_to_screen( $screen );

		$this->_screen = $screen;

		if ( !empty( $columns ) ) {
			$this->_columns = $columns;
			add_filter( 'manage_' . $screen->id . '_columns', array( $this, 'get_columns' ), 0 );
		}
	}

	function get_column_info() {
		$columns = get_column_headers( $this->_screen );
		$hidden = get_hidden_columns( $this->_screen );
		$sortable = array();

		return array( $columns, $hidden, $sortable );
	}

	function get_columns() {
		return $this->_columns;
	}
}
