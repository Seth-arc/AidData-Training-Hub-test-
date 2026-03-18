<?php
/**
 * Plugin Name: AidData Auth System
 * Description: Centralized WordPress authentication handlers for frontend auth modals.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Read environment variables robustly across PHP SAPIs.
 */
function aiddata_auth_env( $key, $default = '' ) {
	$value = getenv( $key );
	if ( false !== $value && '' !== (string) $value ) {
		return (string) $value;
	}

	if ( isset( $_ENV[ $key ] ) && '' !== (string) $_ENV[ $key ] ) {
		return (string) $_ENV[ $key ];
	}

	if ( isset( $_SERVER[ $key ] ) && '' !== (string) $_SERVER[ $key ] ) {
		return (string) $_SERVER[ $key ];
	}

	return $default;
}

/**
 * Enqueue frontend auth integration and pass WordPress endpoint data.
 */
function aiddata_auth_enqueue_scripts() {
	if ( is_admin() ) {
		return;
	}

	$auth_script_path = get_template_directory() . '/assets/js/auth-integration.js';
	$auth_script_ver  = file_exists( $auth_script_path ) ? (string) filemtime( $auth_script_path ) : '1.0.0';

	wp_enqueue_script(
		'aiddata-auth',
		get_template_directory_uri() . '/assets/js/auth-integration.js',
		array( 'jquery' ),
		$auth_script_ver,
		true
	);

	wp_localize_script(
		'aiddata-auth',
		'auth_object',
		array(
			'ajaxurl'  => admin_url( 'admin-ajax.php' ),
			'security' => wp_create_nonce( 'custom-auth-nonce' ),
			'home_url' => home_url(),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'aiddata_auth_enqueue_scripts' );

/**
 * Hide the WordPress admin toolbar on the front-end for non-admin users.
 */
function aiddata_auth_maybe_hide_frontend_admin_bar( $show ) {
	if ( is_admin() ) {
		return $show;
	}

	if ( ! is_user_logged_in() ) {
		return $show;
	}

	if ( current_user_can( 'manage_options' ) ) {
		return $show;
	}

	return false;
}
add_filter( 'show_admin_bar', 'aiddata_auth_maybe_hide_frontend_admin_bar' );

/**
 * Temporary login debug logger.
 * Enable by setting env var AIDDATA_LOGIN_DEBUG=true.
 */
function aiddata_auth_login_debug_log( $event, $context = array() ) {
	static $enabled = null;

	if ( null === $enabled ) {
		$raw_value = aiddata_auth_env( 'AIDDATA_LOGIN_DEBUG', '' );
		$enabled   = in_array( strtolower( (string) $raw_value ), array( '1', 'true', 'yes', 'on' ), true );
	}

	if ( ! $enabled ) {
		return;
	}

	if ( ! is_array( $context ) ) {
		$context = array( 'value' => $context );
	}

	unset( $context['password'], $context['user_password'], $context['security'] );

	error_log( '[aiddata-login-debug] ' . $event . ' ' . wp_json_encode( $context ) );
}

/**
 * Configure SMTP delivery when environment variables are present.
 *
 * Supported env vars:
 * - AIDDATA_SMTP_HOST
 * - AIDDATA_SMTP_PORT (optional, default 587)
 * - AIDDATA_SMTP_USER (optional)
 * - AIDDATA_SMTP_PASS (optional)
 * - AIDDATA_SMTP_SECURE (optional: tls|ssl)
 * - AIDDATA_SMTP_FROM_EMAIL (optional)
 * - AIDDATA_SMTP_FROM_NAME (optional)
 */
function aiddata_auth_configure_phpmailer( $phpmailer ) {
	$smtp_host = aiddata_auth_env( 'AIDDATA_SMTP_HOST', '' );
	if ( '' === trim( (string) $smtp_host ) ) {
		return;
	}

	$smtp_port = (int) aiddata_auth_env( 'AIDDATA_SMTP_PORT', '587' );
	if ( $smtp_port <= 0 ) {
		$smtp_port = 587;
	}

	$smtp_user   = aiddata_auth_env( 'AIDDATA_SMTP_USER', '' );
	$smtp_pass   = aiddata_auth_env( 'AIDDATA_SMTP_PASS', '' );
	$smtp_secure = strtolower( trim( (string) aiddata_auth_env( 'AIDDATA_SMTP_SECURE', '' ) ) );
	$from_email  = aiddata_auth_env( 'AIDDATA_SMTP_FROM_EMAIL', '' );
	$from_name   = aiddata_auth_env( 'AIDDATA_SMTP_FROM_NAME', '' );

	$phpmailer->isSMTP();
	$phpmailer->Host = trim( (string) $smtp_host );
	$phpmailer->Port = $smtp_port;

	if ( '' !== (string) $smtp_user ) {
		$phpmailer->SMTPAuth = true;
		$phpmailer->Username = (string) $smtp_user;
		$phpmailer->Password = (string) $smtp_pass;
	} else {
		$phpmailer->SMTPAuth = false;
	}

	if ( in_array( $smtp_secure, array( 'tls', 'ssl' ), true ) ) {
		$phpmailer->SMTPSecure = $smtp_secure;
	}

	if ( is_email( $from_email ) ) {
		$resolved_from_name = ( '' !== trim( (string) $from_name ) )
			? trim( (string) $from_name )
			: 'AidData Training Hub';
		$phpmailer->setFrom( $from_email, $resolved_from_name, false );
	}
}
add_action( 'phpmailer_init', 'aiddata_auth_configure_phpmailer' );

/**
 * Log SMTP configuration state (without secrets) for troubleshooting.
 */
function aiddata_auth_mail_config_context() {
	$smtp_pass = aiddata_auth_env( 'AIDDATA_SMTP_PASS', '' );

	return array(
		'mail_transport'         => aiddata_auth_env( 'AIDDATA_MAIL_TRANSPORT', 'wp_mail' ),
		'smtp_host'              => aiddata_auth_env( 'AIDDATA_SMTP_HOST', '' ),
		'smtp_port'              => aiddata_auth_env( 'AIDDATA_SMTP_PORT', '' ),
		'smtp_user'              => aiddata_auth_env( 'AIDDATA_SMTP_USER', '' ),
		'smtp_secure'            => aiddata_auth_env( 'AIDDATA_SMTP_SECURE', '' ),
		'smtp_from_email'        => aiddata_auth_env( 'AIDDATA_SMTP_FROM_EMAIL', '' ),
		'smtp_from_name'         => aiddata_auth_env( 'AIDDATA_SMTP_FROM_NAME', '' ),
		'smtp_pass_len'          => strlen( trim( str_replace( ' ', '', (string) $smtp_pass ) ) ),
		'smtp_host_configured'   => '' !== trim( aiddata_auth_env( 'AIDDATA_SMTP_HOST', '' ) ),
		'resend_from_email'      => aiddata_auth_env( 'AIDDATA_RESEND_FROM_EMAIL', '' ),
		'resend_from_name'       => aiddata_auth_env( 'AIDDATA_RESEND_FROM_NAME', '' ),
		'resend_key_len'         => strlen( trim( (string) aiddata_auth_env( 'AIDDATA_RESEND_API_KEY', '' ) ) ),
		'sendgrid_from_email'    => aiddata_auth_env( 'AIDDATA_SENDGRID_FROM_EMAIL', '' ),
		'sendgrid_from_name'     => aiddata_auth_env( 'AIDDATA_SENDGRID_FROM_NAME', '' ),
		'sendgrid_api_key_len'   => strlen( trim( (string) aiddata_auth_env( 'AIDDATA_SENDGRID_API_KEY', '' ) ) ),
	);
}

/**
 * Return active outbound mail transport.
 */
function aiddata_auth_mail_transport() {
	$transport = strtolower( trim( aiddata_auth_env( 'AIDDATA_MAIL_TRANSPORT', 'wp_mail' ) ) );
	if ( '' === $transport ) {
		$transport = 'wp_mail';
	}
	return $transport;
}

/**
 * Send email via Resend API using direct HTTPS (no WP HTTP API dependency).
 */
function aiddata_auth_send_mail_via_resend( $to_email, $subject, $text_body ) {
	$api_key = trim( aiddata_auth_env( 'AIDDATA_RESEND_API_KEY', '' ) );
	if ( '' === $api_key ) {
		return array(
			'success' => false,
			'error'   => 'Resend API key is missing.',
		);
	}

	$from_email = trim( aiddata_auth_env( 'AIDDATA_RESEND_FROM_EMAIL', '' ) );
	if ( '' === $from_email ) {
		$from_email = trim( aiddata_auth_env( 'AIDDATA_SMTP_FROM_EMAIL', '' ) );
	}

	if ( ! is_email( $from_email ) ) {
		return array(
			'success' => false,
			'error'   => 'Resend from email is missing or invalid.',
		);
	}

	$from_name = trim( aiddata_auth_env( 'AIDDATA_RESEND_FROM_NAME', '' ) );
	if ( '' === $from_name ) {
		$from_name = trim( aiddata_auth_env( 'AIDDATA_SMTP_FROM_NAME', '' ) );
	}
	if ( '' === $from_name ) {
		$from_name = 'AidData Training Hub';
	}

	$payload = wp_json_encode(
		array(
			'from'    => sprintf( '%s <%s>', $from_name, $from_email ),
			'to'      => array( $to_email ),
			'subject' => $subject,
			'text'    => $text_body,
		)
	);

	if ( ! is_string( $payload ) || '' === $payload ) {
		return array(
			'success' => false,
			'error'   => 'Could not encode Resend payload.',
		);
	}

	$endpoint = 'https://api.resend.com/emails';

	// Prefer cURL and fall back to stream transport if cURL is unavailable.
	if ( function_exists( 'curl_init' ) ) {
		$ch = curl_init( $endpoint );
		curl_setopt_array(
			$ch,
			array(
				CURLOPT_POST           => true,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_TIMEOUT        => 20,
				CURLOPT_HTTPHEADER     => array(
					'Authorization: Bearer ' . $api_key,
					'Content-Type: application/json',
					'Accept: application/json',
				),
				CURLOPT_POSTFIELDS     => $payload,
			)
		);

		$response_body = curl_exec( $ch );
		$curl_error    = curl_error( $ch );
		$status_code   = (int) curl_getinfo( $ch, CURLINFO_HTTP_CODE );
		curl_close( $ch );

		if ( $response_body === false ) {
			return array(
				'success' => false,
				'error'   => 'Resend cURL error: ' . $curl_error,
			);
		}

		if ( $status_code < 200 || $status_code >= 300 ) {
			return array(
				'success' => false,
				'error'   => 'Resend HTTP ' . $status_code . ': ' . substr( (string) $response_body, 0, 500 ),
			);
		}

		return array(
			'success' => true,
			'error'   => '',
		);
	}

	$context = stream_context_create(
		array(
			'http' => array(
				'method'  => 'POST',
				'timeout' => 20,
				'header'  => implode(
					"\r\n",
					array(
						'Authorization: Bearer ' . $api_key,
						'Content-Type: application/json',
						'Accept: application/json',
					)
				),
				'content' => $payload,
			),
		)
	);

	$response_body = @file_get_contents( $endpoint, false, $context );
	$status_code   = 0;
	if ( isset( $http_response_header ) && is_array( $http_response_header ) && ! empty( $http_response_header[0] ) ) {
		if ( preg_match( '#\s(\d{3})\s#', $http_response_header[0], $matches ) ) {
			$status_code = (int) $matches[1];
		}
	}

	if ( false === $response_body ) {
		return array(
			'success' => false,
			'error'   => 'Resend stream transport failed.',
		);
	}

	if ( $status_code < 200 || $status_code >= 300 ) {
		return array(
			'success' => false,
			'error'   => 'Resend HTTP ' . $status_code . ': ' . substr( (string) $response_body, 0, 500 ),
		);
	}

	return array(
		'success' => true,
		'error'   => '',
	);
}

/**
 * Send email via SendGrid API over HTTPS.
 */
function aiddata_auth_send_mail_via_sendgrid( $to_email, $subject, $text_body ) {
	$api_key = trim( aiddata_auth_env( 'AIDDATA_SENDGRID_API_KEY', '' ) );
	if ( '' === $api_key ) {
		return array(
			'success' => false,
			'error'   => 'SendGrid API key is missing.',
		);
	}

	$from_email = trim( aiddata_auth_env( 'AIDDATA_SENDGRID_FROM_EMAIL', '' ) );
	if ( '' === $from_email ) {
		$from_email = trim( aiddata_auth_env( 'AIDDATA_SMTP_FROM_EMAIL', '' ) );
	}

	if ( ! is_email( $from_email ) ) {
		return array(
			'success' => false,
			'error'   => 'SendGrid from email is missing or invalid.',
		);
	}

	$from_name = trim( aiddata_auth_env( 'AIDDATA_SENDGRID_FROM_NAME', '' ) );
	if ( '' === $from_name ) {
		$from_name = trim( aiddata_auth_env( 'AIDDATA_SMTP_FROM_NAME', '' ) );
	}
	if ( '' === $from_name ) {
		$from_name = 'AidData Training Hub';
	}

	$payload = wp_json_encode(
		array(
			'personalizations' => array(
				array(
					'to' => array(
						array(
							'email' => $to_email,
						),
					),
				),
			),
			'from'             => array(
				'email' => $from_email,
				'name'  => $from_name,
			),
			'subject'          => $subject,
			'content'          => array(
				array(
					'type'  => 'text/plain',
					'value' => $text_body,
				),
			),
		)
	);

	if ( ! is_string( $payload ) || '' === $payload ) {
		return array(
			'success' => false,
			'error'   => 'Could not encode SendGrid payload.',
		);
	}

	$endpoint = 'https://api.sendgrid.com/v3/mail/send';

	if ( function_exists( 'curl_init' ) ) {
		$ch = curl_init( $endpoint );
		curl_setopt_array(
			$ch,
			array(
				CURLOPT_POST           => true,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_TIMEOUT        => 20,
				CURLOPT_HTTPHEADER     => array(
					'Authorization: Bearer ' . $api_key,
					'Content-Type: application/json',
					'Accept: application/json',
				),
				CURLOPT_POSTFIELDS     => $payload,
			)
		);

		$response_body = curl_exec( $ch );
		$curl_error    = curl_error( $ch );
		$status_code   = (int) curl_getinfo( $ch, CURLINFO_HTTP_CODE );
		curl_close( $ch );

		if ( $response_body === false ) {
			return array(
				'success' => false,
				'error'   => 'SendGrid cURL error: ' . $curl_error,
			);
		}

		if ( $status_code < 200 || $status_code >= 300 ) {
			return array(
				'success' => false,
				'error'   => 'SendGrid HTTP ' . $status_code . ': ' . substr( (string) $response_body, 0, 500 ),
			);
		}

		return array(
			'success' => true,
			'error'   => '',
		);
	}

	$context = stream_context_create(
		array(
			'http' => array(
				'method'  => 'POST',
				'timeout' => 20,
				'header'  => implode(
					"\r\n",
					array(
						'Authorization: Bearer ' . $api_key,
						'Content-Type: application/json',
						'Accept: application/json',
					)
				),
				'content' => $payload,
			),
		)
	);

	$response_body = @file_get_contents( $endpoint, false, $context );
	$status_code   = 0;
	if ( isset( $http_response_header ) && is_array( $http_response_header ) && ! empty( $http_response_header[0] ) ) {
		if ( preg_match( '#\s(\d{3})\s#', $http_response_header[0], $matches ) ) {
			$status_code = (int) $matches[1];
		}
	}

	if ( false === $response_body ) {
		return array(
			'success' => false,
			'error'   => 'SendGrid stream transport failed.',
		);
	}

	if ( $status_code < 200 || $status_code >= 300 ) {
		return array(
			'success' => false,
			'error'   => 'SendGrid HTTP ' . $status_code . ': ' . substr( (string) $response_body, 0, 500 ),
		);
	}

	return array(
		'success' => true,
		'error'   => '',
	);
}

/**
 * Send an email via configured transport.
 */
function aiddata_auth_send_mail( $to_email, $subject, $text_body ) {
	$transport = aiddata_auth_mail_transport();

	if ( 'resend' === $transport ) {
		return aiddata_auth_send_mail_via_resend( $to_email, $subject, $text_body );
	}
	if ( 'sendgrid' === $transport ) {
		return aiddata_auth_send_mail_via_sendgrid( $to_email, $subject, $text_body );
	}

	$mail_error_message = '';
	$mail_failed_hook   = static function( $wp_error ) use ( &$mail_error_message ) {
		if ( $wp_error instanceof WP_Error ) {
			$mail_error_message = $wp_error->get_error_message();
		}
	};

	add_action( 'wp_mail_failed', $mail_failed_hook );
	$sent = wp_mail( $to_email, $subject, $text_body );
	remove_action( 'wp_mail_failed', $mail_failed_hook );

	if ( $sent ) {
		return array(
			'success' => true,
			'error'   => '',
		);
	}

	if ( '' === $mail_error_message ) {
		$mail_error_message = 'wp_mail returned false';
	}

	return array(
		'success' => false,
		'error'   => $mail_error_message,
	);
}

/**
 * Build a unique username from an email address.
 */
function aiddata_auth_build_username_from_email( $email ) {
	$parts         = explode( '@', (string) $email );
	$base_username = sanitize_user( $parts[0], true );

	if ( '' === $base_username ) {
		$base_username = 'user';
	}

	$username = $base_username;
	$attempts = 0;

	while ( username_exists( $username ) && $attempts < 20 ) {
		++$attempts;
		$username = $base_username . '_' . wp_rand( 100, 999 );
	}

	if ( username_exists( $username ) ) {
		$username = 'user_' . wp_rand( 10000, 99999 );
	}

	return $username;
}

/**
 * Send a welcome email immediately whenever a user account is created.
 */
function aiddata_auth_send_welcome_email( $user_id ) {
	if ( get_user_meta( $user_id, 'aiddata_welcome_email_sent', true ) ) {
		return;
	}

	$user = get_userdata( $user_id );
	if ( ! $user || ! is_email( $user->user_email ) ) {
		return;
	}

	$display_name = $user->display_name ? $user->display_name : $user->user_login;
	$subject      = 'Welcome to AidData Training Hub';
	$message      = "Hi {$display_name},\n\n";
	$message     .= "Welcome to AidData Training Hub.\n\n";
	$message     .= "You can start learning from your dashboard here:\n";
	$message     .= home_url( '/lp-profile/' ) . "\n\n";
	$message     .= "If you did not create this account, please reply to this email.\n\n";
	$message     .= "AidData Training Hub Team";

	$mail_context = aiddata_auth_mail_config_context();
	error_log(
		'[aiddata-auth-email] welcome_email_attempt user_id=' . (int) $user_id . ' to=' . $user->user_email . ' context=' . wp_json_encode( $mail_context )
	);

	$result             = aiddata_auth_send_mail( $user->user_email, $subject, $message );
	$sent               = ! empty( $result['success'] );
	$mail_error_message = isset( $result['error'] ) ? (string) $result['error'] : '';

	if ( $sent ) {
		update_user_meta( $user_id, 'aiddata_welcome_email_sent', gmdate( 'c' ) );
		delete_user_meta( $user_id, 'aiddata_welcome_email_error' );
		error_log( '[aiddata-auth-email] welcome_email_sent user_id=' . (int) $user_id . ' to=' . $user->user_email );
		return;
	}

	if ( '' === $mail_error_message ) {
		$mail_error_message = 'wp_mail returned false';
	}

	update_user_meta( $user_id, 'aiddata_welcome_email_error', $mail_error_message );
	error_log(
		'[aiddata-auth-email] welcome_email_failed user_id=' . (int) $user_id . ' error=' . $mail_error_message . ' context=' . wp_json_encode( $mail_context )
	);
}
add_action( 'user_register', 'aiddata_auth_send_welcome_email', 20 );

/**
 * AJAX login handler.
 */
function aiddata_ajax_login() {
	aiddata_auth_login_debug_log(
		'request_received',
		array(
			'is_ssl'          => is_ssl(),
			'host'            => isset( $_SERVER['HTTP_HOST'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) ) : '',
			'forwarded_proto' => isset( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) ) : '',
			'has_username'    => isset( $_POST['username'] ),
			'has_password'    => isset( $_POST['password'] ),
			'has_nonce'       => isset( $_POST['security'] ),
		)
	);

	$nonce_ok = check_ajax_referer( 'custom-auth-nonce', 'security', false );
	if ( false === $nonce_ok ) {
		aiddata_auth_login_debug_log( 'nonce_failed' );
		wp_send_json_error( array( 'message' => 'Security check failed. Please refresh and try again.' ) );
	}

	$login_input = isset( $_POST['username'] ) ? sanitize_text_field( wp_unslash( $_POST['username'] ) ) : '';
	$password    = isset( $_POST['password'] ) ? wp_unslash( $_POST['password'] ) : '';

	if ( '' === $login_input || '' === $password ) {
		aiddata_auth_login_debug_log(
			'validation_failed',
			array(
				'reason'          => 'missing_credentials',
				'has_login_input' => '' !== $login_input,
				'has_password'    => '' !== $password,
			)
		);
		wp_send_json_error( array( 'message' => 'Username/email and password are required.' ) );
	}

	$login_type = is_email( $login_input ) ? 'email' : 'username';
	$login_hint = substr( $login_input, 0, 3 ) . '***';

	if ( is_email( $login_input ) ) {
		$user_by_email = get_user_by( 'email', $login_input );

		if ( $user_by_email instanceof WP_User ) {
			$login_input = $user_by_email->user_login;
			aiddata_auth_login_debug_log(
				'email_mapped_to_username',
				array(
					'login_hint' => $login_hint,
					'user_id'    => (int) $user_by_email->ID,
				)
			);
		} else {
			aiddata_auth_login_debug_log(
				'email_not_found',
				array(
					'login_hint' => $login_hint,
				)
			);
		}
	}

	$user = wp_signon(
		array(
			'user_login'    => $login_input,
			'user_password' => $password,
			'remember'      => true,
		),
		is_ssl()
	);

	if ( is_wp_error( $user ) ) {
		aiddata_auth_login_debug_log(
			'login_failed',
			array(
				'login_type'     => $login_type,
				'login_hint'     => $login_hint,
				'error_codes'    => $user->get_error_codes(),
				'error_messages' => $user->get_error_messages(),
			)
		);
		wp_send_json_error( array( 'message' => $user->get_error_message() ) );
	}

	aiddata_auth_login_debug_log(
		'login_success',
		array(
			'login_type' => $login_type,
			'login_hint' => $login_hint,
			'user_id'    => (int) $user->ID,
			'roles'      => (array) $user->roles,
		)
	);
	wp_send_json_success( array( 'message' => 'Login successful!' ) );
}
add_action( 'wp_ajax_nopriv_custom_ajax_login', 'aiddata_ajax_login' );

/**
 * AJAX registration handler.
 */
function aiddata_ajax_register() {
	$nonce_ok = check_ajax_referer( 'custom-auth-nonce', 'security', false );
	if ( false === $nonce_ok ) {
		wp_send_json_error( array( 'message' => 'Security check failed. Please refresh and try again.' ) );
	}

	$user_email = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$user_name  = isset( $_POST['fullName'] ) ? sanitize_text_field( wp_unslash( $_POST['fullName'] ) ) : '';
	$password   = isset( $_POST['password'] ) ? (string) wp_unslash( $_POST['password'] ) : '';

	if ( '' === $user_name ) {
		wp_send_json_error( array( 'message' => 'Full name is required.' ) );
	}

	if ( ! is_email( $user_email ) ) {
		wp_send_json_error( array( 'message' => 'Please enter a valid email address.' ) );
	}

	if ( strlen( $password ) < 8 ) {
		wp_send_json_error( array( 'message' => 'Password must be at least 8 characters long.' ) );
	}

	if ( email_exists( $user_email ) ) {
		wp_send_json_error( array( 'message' => 'This email address is already registered. Please log in instead.' ) );
	}

	$username = aiddata_auth_build_username_from_email( $user_email );

	$user_id = wp_insert_user(
		array(
			'user_login'   => $username,
			'user_email'   => $user_email,
			'user_pass'    => $password,
			'display_name' => $user_name,
			'first_name'   => $user_name,
			'role'         => get_option( 'default_role', 'subscriber' ),
		)
	);

	if ( is_wp_error( $user_id ) ) {
		wp_send_json_error( array( 'message' => $user_id->get_error_message() ) );
	}

	if ( ! empty( $_POST['organization'] ) ) {
		update_user_meta( $user_id, 'organization', sanitize_text_field( wp_unslash( $_POST['organization'] ) ) );
	}

	$newsletter = isset( $_POST['newsletter'] ) ? sanitize_text_field( wp_unslash( $_POST['newsletter'] ) ) : '';
	if ( 'on' === $newsletter ) {
		update_user_meta( $user_id, 'newsletter_subscription', 'yes' );
	}

	update_user_meta( $user_id, 'aiddata_registration_source', 'custom_ajax_register' );

	$user = wp_signon(
		array(
			'user_login'    => $username,
			'user_password' => $password,
			'remember'      => true,
		),
		is_ssl()
	);

	if ( is_wp_error( $user ) ) {
		wp_send_json_error( array( 'message' => 'Registration successful but there was an error logging you in. Please log in manually.' ) );
	}

	// Retry once if the first welcome email attempt failed during user_register.
	if ( ! get_user_meta( $user_id, 'aiddata_welcome_email_sent', true ) ) {
		aiddata_auth_send_welcome_email( $user_id );
	}

	$email_sent = (bool) get_user_meta( $user_id, 'aiddata_welcome_email_sent', true );
	if ( ! $email_sent ) {
		$email_error = (string) get_user_meta( $user_id, 'aiddata_welcome_email_error', true );
		$message     = 'Registration successful, but we could not send your welcome email yet. Please contact support if this continues.';

		if ( $email_error !== '' ) {
			error_log( '[aiddata-auth-email] registration_email_warning user_id=' . (int) $user_id . ' error=' . $email_error );
		}

		wp_send_json_success(
			array(
				'message'      => $message,
				'email_sent'   => false,
				'support_hint' => 'welcome_email_failed',
			)
		);
	}

	wp_send_json_success( array( 'message' => 'Registration successful!' ) );
}
add_action( 'wp_ajax_nopriv_custom_ajax_register', 'aiddata_ajax_register' );

/**
 * AJAX password reset handler.
 */
function aiddata_ajax_reset_password() {
	$nonce_ok = check_ajax_referer( 'custom-auth-nonce', 'security', false );
	if ( false === $nonce_ok ) {
		wp_send_json_error( array( 'message' => 'Security check failed. Please refresh and try again.' ) );
	}

	$email = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$user  = get_user_by( 'email', $email );

	if ( ! $user ) {
		wp_send_json_error( array( 'message' => 'No user found with that email address.' ) );
	}

	$key = get_password_reset_key( $user );
	if ( is_wp_error( $key ) ) {
		wp_send_json_error( array( 'message' => 'Error generating password reset link.' ) );
	}

	$reset_link = network_site_url( "wp-login.php?action=rp&key=$key&login=" . rawurlencode( $user->user_login ), 'login' );
	$subject    = 'Password Reset Request for AidData Training Hub';
	$message    = 'Hello ' . $user->display_name . ",\n\n";
	$message   .= "You requested a password reset for your AidData Training Hub account. Click the link below to set a new password:\n\n";
	$message   .= $reset_link . "\n\n";
	$message   .= "If you didn't request this, please ignore this email.\n\n";
	$message   .= "Thanks,\nAidData Training Hub Team";

	$result = aiddata_auth_send_mail( $email, $subject, $message );
	if ( ! empty( $result['success'] ) ) {
		wp_send_json_success( array( 'message' => 'Password reset link has been sent to your email address.' ) );
	}

	$error_message = isset( $result['error'] ) ? (string) $result['error'] : '';
	if ( '' !== $error_message ) {
		error_log( '[aiddata-auth-email] reset_email_failed email=' . $email . ' error=' . $error_message );
	}

	wp_send_json_error( array( 'message' => 'There was an error sending the email. Please try again later.' ) );
}
add_action( 'wp_ajax_nopriv_custom_ajax_reset_password', 'aiddata_ajax_reset_password' );

/**
 * AJAX logout handler.
 */
function aiddata_ajax_logout() {
	$nonce_ok = check_ajax_referer( 'custom-auth-nonce', 'security', false );
	if ( false === $nonce_ok ) {
		wp_send_json_error( array( 'message' => 'Security check failed. Please refresh and try again.' ) );
	}

	wp_logout();
	wp_send_json_success( array( 'message' => 'Logged out successfully' ) );
}
add_action( 'wp_ajax_custom_ajax_logout', 'aiddata_ajax_logout' );

/**
 * AJAX authentication status handler.
 */
function aiddata_get_auth_status() {
	$response = array(
		'loggedIn'  => is_user_logged_in(),
		'userName'  => '',
		'userEmail' => '',
		'isAdmin'   => false,
	);

	if ( is_user_logged_in() ) {
		$current_user          = wp_get_current_user();
		$response['userName']  = $current_user->display_name;
		$response['userEmail'] = $current_user->user_email;
		$response['isAdmin']   = current_user_can( 'manage_options' );
	}

	wp_send_json( $response );
}
add_action( 'wp_ajax_get_auth_status', 'aiddata_get_auth_status' );
add_action( 'wp_ajax_nopriv_get_auth_status', 'aiddata_get_auth_status' );
