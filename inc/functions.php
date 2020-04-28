<?php
/**
 * Sitemaps: Public functions
 *
 * This file cocntains a variety of public functions developers can use to interact with
 * the XML Sitemaps API.
 *
 * @package WordPress
 * @subpackage Sitemaps
 * @since x.x.x
 */

/**
 * Retrieves the current Sitemaps server instance.
 *
 * @return Core_Sitemaps|null Core_Sitemaps instance, or null of sitemaps are disabled.
 */
function core_sitemaps_get_server() {
	/**
	 * Global Core Sitemaps instance.
	 *
	 * @var Core_Sitemaps $core_sitemaps
	 */
	global $core_sitemaps;

	$is_enabled = (bool) get_option( 'blog_public' );

	/**
	 * Filters whether XML Sitemaps are enabled or not.
	 *
	 * @param bool $is_enabled Whether XML Sitemaps are enabled or not. Defaults to true for public sites.
	 */
	$is_enabled = (bool) apply_filters( 'core_sitemaps_is_enabled', $is_enabled );

	if ( ! $is_enabled ) {
		return null;
	}

	// If there isn't a global instance, set and bootstrap the sitemaps system.
	if ( empty( $core_sitemaps ) ) {
		$core_sitemaps = new Core_Sitemaps();
		$core_sitemaps->init();

		/**
		 * Fires when initializing the Core_Sitemaps object.
		 *
		 * Additional sitemaps should be registered on this hook.
		 *
		 * @since 0.1.0
		 *
		 * @param core_sitemaps $core_sitemaps Server object.
		 */
		do_action( 'core_sitemaps_init', $core_sitemaps );
	}

	return $core_sitemaps;
}

/**
 * Get a list of sitemaps.
 *
 * @return array $sitemaps A list of registered sitemap providers.
 */
function core_sitemaps_get_sitemaps() {
	$core_sitemaps = core_sitemaps_get_server();

	if ( ! $core_sitemaps ) {
		return array();
	}

	return $core_sitemaps->registry->get_sitemaps();
}

/**
 * Register a new sitemap provider.
 *
 * @param string                 $name     Unique name for the sitemap provider.
 * @param Core_Sitemaps_Provider $provider The `Core_Sitemaps_Provider` instance implementing the sitemap.
 * @return bool Returns true if the sitemap was added. False on failure.
 */
function core_sitemaps_register_sitemap( $name, $provider ) {
	$core_sitemaps = core_sitemaps_get_server();

	if ( ! $core_sitemaps ) {
		return false;
	}

	return $core_sitemaps->registry->add_sitemap($name, $provider);
}

/**
 * Get the maximum number of URLs for a sitemap.
 *
 * @since 0.1.0
 *
 * @param string $type Optional. The type of sitemap to be filtered. Default ''.
 * @return int The maximum number of URLs.
 */
function core_sitemaps_get_max_urls( $type = '' ) {
	/**
	 * Filter the maximum number of URLs displayed on a sitemap.
	 *
	 * @since 0.1.0
	 *
	 * @param int    $max_urls The maximum number of URLs included in a sitemap. Default 2000.
	 * @param string $type     Optional. The type of sitemap to be filtered. Default ''.
	 * @return int The maximum number of URLs.
	 */
	return apply_filters( 'core_sitemaps_max_urls', CORE_SITEMAPS_MAX_URLS, $type );
}
