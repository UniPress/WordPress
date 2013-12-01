<?php
/**
 * WordPress Rewrite API
 *
 * @package WordPress
 * @subpackage Rewrite
 */

/**
 * Add a straight rewrite rule.
 *
 * @see WP_Rewrite::add_rule() for long description.
 * @since 2.1.0
 *
 * @param string $regex Regular Expression to match request against.
 * @param string $redirect Page to redirect to.
 * @param string $after Optional, default is 'bottom'. Where to add rule, can also be 'top'.
 */
function add_rewrite_rule($regex, $redirect, $after = 'bottom') {
	//global $wp_rewrite;
    UniPress\UniPress::getService('wp_rewrite')->add_rule($regex, $redirect, $after);
}

/**
 * Add a new rewrite tag (like %postname%).
 *
 * The $query parameter is optional. If it is omitted you must ensure that
 * you call this on, or before, the 'init' hook. This is because $query defaults
 * to "$tag=", and for this to work a new query var has to be added.
 *
 * @see WP_Rewrite::add_rewrite_tag()
 * @since 2.1.0
 *
 * @param string $tag Name of the new rewrite tag.
 * @param string $regex Regular expression to substitute the tag for in rewrite rules.
 * @param string $query String to append to the rewritten query. Must end in '='. Optional.
 */
function add_rewrite_tag( $tag, $regex, $query = '' ) {
	// validate the tag's name
	if ( strlen( $tag ) < 3 || $tag[0] != '%' || $tag[ strlen($tag) - 1 ] != '%' )
		return;

	global $wp;//$wp_rewrite, $wp;

	if ( empty( $query ) ) {
		$qv = trim( $tag, '%' );
		$wp->add_query_var( $qv );
		$query = $qv . '=';
	}

    UniPress\UniPress::getService('wp_rewrite')->add_rewrite_tag( $tag, $regex, $query );
}

/**
 * Add permalink structure.
 *
 * @see WP_Rewrite::add_permastruct()
 * @since 3.0.0
 *
 * @param string $name Name for permalink structure.
 * @param string $struct Permalink structure.
 * @param array $args Optional configuration for building the rules from the permalink structure,
 *     see {@link WP_Rewrite::add_permastruct()} for full details.
 */
function add_permastruct( $name, $struct, $args = array() ) {
	//global $wp_rewrite;

	// backwards compatibility for the old parameters: $with_front and $ep_mask
	if ( ! is_array( $args ) )
		$args = array( 'with_front' => $args );
	if ( func_num_args() == 4 )
		$args['ep_mask'] = func_get_arg( 3 );

	return UniPress\UniPress::getService('wp_rewrite')->add_permastruct( $name, $struct, $args );
}

/**
 * Add a new feed type like /atom1/.
 *
 * @since 2.1.0
 *
 * @param string $feedname
 * @param callback $function Callback to run on feed display.
 * @return string Feed action name.
 */
function add_feed($feedname, $function) {
	//global $wp_rewrite;
	if ( ! in_array($feedname, UniPress\UniPress::getService('wp_rewrite')->feeds) ) //override the file if it is
		$wp_rewrite->feeds[] = $feedname;
	$hook = 'do_feed_' . $feedname;
	// Remove default function hook
	remove_action($hook, $hook);
	add_action($hook, $function, 10, 1);
	return $hook;
}

/**
 * Remove rewrite rules and then recreate rewrite rules.
 *
 * @see WP_Rewrite::flush_rules()
 * @since 3.0.0
 *
 * @param bool $hard Whether to update .htaccess (hard flush) or just update
 * 	rewrite_rules transient (soft flush). Default is true (hard).
 */
function flush_rewrite_rules( $hard = true ) {
	//global $wp_rewrite;
    UniPress\UniPress::getService('wp_rewrite')->flush_rules( $hard );
}

/**
 * Endpoint Mask for default, which is nothing.
 *
 * @since 2.1.0
 */
define('EP_NONE', 0);

/**
 * Endpoint Mask for Permalink.
 *
 * @since 2.1.0
 */
define('EP_PERMALINK', 1);

