<?php

namespace Ginani\UnikonWebMCPDemo;

defined( 'ABSPATH' ) || exit;

final class Content {
	const COURSE_ID   = 'fashion-foundations';
	const DESIGN_COURSE_ID = 'fashion-design-studio';
	const SEWING_COURSE_ID = 'sewing-video-class';
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
				'hero_image'  => array(
					'src' => 'public/images/courses/fashion-foundations-colour-wheel.png',
					'alt' => __( 'A labelled fashion colour wheel showing primary, secondary, and intermediate hues.', 'unikon-webmcp-demo' ),
				),
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
				'hero_image'  => array(
					'src' => 'public/images/courses/fashion-design-studio.jpg',
					'alt' => __( 'Fashion learners developing ideas with fabric, sketches, and a sewing machine.', 'unikon-webmcp-demo' ),
				),
				'lesson'      => array(
					'id'        => 'concept-to-collection',
					'title'     => __( 'Building a Clear Design Direction', 'unikon-webmcp-demo' ),
					'objective' => __( 'Translate one source of inspiration into a concise design direction with intentional visual choices.', 'unikon-webmcp-demo' ),
					'body'      => array(
						__( 'Strong fashion concepts begin with a specific observation rather than a broad trend. Notice a repeated line, texture, colour relationship, or movement that can guide design decisions.', 'unikon-webmcp-demo' ),
						__( 'Reduce the idea to three anchors: a silhouette direction, a limited colour story, and one material quality. These constraints help separate a collection concept from a collection of unrelated references.', 'unikon-webmcp-demo' ),
						__( 'A useful mood board supports decisions. Every image should clarify shape, atmosphere, surface, or colour; remove attractive images that do not serve that purpose.', 'unikon-webmcp-demo' ),
					),
					'images'    => array(
						array(
							'src' => 'public/images/courses/fashion-design-consultation.jpg',
							'alt' => __( 'A fashion educator discussing a design direction with a learner in a bright studio.', 'unikon-webmcp-demo' ),
							'caption' => __( 'Discussing and editing a design direction turns visual research into deliberate choices.', 'unikon-webmcp-demo' ),
						),
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
			self::SEWING_COURSE_ID => array(
				'id'          => self::SEWING_COURSE_ID,
				'title'       => __( 'Sewing Skills: Machine Control to Finishing', 'unikon-webmcp-demo' ),
				'description' => __( 'A guided Vimeo learning path through essential machine handling, stitching, closures, shaping, and finishing techniques.', 'unikon-webmcp-demo' ),
				'hero_image'  => array(
					'src' => 'public/images/courses/sewing-curved-lines.jpg',
					'alt' => __( 'A sewing learner guiding a curved practice piece through a machine.', 'unikon-webmcp-demo' ),
				),
				'lesson'      => array(
					'id'        => 'sewing-video-path',
					'title'     => __( 'How to Use the Sewing Video Path', 'unikon-webmcp-demo' ),
					'objective' => __( 'Practise one technique at a time, explain the key control point, and unlock the next topic.', 'unikon-webmcp-demo' ),
					'body'      => array(
						__( 'Watch each demonstration with your machine switched off when handling the needle area. Pause frequently and reproduce each setup before sewing.', 'unikon-webmcp-demo' ),
						__( 'After every video, submit a short observation about the technique. A response that identifies the important control point unlocks the next topic.', 'unikon-webmcp-demo' ),
						__( 'Video completion is learner-confirmed through the assessment; WebMCP can navigate and stage responses but cannot claim that a video was watched.', 'unikon-webmcp-demo' ),
					),
					'images'    => array(
						array(
							'src' => 'public/images/courses/sewing-machine-handling.jpg',
							'alt' => __( 'Hands guiding white practice fabric beneath an industrial sewing-machine presser foot.', 'unikon-webmcp-demo' ),
							'caption' => __( 'Machine control begins with hand position, a clear seam guide, and a steady pace.', 'unikon-webmcp-demo' ),
						),
						array(
							'src' => 'public/images/courses/sewing-zipper.jpg',
							'alt' => __( 'Hands stitching a dark zipper into white practice fabric.', 'unikon-webmcp-demo' ),
							'caption' => __( 'Later topics apply the same control principles to accurate zipper construction.', 'unikon-webmcp-demo' ),
						),
					),
				),
				'exercise'    => array(
					'id' => 'sewing-topic-checks', 'title' => __( 'Sewing practice journal', 'unikon-webmcp-demo' ),
					'prompt' => __( 'Work through the video topics in order and record the key technique from each.', 'unikon-webmcp-demo' ),
					'choices' => array(), 'max_reason_length' => 600,
				),
			),
		);
	}

