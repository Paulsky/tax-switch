<?php

class Wdevs_Tax_Switch_Ajax {
	private $form_action = 'wts_set_cookie';

	public function getFormAction() {
		return $this->form_action;
	}

	public function wts_set_cookie_endpoint() {
		if ( ! wp_verify_nonce( $_POST['nonce'], 'ajax-nonce' ) ) {
			wp_send_json_error( [ 'message' => 'Invalid nonce' ] );
		}

		if ( ! isset( $_POST['is_switched'] ) ) {
			wp_send_json_error( [ 'message' => 'Invalid data' ] );
		}

		$is_switched = filter_var($_POST['is_switched'], FILTER_VALIDATE_BOOLEAN);

		$one_month = time() + ( 86400 * 30 );

		setcookie( 'wts_is_switched', ($is_switched ? '1' : '0'), [
			'expires'  => $one_month,
			'path'     => '/',
			'domain'   => $_SERVER['HTTP_HOST'],
			'secure'   => false,
			'httponly' => true,
			'samesite' => 'Strict'
		] );

		wp_send_json_success( [ 'is_switched' => $is_switched ] );
	}
}
