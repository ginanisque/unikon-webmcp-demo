<?php

namespace Ginani\UnikonWebMCPDemo;

defined( 'ABSPATH' ) || exit;

final class Content {
	const COURSE_ID   = 'fashion-foundations';
	const DESIGN_COURSE_ID = 'fashion-design-studio';
	const LESSON_ID   = 'first-a-line-skirt';
	const EXERCISE_ID = 'fabric-choice';

	/**
	 * Return the complete, immutable demo curriculum.
	 *
	 * @return array<string,mixed>
	 */
	public static function course( $course_id = self::COURSE_ID ) {
		$courses = self::courses();
		return isset( $courses[ $course_id ] ) ? $courses[ $course_id ] : $courses[ self::COURSE_ID ];
	}

	/** @return array<string,array<string,mixed>> */
	public static function courses() {
		return array(
			self::COURSE_ID => array(
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
			),
			self::DESIGN_COURSE_ID => array(
				'id'          => self::DESIGN_COURSE_ID,
				'title'       => __( 'Fashion Design Studio: Concept to Collection', 'unikon-webmcp-demo' ),
				'description' => __( 'Turn an observation into a coherent fashion concept using colour, silhouette, and a focused mood board.', 'unikon-webmcp-demo' ),
				'lesson'      => array(
					'id'        => 'concept-to-collection',
					'title'     => __( 'Building a Clear Design Direction', 'unikon-webmcp-demo' ),
					'objective' => __( 'Translate one source of inspiration into a concise design direction with intentional visual choices.', 'unikon-webmcp-demo' ),
					'body'      => array(
						__( 'Strong fashion concepts begin with a specific observation rather than a broad trend. Notice a repeated line, texture, colour relationship, or movement that can guide design decisions.', 'unikon-webmcp-demo' ),
						__( 'Reduce the idea to three anchors: a silhouette direction, a limited colour story, and one material quality. These constraints help separate a collection concept from a collection of unrelated references.', 'unikon-webmcp-demo' ),
						__( 'A useful mood board supports decisions. Every image should clarify shape, atmosphere, surface, or colour; remove attractive images that do not serve that purpose.', 'unikon-webmcp-demo' ),
					),
				),
				'exercise'    => array(
					'id'       => 'design-direction',
					'title'    => __( 'Design direction edit', 'unikon-webmcp-demo' ),
					'prompt'   => __( 'Choose the strongest direction for a small collection inspired by coastal wind, then explain how its shape, palette, or material quality keeps the idea coherent.', 'unikon-webmcp-demo' ),
					'choices'  => array(
						'coastal-movement' => __( 'Flowing layers, sand and deep-blue palette, lightweight textured cloth', 'unikon-webmcp-demo' ),
						'mixed-trends' => __( 'Neon tailoring, floral sportswear, metallic eveningwear, and denim basics', 'unikon-webmcp-demo' ),
						'logo-study' => __( 'A board made only from fashion-brand logos', 'unikon-webmcp-demo' ),
					),
					'max_reason_length' => 280,
				),
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
	public static function evaluate( $answer_id, $reason, $course_id = self::COURSE_ID ) {
		$normalized       = strtolower( remove_accents( $reason ) );
		if ( self::DESIGN_COURSE_ID === $course_id ) {
			$mentions_quality = (bool) preg_match( '/\b(coherent|cohesive|flow|flowing|movement|shape|silhouette|palette|colour|color|material|texture|lightweight|coastal|wind)\b/', $normalized );
			$correct_choice   = 'coastal-movement' === $answer_id;
		} else {
			$mentions_quality = (bool) preg_match( '/\b(stable|stability|weight|light|medium|drape|structure|structured|easy|beginner|silhouette|sew|cut|press)\b/', $normalized );
			$correct_choice   = 'cotton-poplin' === $answer_id;
		}
		$passed           = $correct_choice && $mentions_quality;

		if ( $passed ) {
			$code     = 'passed';
			$feedback = self::DESIGN_COURSE_ID === $course_id
				? __( 'Well edited. The flowing shapes, restrained coastal palette, and lightweight texture reinforce one coherent source of inspiration.', 'unikon-webmcp-demo' )
				: __( 'Well reasoned. Cotton poplin is stable and light-to-medium in weight, so it is manageable for a beginner and holds a clear A-line shape.', 'unikon-webmcp-demo' );
		} elseif ( ! $correct_choice ) {
			$code     = self::DESIGN_COURSE_ID === $course_id ? 'review_direction' : 'review_fabric';
			$feedback = self::DESIGN_COURSE_ID === $course_id
				? __( 'Review how a focused source, limited palette, related shapes, and material qualities create a coherent design direction.', 'unikon-webmcp-demo' )
				: __( 'Review how stability and weight affect a beginner project. Look for a fabric that is easy to cut and sew while holding the A-line shape.', 'unikon-webmcp-demo' );
		} else {
			$code     = 'expand_reason';
			$feedback = self::DESIGN_COURSE_ID === $course_id
				? __( 'That direction is focused. Add how its silhouette, palette, or material quality reinforces the coastal-wind concept.', 'unikon-webmcp-demo' )
				: __( 'Cotton poplin is a strong choice. Add how its stability, manageable weight, or controlled drape supports the silhouette.', 'unikon-webmcp-demo' );
		}

		return array(
			'passed'        => $passed,
			'feedback_code' => $code,
			'feedback'      => $feedback,
		);
	}
}