	/** Return ordered assessments for a course. @return array<int,array<string,mixed>> */
	public static function assessments( $course_id ) {
		$course = self::course( $course_id );
		if ( self::SEWING_COURSE_ID === $course_id ) {
			return array_map( static function ( $topic ) {
				return array(
					'id' => $topic['id'], 'type' => $topic['type'], 'title' => $topic['title'], 'prompt' => $topic['prompt'],
					'choices' => array(), 'min_length' => $topic['min_length'], 'max_length' => $topic['max_length'],
					'correct' => null, 'keywords' => $topic['keywords'], 'video_topic' => true,
				);
			}, self::video_topics() );
		}
		if ( self::DESIGN_COURSE_ID !== $course_id ) {
			return array(
				array(
					'id' => self::EXERCISE_ID, 'type' => 'choice', 'title' => $course['exercise']['title'],
					'prompt' => $course['exercise']['prompt'], 'choices' => $course['exercise']['choices'],
					'min_length' => 12, 'max_length' => 280, 'correct' => 'cotton-poplin',
					'keywords' => array( 'stable', 'stability', 'weight', 'light', 'medium', 'drape', 'structure', 'beginner', 'silhouette', 'sew', 'cut', 'press' ),
				),
			);
		}

		return array(
			array(
				'id' => 'design-signal', 'type' => 'choice', 'title' => __( 'Layer 1: Find the design signal', 'unikon-webmcp-demo' ),
				'prompt' => __( 'Which observation gives the clearest starting signal for a coastal-wind collection?', 'unikon-webmcp-demo' ),
				'choices' => array( 'repeated-curves' => __( 'Repeated curved lines in wind-shaped dunes', 'unikon-webmcp-demo' ), 'everything-coastal' => __( 'Every image associated with a beach', 'unikon-webmcp-demo' ), 'current-trends' => __( 'A list of unrelated current trends', 'unikon-webmcp-demo' ) ),
				'min_length' => 12, 'max_length' => 280, 'correct' => 'repeated-curves', 'keywords' => array( 'line', 'curve', 'movement', 'repeat', 'specific', 'shape', 'wind' ),
			),
			array(
				'id' => 'colour-story', 'type' => 'choice', 'title' => __( 'Layer 2: Edit the colour story', 'unikon-webmcp-demo' ),
				'prompt' => __( 'Which palette best supports a coherent coastal-wind direction?', 'unikon-webmcp-demo' ),
				'choices' => array( 'sand-blue-foam' => __( 'Sand, deep blue, and foam white', 'unikon-webmcp-demo' ), 'rainbow' => __( 'Every hue at equal intensity', 'unikon-webmcp-demo' ), 'neon-metallic' => __( 'Neon pink, chrome, and bright orange', 'unikon-webmcp-demo' ) ),
				'min_length' => 12, 'max_length' => 280, 'correct' => 'sand-blue-foam', 'keywords' => array( 'palette', 'colour', 'color', 'coastal', 'limited', 'coherent', 'sand', 'blue', 'foam' ),
			),
			array(
				'id' => 'silhouette-analysis', 'type' => 'short_answer', 'title' => __( 'Layer 3: Silhouette analysis', 'unikon-webmcp-demo' ),
				'prompt' => __( 'In two or three sentences, describe how shape and movement could express coastal wind in a garment.', 'unikon-webmcp-demo' ),
				'choices' => array(), 'min_length' => 40, 'max_length' => 420, 'correct' => null, 'keywords' => array( 'silhouette', 'shape', 'line', 'layer', 'flow', 'movement', 'volume', 'drape', 'wind' ),
			),
			array(
				'id' => 'material-direction', 'type' => 'short_answer', 'title' => __( 'Layer 4: Material direction', 'unikon-webmcp-demo' ),
				'prompt' => __( 'Recommend a material quality for the concept and explain what it contributes.', 'unikon-webmcp-demo' ),
				'choices' => array(), 'min_length' => 40, 'max_length' => 420, 'correct' => null, 'keywords' => array( 'material', 'fabric', 'light', 'texture', 'drape', 'sheer', 'fluid', 'movement', 'structure' ),
			),
			array(
				'id' => 'moodboard-edit', 'type' => 'choice', 'title' => __( 'Layer 5: Mood-board edit', 'unikon-webmcp-demo' ),
				'prompt' => __( 'Which mood-board approach is ready to guide a collection?', 'unikon-webmcp-demo' ),
				'choices' => array( 'coastal-movement' => __( 'Flowing layers, a sand-and-blue palette, and lightweight textured cloth', 'unikon-webmcp-demo' ), 'mixed-trends' => __( 'Neon tailoring, floral sportswear, metallic eveningwear, and denim basics', 'unikon-webmcp-demo' ), 'logo-study' => __( 'A board made only from fashion-brand logos', 'unikon-webmcp-demo' ) ),
				'min_length' => 20, 'max_length' => 320, 'correct' => 'coastal-movement', 'keywords' => array( 'coherent', 'flow', 'movement', 'palette', 'material', 'texture', 'coastal', 'focused' ),
			),
			array(
				'id' => 'collection-rationale', 'type' => 'essay', 'title' => __( 'Final essay: Collection rationale', 'unikon-webmcp-demo' ),
				'prompt' => __( 'Write a short design rationale connecting inspiration, silhouette, colour, and material into one collection direction. Include one choice you deliberately excluded.', 'unikon-webmcp-demo' ),
				'choices' => array(), 'min_length' => 120, 'max_length' => 1200, 'correct' => null,
				'keywords' => array( 'inspiration', 'silhouette', 'shape', 'colour', 'color', 'palette', 'material', 'fabric', 'excluded', 'removed', 'coherent', 'collection' ),
			),
		);
	}

