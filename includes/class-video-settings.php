<?php

namespace Ginani\UnikonWebMCPDemo;

defined( 'ABSPATH' ) || exit;

final class Video_Settings {
	const OPTION = 'unikon_webmcp_sewing_vimeo_urls';

	public function run() {
		add_action( 'admin_menu', array( $this, 'menu' ) );
		add_action( 'admin_init', array( $this, 'register' ) );
	}

	public function menu() {
		add_options_page(
			__( 'Sewing Class Videos', 'unikon-webmcp-demo' ),
			__( 'Sewing Class Videos', 'unikon-webmcp-demo' ),
			'manage_options',
			'unikon-webmcp-videos',
			array( $this, 'render' )
		);
	}

	public function register() {
		register_setting( 'unikon_webmcp_videos', self::OPTION, array( 'type' => 'object', 'sanitize_callback' => array( $this, 'sanitize' ), 'default' => array() ) );
	}

	/** Keep only Vimeo URLs keyed to known topics. */
	public function sanitize( $input ) {
		$clean = array();
		if ( ! is_array( $input ) ) return $clean;
		if ( ! empty( $input['bulk_json'] ) ) {
			$decoded = json_decode( wp_unslash( $input['bulk_json'] ), true );
			if ( is_array( $decoded ) ) $input = array_merge( $input, $decoded );
		}
		foreach ( Content::video_topics() as $topic ) {
			$url = isset( $input[ $topic['id'] ] ) ? esc_url_raw( trim( wp_unslash( $input[ $topic['id'] ] ) ) ) : '';
			if ( $url && self::embed_url( $url ) ) $clean[ $topic['id'] ] = $url;
		}
		return $clean;
	}

	public static function urls() {
		$value = get_option( self::OPTION, array() );
		return is_array( $value ) ? $value : array();
	}

	/** Convert approved Vimeo page/player URLs to a safe player URL. */
	public static function embed_url( $url ) {
		$parts = wp_parse_url( $url );
		if ( ! is_array( $parts ) || empty( $parts['host'] ) || empty( $parts['path'] ) ) return '';
		$host = strtolower( $parts['host'] );
		if ( ! in_array( $host, array( 'vimeo.com', 'www.vimeo.com', 'player.vimeo.com' ), true ) ) return '';
		if ( ! preg_match( '#(?:/video)?/(\d+)(?:/([a-zA-Z0-9]+))?#', $parts['path'], $match ) ) return '';
		$query = array();
		if ( ! empty( $parts['query'] ) ) parse_str( html_entity_decode( $parts['query'] ), $query );
		$hash = isset( $query['h'] ) ? preg_replace( '/[^a-zA-Z0-9]/', '', $query['h'] ) : ( $match[2] ?? '' );
		$embed = 'https://player.vimeo.com/video/' . $match[1];
		if ( $hash ) $embed = add_query_arg( 'h', $hash, $embed );
		return esc_url_raw( $embed );
	}

	public function render() {
		if ( ! current_user_can( 'manage_options' ) ) return;
		$urls = self::urls();
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Sewing Class Vimeo Videos', 'unikon-webmcp-demo' ); ?></h1>
			<p><?php esc_html_e( 'Paste one Vimeo page or player URL for each topic. URLs are stored in WordPress, not in the public plugin repository.', 'unikon-webmcp-demo' ); ?></p>
			<form method="post" action="options.php">
				<?php settings_fields( 'unikon_webmcp_videos' ); ?>
				<h2><?php esc_html_e( 'Private bulk import', 'unikon-webmcp-demo' ); ?></h2>
				<p><?php esc_html_e( 'Optionally paste the private JSON mapping generated during local development. It is validated and stored only in this WordPress database.', 'unikon-webmcp-demo' ); ?></p>
				<textarea class="large-text code" rows="5" name="<?php echo esc_attr( self::OPTION ); ?>[bulk_json]" placeholder='{"threading-machine":"https://vimeo.com/…"}'></textarea>
				<table class="form-table" role="presentation"><tbody>
				<?php foreach ( Content::video_topics() as $topic ) : ?>
					<tr><th scope="row"><label for="video-<?php echo esc_attr( $topic['id'] ); ?>"><?php echo esc_html( $topic['title'] ); ?></label></th>
					<td><input class="regular-text" type="url" id="video-<?php echo esc_attr( $topic['id'] ); ?>" name="<?php echo esc_attr( self::OPTION ); ?>[<?php echo esc_attr( $topic['id'] ); ?>]" value="<?php echo esc_attr( $urls[ $topic['id'] ] ?? '' ); ?>" placeholder="https://vimeo.com/…"></td></tr>
				<?php endforeach; ?>
				</tbody></table>
				<?php submit_button( __( 'Save Vimeo links', 'unikon-webmcp-demo' ) ); ?>
			</form>
		</div>
		<?php
	}
}
