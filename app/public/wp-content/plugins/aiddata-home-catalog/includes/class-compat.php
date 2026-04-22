<?php

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

/**
 * Runtime-compatible helpers for PHP string handling.
 *
 * Keep these helpers free of WordPress dependencies so they can also be used
 * by the repo-local smoke test.
 */
final class AidData_Home_Catalog_Compat {
	/**
	 * No instances.
	 */
	private function __construct() {
	}

	/**
	 * PHP 7-compatible replacement for str_starts_with().
	 */
	public static function starts_with( string $haystack, string $needle ): bool {
		if ( '' === $needle ) {
			return true;
		}

		return 0 === strpos( $haystack, $needle );
	}

	/**
	 * PHP 7-compatible replacement for str_ends_with().
	 */
	public static function ends_with( string $haystack, string $needle ): bool {
		if ( '' === $needle ) {
			return true;
		}

		$needle_length = strlen( $needle );

		if ( $needle_length > strlen( $haystack ) ) {
			return false;
		}

		return substr( $haystack, -$needle_length ) === $needle;
	}
}
