<?php

namespace Ginani\UnikonWebMCPDemo;

defined( 'ABSPATH' ) || exit;

final class Rest_Controller {
	const NAMESPACE = 'unikon-webmcp-demo/v1';

	/** @var Progress */
	private $progress;

	public function __construct( Progress $progress ) {
		$this->progress = $progress;
	}

	public function register_routes() {
		// Route definitions are implemented in the authenticated REST stage.
	}
}

