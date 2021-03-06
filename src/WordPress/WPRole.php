<?php

namespace WordPress;

/**
 * WordPress Role class.
 *
 * @since 2.0.0
 * @package WordPress
 * @subpackage User
 */
class WPRole {
    /**
     * Role name.
     *
     * @since 2.0.0
     * @access public
     * @var string
     */
    var $name;

    /**
     * List of capabilities the role contains.
     *
     * @since 2.0.0
     * @access public
     * @var array
     */
    var $capabilities;

    /**
     * Constructor - Set up object properties.
     *
     * The list of capabilities, must have the key as the name of the capability
     * and the value a boolean of whether it is granted to the role.
     *
     * @since 2.0.0
     * @access public
     *
     * @param string $role Role name.
     * @param array $capabilities List of capabilities.
     */
    function __construct( $role, $capabilities ) {
        $this->name = $role;
        $this->capabilities = $capabilities;
    }

    /**
     * Assign role a capability.
     *
     * @see WP_Roles::add_cap() Method uses implementation for role.
     * @since 2.0.0
     * @access public
     *
     * @param string $cap Capability name.
     * @param bool $grant Whether role has capability privilege.
     */
    function add_cap( $cap, $grant = true ) {
        global $wp_roles;

        if ( ! isset( $wp_roles ) )
            $wp_roles = new WPRoles();

        $this->capabilities[$cap] = $grant;
        $wp_roles->add_cap( $this->name, $cap, $grant );
    }

    /**
     * Remove capability from role.
     *
     * This is a container for {@link WPRoles::remove_cap()} to remove the
     * capability from the role. That is to say, that {@link
     * WPRoles::remove_cap()} implements the functionality, but it also makes
     * sense to use this class, because you don't need to enter the role name.
     *
     * @since 2.0.0
     * @access public
     *
     * @param string $cap Capability name.
     */
    function remove_cap( $cap ) {
        global $wp_roles;

        if ( ! isset( $wp_roles ) )
            $wp_roles = new WPRoles();

        unset( $this->capabilities[$cap] );
        $wp_roles->remove_cap( $this->name, $cap );
    }

    /**
     * Whether role has capability.
     *
     * The capabilities is passed through the 'role_has_cap' filter. The first
     * parameter for the hook is the list of capabilities the class has
     * assigned. The second parameter is the capability name to look for. The
     * third and final parameter for the hook is the role name.
     *
     * @since 2.0.0
     * @access public
     *
     * @param string $cap Capability name.
     * @return bool True, if user has capability. False, if doesn't have capability.
     */
    function has_cap( $cap ) {
        /**
         * Filter which capabilities a role has.
         *
         * @since 2.0.0
         *
         * @param array  $capabilities Array of role capabilities.
         * @param string $cap          Capability name.
         * @param string $name         Role name.
         */
        $capabilities = apply_filters( 'role_has_cap', $this->capabilities, $cap, $this->name );
        if ( !empty( $capabilities[$cap] ) )
            return $capabilities[$cap];
        else
            return false;
    }

}