	/** Public-safe topic structure; Vimeo URLs are stored privately in WordPress options. */
	public static function video_topics() {
		$topics = array(
			array( 'threading-machine', 'Threading the Sewing Machine', array( 'thread', 'guide', 'tension', 'needle', 'presser' ) ),
			array( 'machine-tension', 'Machine Tension', array( 'tension', 'balanced', 'stitch', 'upper', 'bobbin' ) ),
			array( 'guide-fabric', 'Guiding the Fabric', array( 'guide', 'fabric', 'feed', 'hands', 'pull', 'control' ) ),
			array( 'practice-preparation', 'Prepare for Practice', array( 'practice', 'needle', 'machine', 'safety', 'scrap', 'setup' ) ),
			array( 'straight-lines', 'Straight Lines', array( 'straight', 'seam', 'guide', 'allowance', 'line', 'speed' ) ),
			array( 'curved-lines', 'Curved Lines', array( 'curve', 'pivot', 'guide', 'slow', 'line', 'needle' ) ),
			array( 'angled-lines', 'Angled Lines', array( 'angle', 'corner', 'pivot', 'needle', 'line', 'turn' ) ),
			array( 'science-repetition', 'Skill Building Through Repetition', array( 'repeat', 'practice', 'control', 'accuracy', 'muscle', 'consistent' ) ),
			array( 'basic-zipper', 'Basic Zipper', array( 'zipper', 'foot', 'teeth', 'baste', 'seam', 'stitch' ) ),
			array( 'invisible-zipper', 'Invisible Zipper', array( 'invisible', 'coil', 'foot', 'press', 'teeth', 'seam' ) ),
			array( 'decorative-zipper', 'Decorative Zipper', array( 'decorative', 'zipper', 'topstitch', 'placement', 'visible', 'edge' ) ),
			array( 'front-fly-zipper', 'Front-Fly Zipper', array( 'fly', 'zipper', 'shield', 'extension', 'topstitch', 'front' ) ),
			array( 'topstitch-understitch', 'Topstitching and Understitching', array( 'topstitch', 'understitch', 'edge', 'facing', 'seam', 'visible' ) ),
			array( 'reinforce-stitching', 'Reinforcement Stitching', array( 'reinforce', 'backstitch', 'stress', 'secure', 'stitch', 'strength' ) ),
			array( 'ease-gathers', 'Ease and Gathers', array( 'ease', 'gather', 'stitch', 'distribute', 'fullness', 'thread' ) ),
			array( 'sew-darts', 'Sewing Darts', array( 'dart', 'point', 'taper', 'press', 'shape', 'stitch' ) ),
			array( 'sew-corners', 'Sewing Corners', array( 'corner', 'pivot', 'trim', 'turn', 'needle', 'point' ) ),
			array( 'plackets', 'Plackets', array( 'placket', 'opening', 'reinforce', 'fold', 'edge', 'closure' ) ),
			array( 'blind-hemming', 'Blind Hemming Reflection', array( 'blind', 'hem', 'fold', 'stitch', 'invisible', 'finish' ) ),
		);
		return array_map( static function ( $item, $index ) use ( $topics ) {
			$is_final = $index === count( $topics ) - 1;
			return array(
				'id' => $item[0], 'title' => sprintf( __( 'Video %1$d: %2$s', 'unikon-webmcp-demo' ), $index + 1, $item[1] ),
				'type' => $is_final ? 'essay' : 'short_answer',
				'prompt' => $is_final
					? __( 'After watching, write a final reflection connecting blind hemming to at least two earlier control or finishing techniques in this course.', 'unikon-webmcp-demo' )
					: __( 'After watching, describe the key control point demonstrated and one detail you would check during practice.', 'unikon-webmcp-demo' ),
				'min_length' => $is_final ? 140 : 40, 'max_length' => $is_final ? 1200 : 500, 'keywords' => $item[2],
			);
		}, $topics, array_keys( $topics ) );
	}

