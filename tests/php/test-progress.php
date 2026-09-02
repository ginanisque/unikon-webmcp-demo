<?php

use Ginani\UnikonWebMCPDemo\Progress;

final class Unikon_WebMCP_Progress_Test extends WP_UnitTestCase {
	/** @var Progress */
	private $progress;

	public function set_up() {
		parent::set_up();
		$this->progress = new Progress();
	}

	public function test_default_and_permitted_transitions() {
		$user = self::factory()->user->create();
		$this->assertSame( 'not_started', $this->progress->get( $user )['lesson_status'] );
		$this->assertSame( 'in_progress', $this->progress->open_lesson( $user )['lesson_status'] );
		$this->assertSame( 'in_progress', $this->progress->start_exercise( $user )['exercise_status'] );
		$result = $this->progress->submit( $user, 'fabric-choice', 'cotton-poplin', 'It is stable and light enough to hold the silhouette.' );
		$this->assertTrue( $result['evaluation']['passed'] );
		$this->assertSame( 'completed', $result['state']['exercise_status'] );
	}

	public function test_rejects_prerequisite_skip() {
		$user = self::factory()->user->create();
		$this->assertWPError( $this->progress->start_exercise( $user ) );
		$this->assertWPError( $this->progress->submit( $user, 'fabric-choice', 'cotton-poplin', 'It is stable and manageable for a beginner.' ) );
	}

	public function test_scoring_is_deterministic() {
		$pass = Ginani\UnikonWebMCPDemo\Content::evaluate( 'cotton-poplin', 'Its stable medium weight supports the silhouette.' );
		$fail = Ginani\UnikonWebMCPDemo\Content::evaluate( 'silk-charmeuse', 'It has a beautiful drape for this skirt.' );
		$this->assertTrue( $pass['passed'] );
		$this->assertFalse( $fail['passed'] );
	}

	public function test_two_users_are_isolated() {
		$first  = self::factory()->user->create();
		$second = self::factory()->user->create();
		$this->progress->open_lesson( $first );
		$this->assertSame( 'in_progress', $this->progress->get( $first )['lesson_status'] );
		$this->assertSame( 'not_started', $this->progress->get( $second )['lesson_status'] );
	}

	public function test_courses_have_independent_progress_and_scoring() {
		$user = self::factory()->user->create();
		$this->progress->open_lesson( $user, Ginani\UnikonWebMCPDemo\Content::DESIGN_COURSE_ID );
		$this->progress->start_exercise( $user, Ginani\UnikonWebMCPDemo\Content::DESIGN_COURSE_ID );
		$failed = $this->progress->submit(
			$user, 'design-signal', 'current-trends', 'Current trends might offer movement and shape.',
			Ginani\UnikonWebMCPDemo\Content::DESIGN_COURSE_ID
		);
		$this->assertFalse( $failed['evaluation']['passed'] );
		$result = $this->progress->submit(
			$user,
			'design-signal',
			'repeated-curves',
			'The repeated curved line creates a specific shape and movement signal.',
			Ginani\UnikonWebMCPDemo\Content::DESIGN_COURSE_ID
		);
		$this->assertTrue( $result['evaluation']['passed'] );
		$this->assertSame( 'completed', $this->progress->get( $user, Ginani\UnikonWebMCPDemo\Content::DESIGN_COURSE_ID )['activity_statuses']['design-signal'] );
		$this->assertCount( 2, $this->progress->get( $user, Ginani\UnikonWebMCPDemo\Content::DESIGN_COURSE_ID )['submissions'] );
		$this->assertSame( 'in_progress', $this->progress->get( $user, Ginani\UnikonWebMCPDemo\Content::DESIGN_COURSE_ID )['activity_statuses']['colour-story'] );
		$this->assertSame( 'not_started', $this->progress->get( $user )['lesson_status'] );
	}

	public function test_sewing_topic_unlocks_only_the_next_video() {
		$user = self::factory()->user->create();
		$course = Ginani\UnikonWebMCPDemo\Content::SEWING_COURSE_ID;
		$this->progress->open_lesson( $user, $course );
		$this->progress->start_exercise( $user, $course );
		$result = $this->progress->submit( $user, 'threading-machine', '', 'Thread through each guide and check the needle and tension path before sewing.', $course );
		$this->assertTrue( $result['evaluation']['passed'] );
		$this->assertSame( 'completed', $result['state']['activity_statuses']['threading-machine'] );
		$this->assertSame( 'in_progress', $result['state']['activity_statuses']['machine-tension'] );
		$this->assertArrayNotHasKey( 'guide-fabric', $result['state']['activity_statuses'] );
		$this->assertSame( 42, $this->progress->summary( $result['state'], $course )['percent'] );
	}
}