/**
 * Endpoint Mask for Attachment.
 *
 * @since 2.1.0
 */
define('EP_ATTACHMENT', 2);

/**
 * Endpoint Mask for date.
 *
 * @since 2.1.0
 */
define('EP_DATE', 4);

/**
 * Endpoint Mask for year
 *
 * @since 2.1.0
 */
define('EP_YEAR', 8);

/**
 * Endpoint Mask for month.
 *
 * @since 2.1.0
 */
define('EP_MONTH', 16);

/**
 * Endpoint Mask for day.
 *
 * @since 2.1.0
 */
define('EP_DAY', 32);

/**
 * Endpoint Mask for root.
 *
 * @since 2.1.0
 */
define('EP_ROOT', 64);

/**
 * Endpoint Mask for comments.
 *
 * @since 2.1.0
 */
define('EP_COMMENTS', 128);

/**
 * Endpoint Mask for searches.
 *
 * @since 2.1.0
 */
define('EP_SEARCH', 256);

/**
 * Endpoint Mask for categories.
 *
 * @since 2.1.0
 */
define('EP_CATEGORIES', 512);

/**
 * Endpoint Mask for tags.
 *
 * @since 2.3.0
 */
define('EP_TAGS', 1024);

/**
 * Endpoint Mask for authors.
 *
 * @since 2.1.0
 */
define('EP_AUTHORS', 2048);

/**
 * Endpoint Mask for pages.
 *
 * @since 2.1.0
 */
define('EP_PAGES', 4096);

/**
 * Endpoint Mask for all archive views.
 *
 * @since 3.7.0
 */
define( 'EP_ALL_ARCHIVES', EP_DATE | EP_YEAR | EP_MONTH | EP_DAY | EP_CATEGORIES | EP_TAGS | EP_AUTHORS );

/**
 * Endpoint Mask for everything.
 *
 * @since 2.1.0
 */
define( 'EP_ALL', EP_PERMALINK | EP_ATTACHMENT | EP_ROOT | EP_COMMENTS | EP_SEARCH | EP_PAGES | EP_ALL_ARCHIVES );

/**
 * Add an endpoint, like /trackback/.
 *
 * Adding an endpoint creates extra rewrite rules for each of the matching
 * places specified by the provided bitmask. For example:
 *
 * <code>
 * add_rewrite_endpoint( 'json', EP_PERMALINK | EP_PAGES );
 * </code>
 *
 * will add a new rewrite rule ending with "json(/(.*))?/?$" for every permastruct
 * that describes a permalink (post) or page. This is rewritten to "json=$match"
 * where $match is the part of the URL matched by the endpoint regex (e.g. "foo" in
 * "<permalink>/json/foo/").
 *
 * A new query var with the same name as the endpoint will also be created.
 *
 * When specifying $places ensure that you are using the EP_* constants (or a
 * combination of them using the bitwise OR operator) as their values are not
 * guaranteed to remain static (especially EP_ALL).
 *
 * Be sure to flush the rewrite rules - flush_rewrite_rules() - when your plugin gets
 * activated and deactivated.
 *
 * @since 2.1.0
 * @see WP_Rewrite::add_endpoint()
 * @global object $wp_rewrite
 *
 * @param string $name Name of the endpoint.
 * @param int $places Endpoint mask describing the places the endpoint should be added.
 */
function add_rewrite_endpoint( $name, $places ) {
	//global $wp_rewrite;
    UniPress\UniPress::getService('wp_rewrite')->add_endpoint( $name, $places );
}

/**
 * Filter the URL base for taxonomies.
 *
 * To remove any manually prepended /index.php/.
 *
 * @access private
 * @since 2.6.0
 *
 * @param string $base The taxonomy base that we're going to filter
 * @return string
 */
function _wp_filter_taxonomy_base( $base ) {
	if ( !empty( $base ) ) {
		$base = preg_replace( '|^/index\.php/|', '', $base );
		$base = trim( $base, '/' );
	}
	return $base;
}

