<?php
/** @var array<string,mixed> $course */
/** @var array<string,mixed>|null $state */
/** @var array<string,mixed>|null $summary */
defined( 'ABSPATH' ) || exit;
?>
<main
	class="uwmcp-app"
	data-uwmcp-app
	data-authenticated="<?php echo is_user_logged_in() ? 'true' : 'false'; ?>"
	data-course-id="<?php echo esc_attr( $course['id'] ); ?>"
	data-rest-root="<?php echo esc_url( rest_url( 'unikon-webmcp-demo/v1/' ) ); ?>"
	data-rest-nonce="<?php echo is_user_logged_in() ? esc_attr( wp_create_nonce( 'wp_rest' ) ) : ''; ?>"
>
	<header class="uwmcp-hero">
		<?php if ( count( $course_links ) > 1 ) : ?>
			<nav class="uwmcp-course-nav" aria-label="<?php esc_attr_e( 'Demo courses', 'unikon-webmcp-demo' ); ?>">
				<?php foreach ( \Ginani\UnikonWebMCPDemo\Content::courses() as $nav_id => $nav_course ) : ?>
					<?php if ( isset( $course_links[ $nav_id ] ) ) : ?>
						<a href="<?php echo esc_url( $course_links[ $nav_id ] ); ?>" <?php echo $nav_id === $course['id'] ? 'aria-current="page"' : ''; ?>><?php echo esc_html( $nav_course['title'] ); ?></a>
					<?php endif; ?>
				<?php endforeach; ?>
			</nav>
		<?php endif; ?>
		<p class="uwmcp-eyebrow"><?php esc_html_e( 'Agent-assisted fashion learning', 'unikon-webmcp-demo' ); ?></p>
		<h1><?php echo esc_html( $course['title'] ); ?></h1>
		<p><?php echo esc_html( $course['description'] ); ?></p>
		<div class="uwmcp-agent-status" data-agent-status>
			<span aria-hidden="true"></span>
			<?php esc_html_e( 'WebMCP tools are checking browser support…', 'unikon-webmcp-demo' ); ?>
		</div>
	</header>

	<div class="uwmcp-live sr-only" aria-live="polite" aria-atomic="true" data-live-region></div>

	<?php if ( ! is_user_logged_in() ) : ?>
		<section class="uwmcp-card uwmcp-sign-in" aria-labelledby="uwmcp-sign-in-title">
			<p class="uwmcp-step">01</p>
			<div>
				<h2 id="uwmcp-sign-in-title"><?php esc_html_e( 'Sign in to begin', 'unikon-webmcp-demo' ); ?></h2>
				<p><?php esc_html_e( 'Your lesson and exercise progress are saved securely to your WordPress account.', 'unikon-webmcp-demo' ); ?></p>
				<a class="uwmcp-button" href="<?php echo esc_url( wp_login_url( get_permalink() ) ); ?>"><?php esc_html_e( 'Sign in with WordPress', 'unikon-webmcp-demo' ); ?></a>
			</div>
		</section>
	<?php else : ?>
		<section class="uwmcp-progress" aria-labelledby="uwmcp-progress-title">
			<div>
				<p class="uwmcp-eyebrow"><?php esc_html_e( 'Your progress', 'unikon-webmcp-demo' ); ?></p>
				<h2 id="uwmcp-progress-title"><span data-progress-value><?php echo esc_html( $summary['percent'] ); ?></span>%</h2>
			</div>
			<div class="uwmcp-progress-track" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="<?php echo esc_attr( $summary['percent'] ); ?>" data-progress-bar>
				<span style="width: <?php echo esc_attr( $summary['percent'] ); ?>%"></span>
			</div>
			<p data-next-step><?php echo esc_html( $summary['next_step']['label'] ); ?></p>
		</section>

		<section class="uwmcp-card" id="uwmcp-lesson" data-lesson-section data-status="<?php echo esc_attr( $state['lesson_status'] ); ?>" aria-labelledby="uwmcp-lesson-title">
			<p class="uwmcp-step">01</p>
			<div>
				<p class="uwmcp-kicker"><?php esc_html_e( 'One concise lesson', 'unikon-webmcp-demo' ); ?></p>
				<h2 id="uwmcp-lesson-title"><?php echo esc_html( $course['lesson']['title'] ); ?></h2>
				<p><strong><?php esc_html_e( 'Objective:', 'unikon-webmcp-demo' ); ?></strong> <?php echo esc_html( $course['lesson']['objective'] ); ?></p>
				<div class="uwmcp-lesson-body" data-lesson-body <?php echo 'not_started' === $state['lesson_status'] ? 'hidden' : ''; ?>>
					<?php foreach ( $course['lesson']['body'] as $paragraph ) : ?>
						<p><?php echo esc_html( $paragraph ); ?></p>
					<?php endforeach; ?>
				</div>
				<button class="uwmcp-button" type="button" data-action="open-lesson" <?php echo 'not_started' !== $state['lesson_status'] ? 'hidden' : ''; ?>><?php esc_html_e( 'Open lesson', 'unikon-webmcp-demo' ); ?></button>
				<button class="uwmcp-button uwmcp-button-secondary" type="button" data-action="start-exercise" <?php echo 'not_started' === $state['lesson_status'] || 'completed' === $state['exercise_status'] ? 'hidden' : ''; ?>><?php esc_html_e( 'Start exercise', 'unikon-webmcp-demo' ); ?></button>
			</div>
		</section>

		<section class="uwmcp-card" id="uwmcp-exercise" data-exercise-section data-status="<?php echo esc_attr( $state['exercise_status'] ); ?>" aria-labelledby="uwmcp-exercise-title" <?php echo 'not_started' === $state['exercise_status'] ? 'hidden' : ''; ?>>
			<p class="uwmcp-step">02</p>
			<div>
				<p class="uwmcp-kicker"><?php esc_html_e( 'Apply what you learned', 'unikon-webmcp-demo' ); ?></p>
				<h2 id="uwmcp-exercise-title"><?php echo esc_html( $course['exercise']['title'] ); ?></h2>
				<p><?php echo esc_html( $course['exercise']['prompt'] ); ?></p>
				<form data-exercise-form>
					<fieldset>
						<legend><?php esc_html_e( 'Choose one fabric', 'unikon-webmcp-demo' ); ?></legend>
						<?php foreach ( $course['exercise']['choices'] as $value => $label ) : ?>
							<label class="uwmcp-choice"><input type="radio" name="answer_id" value="<?php echo esc_attr( $value ); ?>" required> <span><?php echo esc_html( $label ); ?></span></label>
						<?php endforeach; ?>
					</fieldset>
					<label for="uwmcp-reason"><strong><?php esc_html_e( 'Explain your choice', 'unikon-webmcp-demo' ); ?></strong></label>
					<textarea id="uwmcp-reason" name="reason" rows="4" minlength="12" maxlength="<?php echo esc_attr( $course['exercise']['max_reason_length'] ); ?>" required></textarea>
					<div class="uwmcp-confirmation" data-confirmation hidden tabindex="-1">
						<strong><?php esc_html_e( 'Ready for your review', 'unikon-webmcp-demo' ); ?></strong>
						<p><?php esc_html_e( 'An agent staged this answer. Check it carefully; nothing is graded or saved until you choose Submit my answer.', 'unikon-webmcp-demo' ); ?></p>
					</div>
					<button class="uwmcp-button" type="submit" data-submit-answer><?php esc_html_e( 'Submit my answer', 'unikon-webmcp-demo' ); ?></button>
				</form>
				<div class="uwmcp-feedback" data-feedback hidden tabindex="-1"></div>
			</div>
		</section>
	<?php endif; ?>
</main>
