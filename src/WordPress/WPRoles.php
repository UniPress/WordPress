<?php

namespace WordPress;

/**
 * WordPress User Roles.
 *
 * The role option is simple, the structure is organized by role name that store
 * the name in value of the 'name' key. The capabilities are stored as an array
 * in the value of the 'capability' key.
 *
 * <code>
 * array (
 *		'rolename' => array (
 *			'name' => 'rolename',
 *			'capabilities' => array()
 *		)
 * )
 * </code>
 *
 * @since 2.0.0
 * @package WordPress
 * @subpackage User
 */
class WPRoles {
    /**
     * List of roles and capabilities.
     *
     * @since 2.0.0
     * @access public
     * @var array
     */
    var $roles;

    /**
     * List of the role objects.
     *
     * @since 2.0.0
     * @access public
     * @var array
     */
    var $role_objects = array();

    /**
     * List of role names.
     *
     * @since 2.0.0
     * @access public
     * @var array
     */
    var $role_names = array();

    /**
     * Option name for storing role list.
     *
     * @since 2.0.0
     * @access public
     * @var string
     */
    var $role_key;

    /**
     * Whether to use the database for retrieval and storage.
     *
     * @since 2.1.0
     * @access public
     * @var bool
     */
    var $use_db = true;

    /**
     * Constructor
     *
     * @since 2.0.0
     */
    function __construct() {
        $this->_init();
    }

    /**
     * Set up the object properties.
     *
     * The role key is set to the current prefix for the $wpdb object with
     * 'user_roles' appended. If the $wp_user_roles global is set, then it will
     * be used and the role option will not be updated or used.
     *
     * @since 2.1.0
     * @access protected
     * @uses $wpdb Used to get the database prefix.
     * @global array $wp_user_roles Used to set the 'roles' property value.
     */
    function _init () {
        global $wpdb, $wp_user_roles;
        $this->role_key = $wpdb->get_blog_prefix() . 'user_roles';
        if ( ! empty( $wp_user_roles ) ) {
            $this->roles = $wp_user_roles;
            $this->use_db = false;
        } else {
            $this->roles = get_option( $this->role_key );
        }

        if ( empty( $this->roles ) )
            return;

        $this->role_objects = array();
        $this->role_names =  array();
        foreach ( array_keys( $this->roles ) as $role ) {
            $this->role_objects[$role] = new WPRole( $role, $this->roles[$role]['capabilities'] );
            $this->role_names[$role] = $this->roles[$role]['name'];
        }
    }

    /**
     * Reinitialize the object
     *
     * Recreates the role objects. This is typically called only by switch_to_blog()
     * after switching wpdb to a new blog ID.
     *
     * @since 3.5.0
     * @access public
     */
    function reinit() {
        // There is no need to reinit if using the wp_user_roles global.
        if ( ! $this->use_db )
            return;

        global $wpdb, $wp_user_roles;

        // Duplicated from _init() to avoid an extra function call.
        $this->role_key = $wpdb->get_blog_prefix() . 'user_roles';
        $this->roles = get_option( $this->role_key );
        if ( empty( $this->roles ) )
            return;

        $this->role_objects = array();
        $this->role_names =  array();
        foreach ( array_keys( $this->roles ) as $role ) {
            $this->role_objects[$role] = new \WordPress\WPRole( $role, $this->roles[$role]['capabilities'] );
            $this->role_names[$role] = $this->roles[$role]['name'];
        }
    }

    /**
     * Add role name with capabilities to list.
     *
     * Updates the list of roles, if the role doesn't already exist.
     *
     * The capabilities are defined in the following format `array( 'read' => true );`
     * To explicitly deny a role a capability you set the value for that capability to false.
     *
     * @since 2.0.0
     * @access public
     *
     * @param string $role Role name.
     * @param string $display_name Role display name.
     * @param array $capabilities List of role capabilities in the above format.
     * @return WPRole|null WPRole object if role is added, null if already exists.
     */
    function add_role( $role, $display_name, $capabilities = array() ) {
        if ( isset( $this->roles[$role] ) )
            return;

        $this->roles[$role] = array(
            'name' => $display_name,
            'capabilities' => $capabilities
        );
        if ( $this->use_db )
            update_option( $this->role_key, $this->roles );
        $this->role_objects[$role] = new WP_Role( $role, $capabilities );
        $this->role_names[$role] = $display_name;
        return $this->role_objects[$role];
    }

    /**
     * Remove role by name.
     *
     * @since 2.0.0
     * @access public
     *
     * @param string $role Role name.
     */
    function remove_role( $role ) {
        if ( ! isset( $this->role_objects[$role] ) )
            return;

        unset( $this->role_objects[$role] );
        unset( $this->role_names[$role] );
        unset( $this->roles[$role] );

        if ( $this->use_db )
            update_option( $this->role_key, $this->roles );

        if ( get_option( 'default_role' ) == $role )
            update_option( 'default_role', 'subscriber' );
    }

    /**
     * Add capability to role.
     *
     * @since 2.0.0
     * @access public
     *
     * @param string $role Role name.
     * @param string $cap Capability name.
     * @param bool $grant Optional, default is true. Whether role is capable of performing capability.
     */
    function add_cap( $role, $cap, $grant = true ) {
        if ( ! isset( $this->roles[$role] ) )
            return;

        $this->roles[$role]['capabilities'][$cap] = $grant;
        if ( $this->use_db )
            update_option( $this->role_key, $this->roles );
    }

    /**
     * Remove capability from role.
     *
     * @since 2.0.0
     * @access public
     *
     * @param string $role Role name.
     * @param string $cap Capability name.
     */
    function remove_cap( $role, $cap ) {
        if ( ! isset( $this->roles[$role] ) )
            return;

        unset( $this->roles[$role]['capabilities'][$cap] );
        if ( $this->use_db )
            update_option( $this->role_key, $this->roles );
    }

    /**
     * Retrieve role object by name.
     *
     * @since 2.0.0
     * @access public
     *
     * @param string $role Role name.
     * @return WPRole|null WPRole object if found, null if the role does not exist.
     */
    function get_role( $role ) {
        if ( isset( $this->role_objects[$role] ) )
            return $this->role_objects[$role];
        else
            return null;
    }

    /**
     * Retrieve list of role names.
     *
     * @since 2.0.0
     * @access public
     *
     * @return array List of role names.
     */
    function get_names() {
        return $this->role_names;
    }

    /**
     * Whether role name is currently in the list of available roles.
     *
     * @since 2.0.0
     * @access public
     *
     * @param string $role Role name to look up.
     * @return bool
     */
    function is_role( $role ) {
        return isset( $this->role_names[$role] );
    }
}