/**
 * Examine a url and try to determine the post ID it represents.
 *
 * Checks are supposedly from the hosted site blog.
 *
 * @since 1.0.0
 *
 * @param string $url Permalink to check.
 * @return int Post ID, or 0 on failure.
 */
function url_to_postid($url) {
	//global $wp_rewrite;

    $wp_rewrite = UniPress\UniPress::getService('wp_rewrite');

	$url = apply_filters('url_to_postid', $url);

	// First, check to see if there is a 'p=N' or 'page_id=N' to match against
	if ( preg_match('#[?&](p|page_id|attachment_id)=(\d+)#', $url, $values) )	{
		$id = absint($values[2]);
		if ( $id )
			return $id;
	}

	// Check to see if we are using rewrite rules
	$rewrite = $wp_rewrite->wp_rewrite_rules();

	// Not using rewrite rules, and 'p=N' and 'page_id=N' methods failed, so we're out of options
	if ( empty($rewrite) )
		return 0;

	// Get rid of the #anchor
	$url_split = explode('#', $url);
	$url = $url_split[0];

	// Get rid of URL ?query=string
	$url_split = explode('?', $url);
	$url = $url_split[0];

	// Add 'www.' if it is absent and should be there
	if ( false !== strpos(home_url(), '://www.') && false === strpos($url, '://www.') )
		$url = str_replace('://', '://www.', $url);

	// Strip 'www.' if it is present and shouldn't be
	if ( false === strpos(home_url(), '://www.') )
		$url = str_replace('://www.', '://', $url);

	// Strip 'index.php/' if we're not using path info permalinks
	if ( !$wp_rewrite->using_index_permalinks() )
		$url = str_replace( $wp_rewrite->index . '/', '', $url );

	if ( false !== strpos( trailingslashit( $url ), home_url( '/' ) ) ) {
		// Chop off http://domain.com/[path]
		$url = str_replace(home_url(), '', $url);
	} else {
		// Chop off /path/to/blog
		$home_path = parse_url( home_url( '/' ) );
		$home_path = isset( $home_path['path'] ) ? $home_path['path'] : '' ;
		$url = preg_replace( sprintf( '#^%s#', preg_quote( $home_path ) ), '', trailingslashit( $url ) );
	}

	// Trim leading and lagging slashes
	$url = trim($url, '/');

	$request = $url;
	$post_type_query_vars = array();

	foreach ( get_post_types( array() , 'objects' ) as $post_type => $t ) {
		if ( ! empty( $t->query_var ) )
			$post_type_query_vars[ $t->query_var ] = $post_type;
	}

	// Look for matches.
	$request_match = $request;
	foreach ( (array)$rewrite as $match => $query) {

		// If the requesting file is the anchor of the match, prepend it
		// to the path info.
		if ( !empty($url) && ($url != $request) && (strpos($match, $url) === 0) )
			$request_match = $url . '/' . $request;

		if ( preg_match("!^$match!", $request_match, $matches) ) {

			if ( $wp_rewrite->use_verbose_page_rules && preg_match( '/pagename=\$matches\[([0-9]+)\]/', $query, $varmatch ) ) {
				// this is a verbose page match, lets check to be sure about it
				if ( ! get_page_by_path( $matches[ $varmatch[1] ] ) )
					continue;
			}

			// Got a match.
			// Trim the query of everything up to the '?'.
			$query = preg_replace("!^.+\?!", '', $query);

			// Substitute the substring matches into the query.
			$query = addslashes(WP_MatchesMapRegex::apply($query, $matches));

			// Filter out non-public query vars
			global $wp;
			parse_str( $query, $query_vars );
			$query = array();
			foreach ( (array) $query_vars as $key => $value ) {
				if ( in_array( $key, $wp->public_query_vars ) ){
					$query[$key] = $value;
					if ( isset( $post_type_query_vars[$key] ) ) {
						$query['post_type'] = $post_type_query_vars[$key];
						$query['name'] = $value;
					}
				}
			}

			// Do the query
			$query = new WP_Query( $query );
			if ( ! empty( $query->posts ) && $query->is_singular )
				return $query->post->ID;
			else
				return 0;
		}
	}
	return 0;
}
