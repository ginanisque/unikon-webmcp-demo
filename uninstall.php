<?php

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

delete_metadata( 'user', 0, 'unikon_webmcp_progress_v1', '', true );
delete_metadata( 'user', 0, 'unikon_webmcp_progress_v1_fashion-design-studio', '', true );
delete_metadata( 'user', 0, 'unikon_webmcp_progress_v1_sewing-video-class', '', true );

$page_id = (int) get_option( 'unikon_webmcp_demo_page_id' );
if ( $page_id ) {
	$page = get_post( $page_id );
	if ( $page instanceof WP_Post && '[unikon_webmcp_demo]' === trim( $page->post_content ) ) {
		wp_delete_post( $page_id, true );
	}
}
delete_option( 'unikon_webmcp_demo_page_id' );

$design_page_id = (int) get_option( 'unikon_webmcp_demo_design_page_id' );
if ( $design_page_id ) {
	$design_page = get_post( $design_page_id );
	if ( $design_page instanceof WP_Post && '[unikon_webmcp_demo course="fashion-design-studio"]' === trim( $design_page->post_content ) ) {
		wp_delete_post( $design_page_id, true );
	}
}
delete_option( 'unikon_webmcp_demo_design_page_id' );

$sewing_page_id = (int) get_option( 'unikon_webmcp_demo_sewing_page_id' );
if ( $sewing_page_id ) {
	$sewing_page = get_post( $sewing_page_id );
	if ( $sewing_page instanceof WP_Post && '[unikon_webmcp_demo course="sewing-video-class"]' === trim( $sewing_page->post_content ) ) wp_delete_post( $sewing_page_id, true );
}
delete_option( 'unikon_webmcp_demo_sewing_page_id' );
delete_option( 'unikon_webmcp_sewing_vimeo_urls' );
