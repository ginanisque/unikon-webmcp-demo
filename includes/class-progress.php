<?php

namespace Ginani\UnikonWebMCPDemo;

defined( 'ABSPATH' ) || exit;

final class Progress {
	const META_KEY = 'unikon_webmcp_progress_v1';

	/** @return array<string,mixed> */
	public function defaults() {
		return array(
			'version'         => 2,
			'lesson_status'   => 'not_started',
			'exercise_status' => 'not_started',
			'attempt_count'   => 0,
			'selected_answer' => null,
			'feedback_code'   => null,
			'activity_statuses' => array(),
			'submissions'       => array(),
			'updated_at'      => null,
		);
	}

	/** @return array<string,mixed> */
	public function get( $user_id, $course_id = Content::COURSE_ID ) {
		$stored = get_user_meta( (int) $user_id, $this->meta_key( $course_id ), true );
		if ( ! is_array( $stored ) ) {
			return $this->defaults();
		}

		$state = $this->normalize( wp_parse_args( $stored, $this->defaults() ) );
		if ( empty( $state['activity_statuses'] ) && 'completed' === $state['exercise_status'] ) {
			foreach ( Content::assessments( $course_id ) as $assessment ) $state['activity_statuses'][ $assessment['id'] ] = 'completed';
		}
		return $state;
	}

	/** @return array<string,mixed> */
	public function open_lesson( $user_id, $course_id = Content::COURSE_ID ) {
		$state = $this->get( $user_id, $course_id );
		if ( 'not_started' === $state['lesson_status'] ) {
			$state['lesson_status'] = 'in_progress';
			$this->save( $user_id, $state, $course_id );
		}
		return $state;
	}

	/** @return array<string,mixed>|\WP_Error */
	public function start_exercise( $user_id, $course_id = Content::COURSE_ID ) {
		$state = $this->get( $user_id, $course_id );
		if ( 'not_started' === $state['lesson_status'] ) {
			return new \WP_Error( 'invalid_state', __( 'Open the lesson before starting its exercise.', 'unikon-webmcp-demo' ), array( 'status' => 409 ) );
		}

		if ( 'not_started' === $state['exercise_status'] ) {
			$state['lesson_status']   = 'completed';
			$state['exercise_status'] = 'in_progress';
			$assessments = Content::assessments( $course_id );
			if ( $assessments ) $state['activity_statuses'][ $assessments[0]['id'] ] = 'in_progress';
			$this->save( $user_id, $state, $course_id );
		} elseif ( 'in_progress' === $state['exercise_status'] && empty( $state['activity_statuses'] ) ) {
			$assessments = Content::assessments( $course_id );
			if ( $assessments ) $state['activity_statuses'][ $assessments[0]['id'] ] = 'in_progress';
			$this->save( $user_id, $state, $course_id );
		}
		return $state;
	}

	/** @return array<string,mixed>|\WP_Error */
	public function submit( $user_id, $activity_id, $answer_id, $reason, $course_id = Content::COURSE_ID ) {
		$state = $this->get( $user_id, $course_id );
		if ( 'in_progress' !== $state['exercise_status'] ) {
			return new \WP_Error( 'invalid_state', __( 'Start the exercise before submitting an answer.', 'unikon-webmcp-demo' ), array( 'status' => 409 ) );
		}

		$assessments = Content::assessments( $course_id );
		$index = null;
		foreach ( $assessments as $position => $assessment ) if ( $assessment['id'] === $activity_id ) $index = $position;
		if ( null === $index || 'in_progress' !== ( $state['activity_statuses'][ $activity_id ] ?? 'locked' ) ) {
			return new \WP_Error( 'invalid_state', __( 'Complete earlier assessment layers before submitting this one.', 'unikon-webmcp-demo' ), array( 'status' => 409 ) );
		}

		$result = Content::evaluate_assessment( $course_id, $activity_id, $answer_id, $reason );
		if ( is_wp_error( $result ) ) return $result;
		$state['attempt_count']   = (int) $state['attempt_count'] + 1;
		$state['selected_answer'] = $answer_id;
		$state['feedback_code']   = $result['feedback_code'];
		$state['submissions'][]   = array(
			'activity_id' => $activity_id,
			'attempt' => 1 + count( array_filter( $state['submissions'], static function ( $submission ) use ( $activity_id ) { return $submission['activity_id'] === $activity_id; } ) ),
			'answer_id' => $answer_id ?: null,
			'response' => $reason,
			'feedback_code' => $result['feedback_code'],
			'passed' => (bool) $result['passed'],
			'submitted_at' => gmdate( 'c' ),
		);
		$state['submissions'] = array_slice( $state['submissions'], -30 );
		if ( $result['passed'] ) {
			$state['activity_statuses'][ $activity_id ] = 'completed';
			if ( isset( $assessments[ $index + 1 ] ) ) {
				$state['activity_statuses'][ $assessments[ $index + 1 ]['id'] ] = 'in_progress';
			} else {
				$state['exercise_status'] = 'completed';
				$state['lesson_status']   = 'completed';
			}
		}
		$this->save( $user_id, $state, $course_id );

		return array(
			'state'      => $state,
			'evaluation' => $result,
		);
	}

