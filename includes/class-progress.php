<?php

namespace Ginani\UnikonWebMCPDemo;

defined( 'ABSPATH' ) || exit;

final class Progress {
	const META_KEY = 'unikon_webmcp_progress_v1';

	/** @return array<string,mixed> */
	public function defaults() {
		return array(
			'version'         => 1,
			'lesson_status'   => 'not_started',
			'exercise_status' => 'not_started',
			'attempt_count'   => 0,
			'selected_answer' => null,
			'feedback_code'   => null,
			'updated_at'      => null,
		);
	}

	/** @return array<string,mixed> */
	public function get( $user_id ) {
		$stored = get_user_meta( (int) $user_id, self::META_KEY, true );
		if ( ! is_array( $stored ) ) {
			return $this->defaults();
		}

		$state = wp_parse_args( $stored, $this->defaults() );
		return $this->normalize( $state );
	}

	/** @return array<string,mixed> */
	public function open_lesson( $user_id ) {
		$state = $this->get( $user_id );
		if ( 'not_started' === $state['lesson_status'] ) {
			$state['lesson_status'] = 'in_progress';
			$this->save( $user_id, $state );
		}
		return $state;
	}

	/** @return array<string,mixed>|\WP_Error */
	public function start_exercise( $user_id ) {
		$state = $this->get( $user_id );
		if ( 'not_started' === $state['lesson_status'] ) {
			return new \WP_Error( 'invalid_state', __( 'Open the lesson before starting its exercise.', 'unikon-webmcp-demo' ), array( 'status' => 409 ) );
		}

		if ( 'not_started' === $state['exercise_status'] ) {
			$state['lesson_status']   = 'completed';
			$state['exercise_status'] = 'in_progress';
			$this->save( $user_id, $state );
		}
		return $state;
	}

	/** @return array<string,mixed>|\WP_Error */
	public function submit( $user_id, $answer_id, $reason ) {
		$state = $this->get( $user_id );
		if ( 'in_progress' !== $state['exercise_status'] ) {
			return new \WP_Error( 'invalid_state', __( 'Start the exercise before submitting an answer.', 'unikon-webmcp-demo' ), array( 'status' => 409 ) );
		}

		$result                   = Content::evaluate( $answer_id, $reason );
		$state['attempt_count']   = (int) $state['attempt_count'] + 1;
		$state['selected_answer'] = $answer_id;
		$state['feedback_code']   = $result['feedback_code'];
		if ( $result['passed'] ) {
			$state['exercise_status'] = 'completed';
			$state['lesson_status']   = 'completed';
		}
		$this->save( $user_id, $state );

		return array(
			'state'      => $state,
			'evaluation' => $result,
		);
	}

	/** @return array<string,mixed> */
	public function summary( $state ) {
		if ( 'completed' === $state['exercise_status'] ) {
			$percent = 100;
			$next    = array( 'action' => 'complete', 'label' => __( 'Course complete—review what you learned.', 'unikon-webmcp-demo' ) );
		} elseif ( 'in_progress' === $state['exercise_status'] ) {
			$percent = 70;
			$next    = array( 'action' => 'submit_answer', 'label' => __( 'Complete the fabric choice exercise.', 'unikon-webmcp-demo' ) );
		} elseif ( 'not_started' !== $state['lesson_status'] ) {
			$percent = 35;
			$next    = array( 'action' => 'start_exercise', 'label' => __( 'Start the fabric choice exercise.', 'unikon-webmcp-demo' ) );
		} else {
			$percent = 0;
			$next    = array( 'action' => 'open_lesson', 'label' => __( 'Open your first lesson.', 'unikon-webmcp-demo' ) );
		}

		return array(
			'percent'              => $percent,
			'completed_milestones' => array_values( array_filter( array(
				'completed' === $state['lesson_status'] ? 'lesson' : null,
				'completed' === $state['exercise_status'] ? 'exercise' : null,
			) ) ),
			'next_step'            => $next,
		);
	}

	private function save( $user_id, &$state ) {
		$state['updated_at'] = gmdate( 'c' );
		update_user_meta( (int) $user_id, self::META_KEY, $this->normalize( $state ) );
	}

	/** @return array<string,mixed> */
	private function normalize( $state ) {
		$lesson_states   = array( 'not_started', 'in_progress', 'completed' );
		$exercise_states = array( 'not_started', 'in_progress', 'completed' );
		$defaults        = $this->defaults();

		return array(
			'version'         => 1,
			'lesson_status'   => in_array( $state['lesson_status'], $lesson_states, true ) ? $state['lesson_status'] : $defaults['lesson_status'],
			'exercise_status' => in_array( $state['exercise_status'], $exercise_states, true ) ? $state['exercise_status'] : $defaults['exercise_status'],
			'attempt_count'   => max( 0, (int) $state['attempt_count'] ),
			'selected_answer' => is_string( $state['selected_answer'] ) ? sanitize_key( $state['selected_answer'] ) : null,
			'feedback_code'   => is_string( $state['feedback_code'] ) ? sanitize_key( $state['feedback_code'] ) : null,
			'updated_at'      => is_string( $state['updated_at'] ) ? sanitize_text_field( $state['updated_at'] ) : null,
		);
	}
}

