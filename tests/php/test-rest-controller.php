<?php

use Ginani\UnikonWebMCPDemo\Progress;
use Ginani\UnikonWebMCPDemo\Rest_Controller;

final class Unikon_WebMCP_REST_Test extends WP_UnitTestCase {
	/** @var WP_REST_Server */
	private $server;

	public function set_up() {
		parent::set_up();
		global $wp_rest_server;
		$wp_rest_server = new WP_REST_Server();
		$this->server   = $wp_rest_server;
		do_action( 'rest_api_init' );
	}

	private function request( $method, $route, $body = null, $nonce = null ) {
		$request = new WP_REST_Request( $method, '/unikon-webmcp-demo/v1/' . $route );
		if ( null !== $nonce ) $request->set_header( 'X-WP-Nonce', $nonce );
		if ( null !== $body ) $request->set_body( wp_json_encode( $body ) );
		$request->set_header( 'Content-Type', 'application/json' );
		return $this->server->dispatch( $request );
	}

	public function test_signed_out_and_invalid_nonce_are_rejected() {
		$this->assertSame( 401, $this->request( 'GET', 'state' )->get_status() );
		wp_set_current_user( self::factory()->user->create() );
		$this->assertSame( 403, $this->request( 'GET', 'state', null, 'invalid' )->get_status() );
	}

	public function test_malformed_answer_is_rejected() {
		$user = self::factory()->user->create();
		wp_set_current_user( $user );
		$nonce = wp_create_nonce( 'wp_rest' );
		$response = $this->request( 'POST', 'exercise/submit', array( 'activity_id' => 'unknown', 'answer_id' => 'unknown', 'reason' => 'short', 'extra' => true ), $nonce );
		$this->assertSame( 400, $response->get_status() );
	}

	public function test_confirmed_submission_succeeds() {
		$user = self::factory()->user->create();
		wp_set_current_user( $user );
		$nonce = wp_create_nonce( 'wp_rest' );
		$this->request( 'POST', 'lesson/open', array(), $nonce );
		$this->request( 'POST', 'exercise/start', array(), $nonce );
		$response = $this->request( 'POST', 'exercise/submit', array(
			'activity_id' => 'fabric-choice',
			'answer_id' => 'cotton-poplin',
			'reason'    => 'Its stable medium weight holds a clear silhouette.',
		), $nonce );
		$this->assertSame( 200, $response->get_status() );
		$this->assertTrue( $response->get_data()['evaluation']['passed'] );
	}
}
