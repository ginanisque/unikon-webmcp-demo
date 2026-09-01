<?php

namespace Ginani\UnikonWebMCPDemo;

defined( 'ABSPATH' ) || exit;

final class Rest_Controller {
	const NAMESPACE = 'unikon-webmcp-demo/v1';

	/** @var Progress */
	private $progress;

	public function __construct( Progress $progress ) {
		$this->progress = $progress;
	}

	public function register_routes() {
		$read_args = array(
			'methods'             => \WP_REST_Server::READABLE,
			'permission_callback' => array( $this, 'permissions_check' ),
		);

		register_rest_route( self::NAMESPACE, '/state', array( $read_args + array( 'callback' => array( $this, 'get_state' ) ) ) );
		register_rest_route(
			self::NAMESPACE,
			'/lesson/open',
			array(
				'methods'             => \WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'open_lesson' ),
				'permission_callback' => array( $this, 'permissions_check' ),
			)
		);
		register_rest_route(
			self::NAMESPACE,
			'/exercise/start',
			array(
				'methods'             => \WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'start_exercise' ),
				'permission_callback' => array( $this, 'permissions_check' ),
			)
		);
		register_rest_route(
			self::NAMESPACE,
			'/exercise/submit',
			array(
				'methods'             => \WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'submit_exercise' ),
				'permission_callback' => array( $this, 'permissions_check' ),
				'args'                => array(
					'answer_id' => array(
						'required'          => true,
						'type'              => 'string',
						'enum'              => array( 'cotton-poplin', 'silk-charmeuse', 'heavy-denim', 'coastal-movement', 'mixed-trends', 'logo-study' ),
						'sanitize_callback' => 'sanitize_key',
					),
					'reason'    => array(
						'required'          => true,
						'type'              => 'string',
						'minLength'         => 12,
						'maxLength'         => 280,
						'sanitize_callback' => 'sanitize_textarea_field',
					),
				),
			)
		);
		register_rest_route( self::NAMESPACE, '/progress', array( $read_args + array( 'callback' => array( $this, 'get_progress' ) ) ) );
	}

	/** @return true|\WP_Error */
	public function permissions_check( \WP_REST_Request $request ) {
		if ( ! is_user_logged_in() ) {
			return new \WP_Error( 'authentication_required', __( 'Sign in to access your learning progress.', 'unikon-webmcp-demo' ), array( 'status' => 401 ) );
		}

		$nonce = $request->get_header( 'X-WP-Nonce' );
		if ( ! $nonce || ! wp_verify_nonce( $nonce, 'wp_rest' ) ) {
			return new \WP_Error( 'invalid_nonce', __( 'Your session could not be verified. Refresh the page and try again.', 'unikon-webmcp-demo' ), array( 'status' => 403 ) );
		}

		return true;
	}

	/** @return \WP_REST_Response */
	public function get_state() {
		$course_id = $this->course_id();
		return rest_ensure_response( $this->payload( $this->progress->get( get_current_user_id(), $course_id ), $course_id ) );
	}

	/** @return \WP_REST_Response */
	public function open_lesson() {
		$course_id = $this->course_id();
		return rest_ensure_response( $this->payload( $this->progress->open_lesson( get_current_user_id(), $course_id ), $course_id ) );
	}

	/** @return \WP_REST_Response|\WP_Error */
	public function start_exercise() {
		$course_id = $this->course_id();
		$state = $this->progress->start_exercise( get_current_user_id(), $course_id );
		return is_wp_error( $state ) ? $state : rest_ensure_response( $this->payload( $state, $course_id ) );
	}

	/** @return \WP_REST_Response|\WP_Error */
	public function submit_exercise( \WP_REST_Request $request ) {
		$body    = $request->get_json_params();
		$allowed = array( 'answer_id', 'reason' );
		if ( ! is_array( $body ) || array_diff( array_keys( $body ), $allowed ) ) {
			return new \WP_Error( 'invalid_parameters', __( 'The answer contains unsupported fields.', 'unikon-webmcp-demo' ), array( 'status' => 400 ) );
		}

		$course_id = $this->course_id();
		$result = $this->progress->submit( get_current_user_id(), $request['answer_id'], $request['reason'], $course_id );
		if ( is_wp_error( $result ) ) {
			return $result;
		}

		return rest_ensure_response(
			array(
				'state'      => $this->payload( $result['state'], $course_id ),
				'evaluation' => $result['evaluation'],
			)
		);
	}

	/** @return \WP_REST_Response */
	public function get_progress() {
		$state = $this->progress->get( get_current_user_id(), $this->course_id() );
		return rest_ensure_response( $this->progress->summary( $state ) );
	}

	/** @return array<string,mixed> */
	private function payload( $state, $course_id ) {
		$course = Content::course( $course_id );
		return array(
			'course'          => array( 'id' => $course['id'], 'title' => $course['title'] ),
			'lesson'          => array( 'id' => $course['lesson']['id'], 'title' => $course['lesson']['title'], 'status' => $state['lesson_status'] ),
			'exercise'        => array( 'id' => $course['exercise']['id'], 'title' => $course['exercise']['title'], 'status' => $state['exercise_status'], 'attempt_count' => $state['attempt_count'] ),
			'allowed_actions' => $this->allowed_actions( $state ),
			'progress'        => $this->progress->summary( $state ),
		);
	}

	private function course_id() {
		$course_id = sanitize_key( isset( $_SERVER['HTTP_X_UNIKON_COURSE'] ) ? wp_unslash( $_SERVER['HTTP_X_UNIKON_COURSE'] ) : '' );
		return isset( Content::courses()[ $course_id ] ) ? $course_id : Content::COURSE_ID;
	}

	/** @return string[] */
	private function allowed_actions( $state ) {
		if ( 'completed' === $state['exercise_status'] ) {
			return array( 'get_learning_state', 'get_progress_and_next_step' );
		}
		if ( 'in_progress' === $state['exercise_status'] ) {
			return array( 'get_learning_state', 'stage_exercise_answer', 'get_progress_and_next_step' );
		}
		if ( 'not_started' !== $state['lesson_status'] ) {
			return array( 'get_learning_state', 'open_next_lesson', 'start_exercise', 'get_progress_and_next_step' );
		}
		return array( 'get_learning_state', 'open_next_lesson', 'get_progress_and_next_step' );
	}
}
