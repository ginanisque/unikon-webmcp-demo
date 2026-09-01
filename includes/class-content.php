<?php

namespace Ginani\UnikonWebMCPDemo;

defined( 'ABSPATH' ) || exit;

final class Content {
	const COURSE_ID   = 'fashion-foundations';
	const LESSON_ID   = 'first-a-line-skirt';
	const EXERCISE_ID = 'fabric-choice';

	/**
	 * Return the complete, immutable demo curriculum.
	 *
	 * @return array<string,mixed>
	 */
	public static function course() {
		return array(
			'id'          => self::COURSE_ID,
			'title'       => __( 'Fashion Foundations: Fabric to Silhouette', 'unikon-webmcp-demo' ),
			'description' => __( 'A focused introduction to matching fabric behaviour with a garment shape.', 'unikon-webmcp-demo' ),
			'lesson'      => array(
				'id'        => self::LESSON_ID,
				'title'     => __( 'Choosing Fabric for a First A-Line Skirt', 'unikon-webmcp-demo' ),
				'objective' => __( 'Identify how weight, drape, and stability influence a clear A-line silhouette.', 'unikon-webmcp-demo' ),
				'body'      => array(
					__( 'An A-line skirt is fitted near the waist and widens gradually toward the hem. Fabric behaviour determines whether that line looks crisp, fluid, or bulky.', 'unikon-webmcp-demo' ),
					__( 'For a first version, a stable light-to-medium woven fabric is easier to measure, cut, press, and sew than a slippery or highly elastic fabric.', 'unikon-webmcp-demo' ),
					__( 'Cotton poplin has enough structure to show the silhouette while remaining light enough to avoid a stiff, heavy result.', 'unikon-webmcp-demo' ),
				),
			),
			'exercise'    => array(
				'id'       => self::EXERCISE_ID,
				'title'    => __( 'Fabric choice studio', 'unikon-webmcp-demo' ),
				'prompt'   => __( 'Choose the most beginner-friendly fabric for a first A-line skirt, then explain how its stability, weight, or drape supports the silhouette.', 'unikon-webmcp-demo' ),
				'choices'  => array(
					'cotton-poplin' => __( 'Cotton poplin', 'unikon-webmcp-demo' ),
					'silk-charmeuse' => __( 'Silk charmeuse', 'unikon-webmcp-demo' ),
					'heavy-denim' => __( 'Heavy denim', 'unikon-webmcp-demo' ),
				),
				'max_reason_length' => 280,
			),
		);
	}

	/**
	 * Evaluate an answer using a small, deterministic rubric.
	 *
	 * @param string $answer_id Answer identifier.
	 * @param string $reason Learner reason.
	 * @return array<string,mixed>
	 */
	public static function evaluate( $answer_id, $reason ) {
		$normalized       = strtolower( remove_accents( $reason ) );
		$mentions_quality = (bool) preg_match( '/\b(stable|stability|weight|light|medium|drape|structure|structured|easy|beginner|silhouette|sew|cut|press)\b/', $normalized );
		$correct_choice   = 'cotton-poplin' === $answer_id;
		$passed           = $correct_choice && $mentions_quality;

		if ( $passed ) {
			$code     = 'passed';
			$feedback = __( 'Well reasoned. Cotton poplin is stable and light-to-medium in weight, so it is manageable for a beginner and holds a clear A-line shape.', 'unikon-webmcp-demo' );
		} elseif ( ! $correct_choice ) {
			$code     = 'review_fabric';
			$feedback = __( 'Review how stability and weight affect a beginner project. Look for a fabric that is easy to cut and sew while holding the A-line shape.', 'unikon-webmcp-demo' );
		} else {
			$code     = 'expand_reason';
			$feedback = __( 'Cotton poplin is a strong choice. Add how its stability, manageable weight, or controlled drape supports the silhouette.', 'unikon-webmcp-demo' );
		}

		return array(
			'passed'        => $passed,
			'feedback_code' => $code,
			'feedback'      => $feedback,
		);
	}
}

