<?php
/*
* Plugin Name: Presslabs Let's Encrypt
* Description: This plugin is used by Presslabs to generate SSL certificates.
* Author: Presslabs
*
* */

function pl_acme_challenge() {
	$oxygen_url = apply_filters( 'pl_acme_oxygen_url', 'https://api.presslabs.com/api' );
	$oxygen_verify_cert = apply_filters( 'pl_acme_oxygen_verify_cert', true );
	$challenge_url = "$oxygen_url/certificates/" . ltrim( $_SERVER['REQUEST_URI'], '/' );
	$response = wp_remote_get( $challenge_url, array( 'sslverify' => $oxygen_verify_cert ) );
	if ( ! is_wp_error( $response ) && $response['response']['code'] == 200 ) {
		echo $response['body'];
	} else {
		trigger_error( 'ACME challenge failed. request: "' . $challenge_url . '", response: ' . json_encode( $response ), E_USER_ERROR );
	}
	die();
}

function pl_lets_encrypt() {
    if ( substr( $_SERVER['REQUEST_URI'], 0, 28 ) == '/.well-known/acme-challenge/' ) {
        pl_acme_challenge();
    }
}

add_action('init', 'pl_lets_encrypt');
