<?php
/**
 * Plugin Name: Unikon WebMCP Fashion eSchool Demo
 * Description: A standalone agent-assisted fashion learning demo for WordPress and WebMCP.
 * Version: 0.5.0
 * Requires at least: 6.4
 * Requires PHP: 7.4
 * Author: Ginani
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: unikon-webmcp-demo
 */

defined( 'ABSPATH' ) || exit;

define( 'UNIKON_WEBMCP_DEMO_VERSION', '0.5.0' );
define( 'UNIKON_WEBMCP_DEMO_FILE', __FILE__ );
define( 'UNIKON_WEBMCP_DEMO_DIR', plugin_dir_path( __FILE__ ) );
define( 'UNIKON_WEBMCP_DEMO_URL', plugin_dir_url( __FILE__ ) );

require_once UNIKON_WEBMCP_DEMO_DIR . 'includes/class-content.php';
require_once UNIKON_WEBMCP_DEMO_DIR . 'includes/class-progress.php';
require_once UNIKON_WEBMCP_DEMO_DIR . 'includes/class-video-settings.php';
require_once UNIKON_WEBMCP_DEMO_DIR . 'includes/class-assets.php';
require_once UNIKON_WEBMCP_DEMO_DIR . 'includes/class-rest-controller.php';
require_once UNIKON_WEBMCP_DEMO_DIR . 'includes/class-plugin.php';

register_activation_hook( __FILE__, array( 'Ginani\\UnikonWebMCPDemo\\Plugin', 'activate' ) );

Ginani\UnikonWebMCPDemo\Plugin::instance()->run();