	/** @return array<string,mixed> */
	public function summary( $state, $course_id = Content::COURSE_ID ) {
		if ( 'completed' === $state['exercise_status'] ) {
			$percent = 100;
			$next    = array( 'action' => 'complete', 'label' => __( 'Course complete—review what you learned.', 'unikon-webmcp-demo' ) );
		} elseif ( 'in_progress' === $state['exercise_status'] ) {
			$completed = count( array_filter( $state['activity_statuses'], static function ( $status ) { return 'completed' === $status; } ) );
			$total = max( 1, count( Content::assessments( $course_id ) ) );
			$percent = min( 95, 40 + (int) floor( 55 * $completed / $total ) );
			$next    = array( 'action' => 'submit_answer', 'label' => __( 'Complete the current exercise.', 'unikon-webmcp-demo' ) );
		} elseif ( 'not_started' !== $state['lesson_status'] ) {
			$percent = 35;
			$next    = array( 'action' => 'start_exercise', 'label' => __( 'Start the current course exercise.', 'unikon-webmcp-demo' ) );
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

	private function save( $user_id, &$state, $course_id ) {
		$state['updated_at'] = gmdate( 'c' );
		update_user_meta( (int) $user_id, $this->meta_key( $course_id ), $this->normalize( $state ) );
	}

	private function meta_key( $course_id ) {
		return Content::COURSE_ID === $course_id ? self::META_KEY : self::META_KEY . '_' . sanitize_key( $course_id );
	}

	/** @return array<string,mixed> */
	private function normalize( $state ) {
		$lesson_states   = array( 'not_started', 'in_progress', 'completed' );
		$exercise_states = array( 'not_started', 'in_progress', 'completed' );
		$defaults        = $this->defaults();

		$activity_statuses = array();
		if ( is_array( $state['activity_statuses'] ) ) foreach ( $state['activity_statuses'] as $id => $status ) {
			if ( in_array( $status, array( 'in_progress', 'completed' ), true ) ) $activity_statuses[ sanitize_key( $id ) ] = $status;
		}
		$submissions = array();
		if ( is_array( $state['submissions'] ) ) foreach ( array_slice( $state['submissions'], -30 ) as $submission ) {
			if ( ! is_array( $submission ) || empty( $submission['activity_id'] ) ) continue;
			$submissions[] = array(
				'activity_id' => sanitize_key( $submission['activity_id'] ), 'attempt' => max( 1, (int) $submission['attempt'] ),
				'answer_id' => empty( $submission['answer_id'] ) ? null : sanitize_key( $submission['answer_id'] ),
				'response' => sanitize_textarea_field( $submission['response'] ), 'feedback_code' => sanitize_key( $submission['feedback_code'] ),
				'passed' => ! empty( $submission['passed'] ), 'submitted_at' => sanitize_text_field( $submission['submitted_at'] ),
			);
		}

		return array(
			'version'         => 2,
			'lesson_status'   => in_array( $state['lesson_status'], $lesson_states, true ) ? $state['lesson_status'] : $defaults['lesson_status'],
			'exercise_status' => in_array( $state['exercise_status'], $exercise_states, true ) ? $state['exercise_status'] : $defaults['exercise_status'],
			'attempt_count'   => max( 0, (int) $state['attempt_count'] ),
			'selected_answer' => is_string( $state['selected_answer'] ) ? sanitize_key( $state['selected_answer'] ) : null,
			'feedback_code'   => is_string( $state['feedback_code'] ) ? sanitize_key( $state['feedback_code'] ) : null,
			'activity_statuses' => $activity_statuses,
			'submissions'       => $submissions,
			'updated_at'      => is_string( $state['updated_at'] ) ? sanitize_text_field( $state['updated_at'] ) : null,
		);
	}
}