	/** Evaluate one assessment with a deterministic minimum-evidence rubric. */
	public static function evaluate_assessment( $course_id, $activity_id, $answer_id, $response ) {
		$assessment = null;
		foreach ( self::assessments( $course_id ) as $item ) if ( $item['id'] === $activity_id ) $assessment = $item;
		if ( ! $assessment ) return new \WP_Error( 'invalid_activity', __( 'That assessment does not exist in this course.', 'unikon-webmcp-demo' ), array( 'status' => 400 ) );
		$length = function_exists( 'mb_strlen' ) ? mb_strlen( $response ) : strlen( $response );
		$normalized = strtolower( remove_accents( $response ) );
		$matches = 0;
		foreach ( $assessment['keywords'] as $keyword ) if ( false !== strpos( $normalized, $keyword ) ) ++$matches;
		$choice_ok = null === $assessment['correct'] || $assessment['correct'] === $answer_id;
		$needed = 'essay' === $assessment['type'] ? 3 : 1;
		$passed = $choice_ok && $length >= $assessment['min_length'] && $matches >= $needed;
		return array(
			'passed' => $passed,
			'feedback_code' => $passed ? 'passed' : ( $choice_ok ? 'expand_response' : 'review_choice' ),
			'feedback' => $passed
				? __( 'This response meets the layer criteria. Continue to the next assessment.', 'unikon-webmcp-demo' )
				: __( 'Review this layer and strengthen the response with specific evidence from its prompt before trying again.', 'unikon-webmcp-demo' ),
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
