<?php

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

delete_metadata( 'user', 0, 'unikon_webmcp_progress_v1', '', true );

$page_id = (int) get_option( 'unikon_webmcp_demo_page_id' );
if ( $page_id ) {
	$page = get_post( $page_id );
	if ( $page instanceof WP_Post && '[unikon_webmcp_demo]' === trim( $page->post_content ) ) {
		wp_delete_post( $page_id, true );
	}
}
delete_option( 'unikon_webmcp_demo_page_id' );

