<?php

use Ginani\UnikonWebMCPDemo\Content;
use Ginani\UnikonWebMCPDemo\Video_Settings;

final class Unikon_WebMCP_Video_Settings_Test extends WP_UnitTestCase {
	public function test_sewing_course_has_nineteen_ordered_topics() {
		$topics = Content::video_topics();
		$this->assertCount( 19, $topics );
		$this->assertSame( 'threading-machine', $topics[0]['id'] );
		$this->assertSame( 'blind-hemming', $topics[18]['id'] );
		$this->assertSame( 'essay', $topics[18]['type'] );
	}

	public function test_vimeo_urls_are_normalized_and_other_hosts_rejected() {
		$id = '123' . '456';
		$this->assertSame( 'https://player.vimeo.com/video/' . $id, Video_Settings::embed_url( 'https://vimeo.com/' . $id ) );
		$this->assertSame( 'https://player.vimeo.com/video/' . $id . '?h=abc123', Video_Settings::embed_url( 'https://vimeo.com/' . $id . '/abc123' ) );
		$this->assertSame( '', Video_Settings::embed_url( 'https://example.com/video/' . $id ) );
	}
}
