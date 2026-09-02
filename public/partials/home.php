<?php
/** @var string $demo_url */
/** @var array<string,string> $course_links */
defined( 'ABSPATH' ) || exit;
?>
<main class="uwmcp-home">
	<section class="uwmcp-home-hero" aria-labelledby="uwmcp-home-title">
		<img class="uwmcp-home-hero-image" src="<?php echo esc_url( UNIKON_WEBMCP_DEMO_URL . 'assets/fashion-elearning-unikon.png' ); ?>" alt="A fashion designer fitting a blue garment on a dress form beside patterns, fabric, and a laptop.">
		<div class="uwmcp-home-hero-shade" aria-hidden="true"></div>
		<div class="uwmcp-home-hero-content">
			<p class="uwmcp-eyebrow"><?php esc_html_e( 'Fashion Learning Studio', 'unikon-webmcp-demo' ); ?></p>
			<h1 id="uwmcp-home-title"><?php esc_html_e( 'Learn fashion with an agent—and keep the final say.', 'unikon-webmcp-demo' ); ?></h1>
			<p><?php esc_html_e( 'Explore fashion design and practical sewing through structured lessons, guided exercises, and five browser-native WebMCP tools.', 'unikon-webmcp-demo' ); ?></p>
			<div class="uwmcp-home-actions">
				<a class="uwmcp-home-button" href="<?php echo esc_url( $demo_url ); ?>"><?php echo is_user_logged_in() ? esc_html__( 'Enter the learning studio', 'unikon-webmcp-demo' ) : esc_html__( 'Log in to try the demo', 'unikon-webmcp-demo' ); ?></a>
				<a class="uwmcp-home-text-link" href="https://github.com/ginanisque/unikon-webmcp-demo"><?php esc_html_e( 'View the open-source project', 'unikon-webmcp-demo' ); ?></a>
			</div>
		</div>
	</section>

	<section class="uwmcp-home-intro" aria-labelledby="uwmcp-home-courses-title">
		<div>
			<p class="uwmcp-eyebrow"><?php esc_html_e( 'Human-first by design', 'unikon-webmcp-demo' ); ?></p>
			<h2 id="uwmcp-home-courses-title"><?php esc_html_e( 'Three ways to learn', 'unikon-webmcp-demo' ); ?></h2>
		</div>
		<p><?php esc_html_e( 'Your browser agent can find the next lesson, open an exercise, and stage a response. You review the work and personally choose when to submit it.', 'unikon-webmcp-demo' ); ?></p>
	</section>

	<div class="uwmcp-home-courses">
		<?php foreach ( \Ginani\UnikonWebMCPDemo\Content::courses() as $course_id => $course ) : ?>
			<article>
				<p class="uwmcp-home-course-number"><?php echo esc_html( sprintf( '%02d', array_search( $course_id, array_keys( \Ginani\UnikonWebMCPDemo\Content::courses() ), true ) + 1 ) ); ?></p>
				<h3><?php echo esc_html( $course['title'] ); ?></h3>
				<p><?php echo esc_html( $course['description'] ); ?></p>
				<?php if ( isset( $course_links[ $course_id ] ) ) : ?><a href="<?php echo esc_url( is_user_logged_in() ? $course_links[ $course_id ] : wp_login_url( $course_links[ $course_id ] ) ); ?>"><?php esc_html_e( 'Open course', 'unikon-webmcp-demo' ); ?></a><?php endif; ?>
			</article>
		<?php endforeach; ?>
	</div>

	<section class="uwmcp-home-boundary">
		<p class="uwmcp-eyebrow"><?php esc_html_e( 'A clear boundary', 'unikon-webmcp-demo' ); ?></p>
		<h2><?php esc_html_e( 'The agent assists. The learner commits.', 'unikon-webmcp-demo' ); ?></h2>
		<p><?php esc_html_e( 'An agent cannot claim a video was watched or submit, grade, or save an answer. Every consequential learning action remains visible and learner-controlled.', 'unikon-webmcp-demo' ); ?></p>
	</section>
</main>
