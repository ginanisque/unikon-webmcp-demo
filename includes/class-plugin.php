<?php

namespace Ginani\UnikonWebMCPDemo;

defined( 'ABSPATH' ) || exit;

final class Plugin {
	const PAGE_OPTION = 'unikon_webmcp_demo_page_id';
	const DESIGN_PAGE_OPTION = 'unikon_webmcp_demo_design_page_id';
	const SEWING_PAGE_OPTION = 'unikon_webmcp_demo_sewing_page_id';
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
		( new Video_Settings() )->run();
		add_action( 'rest_api_init', array( new Rest_Controller( $this->progress ), 'register_routes' ) );
		add_action( 'wp_enqueue_scripts', array( new Assets(), 'register' ) );
		add_action( 'admin_init', array( __CLASS__, 'maybe_ensure_pages' ) );
		add_shortcode( self::SHORTCODE, array( $this, 'render_shortcode' ) );
	}

	public static function maybe_ensure_pages() {
		if ( current_user_can( 'manage_options' ) && ( ! get_option( self::DESIGN_PAGE_OPTION ) || ! get_option( self::SEWING_PAGE_OPTION ) ) ) self::activate();
	}

	public static function activate() {
		self::ensure_page( self::PAGE_OPTION, 'fashion-learning-studio', __( 'Fashion Learning Studio', 'unikon-webmcp-demo' ), '[' . self::SHORTCODE . ']' );
		self::ensure_page( self::DESIGN_PAGE_OPTION, 'fashion-design-studio', __( 'Fashion Design Studio', 'unikon-webmcp-demo' ), '[' . self::SHORTCODE . ' course="' . Content::DESIGN_COURSE_ID . '"]' );
		self::ensure_page( self::SEWING_PAGE_OPTION, 'sewing-video-class', __( 'Sewing Video Class', 'unikon-webmcp-demo' ), '[' . self::SHORTCODE . ' course="' . Content::SEWING_COURSE_ID . '"]' );
	}

	private static function ensure_page( $option, $slug, $title, $shortcode ) {
		$page_id = (int) get_option( $option );
		if ( $page_id && 'trash' !== get_post_status( $page_id ) ) return;
		$existing = get_page_by_path( $slug, OBJECT, 'page' );
		if ( $existing instanceof \WP_Post ) {
			update_option( $option, (int) $existing->ID, false );
			return;
		}
		$page_id = wp_insert_post( array( 'post_title' => $title, 'post_name' => $slug, 'post_content' => $shortcode, 'post_status' => 'publish', 'post_type' => 'page' ), true );
		if ( ! is_wp_error( $page_id ) ) update_option( $option, (int) $page_id, false );
	}

	/** @return string */
	public function render_shortcode( $attributes = array() ) {
		Assets::enqueue();
		$attributes = shortcode_atts( array( 'course' => Content::COURSE_ID ), $attributes, self::SHORTCODE );
		$course_id = sanitize_key( $attributes['course'] );
		if ( ! isset( Content::courses()[ $course_id ] ) ) $course_id = Content::COURSE_ID;
		$course = Content::course( $course_id );
		$assessments = Content::assessments( $course_id );
		$video_urls = Content::SEWING_COURSE_ID === $course_id ? Video_Settings::urls() : array();
		$state  = is_user_logged_in() ? $this->progress->get( get_current_user_id(), $course_id ) : null;
		$summary = $state ? $this->progress->summary( $state, $course_id ) : null;
		$course_links = array();
		foreach ( array( Content::COURSE_ID => self::PAGE_OPTION, Content::DESIGN_COURSE_ID => self::DESIGN_PAGE_OPTION, Content::SEWING_COURSE_ID => self::SEWING_PAGE_OPTION ) as $id => $option ) {
			$page_id = (int) get_option( $option );
			if ( $page_id ) $course_links[ $id ] = get_permalink( $page_id );
		}

		ob_start();
		include UNIKON_WEBMCP_DEMO_DIR . 'public/partials/learning-app.php';
		return (string) ob_get_clean();
	}
}
