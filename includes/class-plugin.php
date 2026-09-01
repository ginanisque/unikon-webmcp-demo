<?php

namespace Ginani\UnikonWebMCPDemo;

defined( 'ABSPATH' ) || exit;

final class Plugin {
	const PAGE_OPTION = 'unikon_webmcp_demo_page_id';
	const SHORTCODE   = 'unikon_webmcp_demo';

	/** @var Plugin|null */
	private static $instance;

	/** @var Progress */
	private $progress;

	/** @return Plugin */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		$this->progress = new Progress();
	}

	public function run() {
		add_action( 'rest_api_init', array( new Rest_Controller( $this->progress ), 'register_routes' ) );
		add_action( 'wp_enqueue_scripts', array( new Assets(), 'register' ) );
		add_shortcode( self::SHORTCODE, array( $this, 'render_shortcode' ) );
	}

	public static function activate() {
		$page_id = (int) get_option( self::PAGE_OPTION );
		if ( $page_id && 'trash' !== get_post_status( $page_id ) ) {
			return;
		}

		$existing = get_page_by_path( 'fashion-learning-studio', OBJECT, 'page' );
		if ( $existing instanceof \WP_Post ) {
			update_option( self::PAGE_OPTION, (int) $existing->ID, false );
			return;
		}

		$page_id = wp_insert_post(
			array(
				'post_title'   => __( 'Fashion Learning Studio', 'unikon-webmcp-demo' ),
				'post_name'    => 'fashion-learning-studio',
				'post_content' => '[' . self::SHORTCODE . ']',
				'post_status'  => 'publish',
				'post_type'    => 'page',
			),
			true
		);

		if ( ! is_wp_error( $page_id ) ) {
			update_option( self::PAGE_OPTION, (int) $page_id, false );
		}
	}

	/** @return string */
	public function render_shortcode() {
		Assets::enqueue();
		$course = Content::course();
		$state  = is_user_logged_in() ? $this->progress->get( get_current_user_id() ) : null;
		$summary = $state ? $this->progress->summary( $state ) : null;

		ob_start();
		include UNIKON_WEBMCP_DEMO_DIR . 'public/partials/learning-app.php';
		return (string) ob_get_clean();
	}
}

