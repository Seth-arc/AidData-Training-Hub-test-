<?php

declare(strict_types=1);

define( 'ABSPATH', dirname( __DIR__ ) . DIRECTORY_SEPARATOR );

require_once __DIR__ . '/../includes/class-compat.php';

$cases = array(
	array( 'starts_with', 'assets/images/example.png', 'assets/', true ),
	array( 'starts_with', '/courses/example/', '/', true ),
	array( 'starts_with', 'courses/example/', '/', false ),
	array( 'ends_with', '/themes/aiddata/front-page.php', '/front-page.php', true ),
	array( 'ends_with', '/themes/aiddata/index.php', '/front-page.php', false ),
	array( 'ends_with', '', '', true ),
);

foreach ( $cases as $case ) {
	list( $method, $haystack, $needle, $expected ) = $case;

	$actual = AidData_Home_Catalog_Compat::$method( $haystack, $needle );

	if ( $actual !== $expected ) {
		fwrite(
			STDERR,
			sprintf(
				"Compatibility assertion failed for %s(%s, %s). Expected %s, got %s.\n",
				$method,
				var_export( $haystack, true ),
				var_export( $needle, true ),
				var_export( $expected, true ),
				var_export( $actual, true )
			)
		);
		exit( 1 );
	}
}

fwrite( STDOUT, "AidData home catalog compatibility smoke test passed\n" );
