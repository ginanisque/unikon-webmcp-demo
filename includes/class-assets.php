<?php

namespace Ginani\UnikonWebMCPDemo;

defined( 'ABSPATH' ) || exit;

final class Assets {
	public function register() {
		wp_register_style( 'unikon-webmcp-demo', UNIKON_WEBMCP_DEMO_URL . 'public/css/learning-app.css', array(), UNIKON_WEBMCP_DEMO_VERSION );
		wp_register_script( 'unikon-webmcp-demo-app', UNIKON_WEBMCP_DEMO_URL . 'public/js/learning-app.js', array(), UNIKON_WEBMCP_DEMO_VERSION, true );
		wp_register_script( 'unikon-webmcp-demo-tools', UNIKON_WEBMCP_DEMO_URL . 'public/js/webmcp-tools.js', array( 'unikon-webmcp-demo-app' ), UNIKON_WEBMCP_DEMO_VERSION, true );
	}

	public static function enqueue() {
		wp_enqueue_style( 'unikon-webmcp-demo' );
		wp_enqueue_script( 'unikon-webmcp-demo-app' );
		wp_enqueue_script( 'unikon-webmcp-demo-tools' );
		wp_localize_script(
			'unikon-webmcp-demo-app',
			'UnikonWebMCPDemo',
			array(
				'root'          => esc_url_raw( rest_url( 'unikon-webmcp-demo/v1/' ) ),
				'nonce'         => wp_create_nonce( 'wp_rest' ),
				'authenticated' => is_user_logged_in(),
			)
		);
	}
}

