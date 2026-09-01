<?php
/**
 * Theme setup for Unikon WebMCP Studio.
 *
 * @package UnikonWebMCPTheme
 */

defined( 'ABSPATH' ) || exit;

/** Enqueue the small theme stylesheet. */
function unikon_webmcp_theme_enqueue_assets() {
	wp_enqueue_style(
		'unikon-webmcp-theme',
		get_stylesheet_uri(),
		array(),
		wp_get_theme()->get( 'Version' )
	);
}
add_action( 'wp_enqueue_scripts', 'unikon_webmcp_theme_enqueue_assets' );

/**
 * Focus the site on the generated learning page once when the theme is switched.
 * If the plugin has not created the page yet, admin_init retries until it exists.
 */
function unikon_webmcp_theme_schedule_front_page() {
	update_option( 'unikon_webmcp_theme_needs_front_page', 1, false );
	unikon_webmcp_theme_focus_front_page();
}
add_action( 'after_switch_theme', 'unikon_webmcp_theme_schedule_front_page' );

/** Set the known demo page as the static homepage without creating content. */
function unikon_webmcp_theme_focus_front_page() {
	if ( ! get_option( 'unikon_webmcp_theme_needs_front_page' ) || ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$page = get_page_by_path( 'fashion-learning-studio', OBJECT, 'page' );
	if ( ! $page instanceof WP_Post || 'publish' !== $page->post_status ) {
		return;
	}

	update_option( 'show_on_front', 'page' );
	update_option( 'page_on_front', (int) $page->ID );
	delete_option( 'unikon_webmcp_theme_needs_front_page' );
}
add_action( 'admin_init', 'unikon_webmcp_theme_focus_front_page' );

/** Explain what is needed if the learning plugin is not active yet. */
function unikon_webmcp_theme_admin_notice() {
	if ( ! current_user_can( 'manage_options' ) || ! get_option( 'unikon_webmcp_theme_needs_front_page' ) ) {
		return;
	}
	?>
	<div class="notice notice-info"><p>
		<?php esc_html_e( 'Unikon WebMCP Studio is ready. Activate the Unikon WebMCP Fashion eSchool Demo plugin to create and focus the learning homepage.', 'unikon-webmcp-theme' ); ?>
	</p></div>
	<?php
}
add_action( 'admin_notices', 'unikon_webmcp_theme_admin_notice' );

