<?php
/**
 * Simple Phase 2 File Validation
 *
 * Checks if all required Phase 2 files exist without requiring database access.
 * Can be run from command line without WordPress bootstrap.
 *
 * Usage: php validate-phase-2-files.php
 *
 * @package AidData_LMS
 * @subpackage Admin
 * @since 2.0.0
 */

// Determine plugin directory
$plugin_dir = dirname( dirname( __DIR__ ) );

// Color output for CLI
function cli_color( $text, $color = 'white' ) {
	$colors = array(
		'black'  => '30',
		'red'    => '31',
		'green'  => '32',
		'yellow' => '33',
		'blue'   => '34',
		'purple' => '35',
		'cyan'   => '36',
		'white'  => '37',
	);

	if ( php_sapi_name() === 'cli' && isset( $colors[ $color ] ) ) {
		return "\033[" . $colors[ $color ] . 'm' . $text . "\033[0m";
	}

	return $text;
}

/**
 * Check if file exists
 */
function check_file( $path, $name ) {
	global $plugin_dir;
	$full_path = $plugin_dir . '/' . $path;
	$exists = file_exists( $full_path );

	$icon = $exists ? cli_color( '✓', 'green' ) : cli_color( '✗', 'red' );
	$status = $exists ? cli_color( 'PASS', 'green' ) : cli_color( 'FAIL', 'red' );

	printf( "  %s  %-60s  %s\n", $icon, $name, $status );

	return $exists;
}

/**
 * Print section header
 */
function print_section( $title ) {
	echo "\n";
	echo str_repeat( '─', 80 ) . "\n";
	echo "  " . cli_color( $title, 'cyan' ) . "\n";
	echo str_repeat( '─', 80 ) . "\n";
}

/**
 * Run validation
 */
function run_validation() {
	global $plugin_dir;

	$total_files = 0;
	$existing_files = 0;

	// Header
	echo "\n";
	echo str_repeat( '=', 80 ) . "\n";
	echo "        " . cli_color( 'PHASE 2 FILE VALIDATION', 'yellow' ) . "\n";
	echo str_repeat( '=', 80 ) . "\n";
	echo "Date: " . date( 'Y-m-d H:i:s' ) . "\n";
	echo "Plugin Directory: " . $plugin_dir . "\n";
	echo str_repeat( '=', 80 ) . "\n";

	// Tutorial Builder Files
	print_section( 'Tutorial Builder (Prompt 1)' );
	$total_files++;
	$existing_files += check_file( 'includes/admin/class-aiddata-lms-tutorial-meta-boxes.php', 'Meta Boxes Class' ) ? 1 : 0;
	$total_files++;
	$existing_files += check_file( 'includes/admin/views/tutorial-step-builder.php', 'Step Builder View' ) ? 1 : 0;
	$total_files++;
	$existing_files += check_file( 'includes/admin/views/step-item.php', 'Step Item Template' ) ? 1 : 0;
	$total_files++;
	$existing_files += check_file( 'assets/js/admin/tutorial-step-builder.js', 'Step Builder JavaScript' ) ? 1 : 0;
	$total_files++;
	$existing_files += check_file( 'assets/css/admin/tutorial-meta-boxes.css', 'Meta Boxes CSS' ) ? 1 : 0;

	// Admin List Interface Files
	print_section( 'Admin List Interface (Prompt 2)' );
	$total_files++;
	$existing_files += check_file( 'includes/admin/class-aiddata-lms-tutorial-list-table.php', 'List Table Handler Class' ) ? 1 : 0;
	$total_files++;
	$existing_files += check_file( 'assets/css/admin/tutorial-list.css', 'List Table CSS' ) ? 1 : 0;
	$total_files++;
	$existing_files += check_file( 'assets/js/admin/tutorial-list.js', 'List Table JavaScript' ) ? 1 : 0;

	// Frontend Display Files
	print_section( 'Frontend Display (Prompt 3)' );
	$total_files++;
	$existing_files += check_file( 'templates/archive-aiddata_tutorial.php', 'Archive Template' ) ? 1 : 0;
	$total_files++;
	$existing_files += check_file( 'templates/single-aiddata_tutorial.php', 'Single Template' ) ? 1 : 0;
	$total_files++;
	$existing_files += check_file( 'templates/template-parts/content-tutorial-card.php', 'Tutorial Card Template' ) ? 1 : 0;
	$total_files++;
	$existing_files += check_file( 'templates/template-parts/enrollment-button.php', 'Enrollment Button Template' ) ? 1 : 0;
	$total_files++;
	$existing_files += check_file( 'assets/css/frontend/tutorial-display.css', 'Frontend Display CSS' ) ? 1 : 0;

	// Progress Persistence Files
	print_section( 'Progress Persistence (Prompt 4)' );
	$total_files++;
	$existing_files += check_file( 'includes/tutorials/class-aiddata-lms-tutorial-progress.php', 'Progress Tracking Class' ) ? 1 : 0;
	$total_files++;
	$existing_files += check_file( 'includes/tutorials/class-aiddata-lms-progress-milestones.php', 'Progress Milestones Class' ) ? 1 : 0;

	// Active Tutorial Navigation Files
	print_section( 'Active Tutorial Navigation (Prompt 5)' );
	$total_files++;
	$existing_files += check_file( 'templates/template-parts/active-tutorial.php', 'Active Tutorial Template' ) ? 1 : 0;
	$total_files++;
	$existing_files += check_file( 'includes/tutorials/class-aiddata-lms-step-renderer.php', 'Step Renderer Class' ) ? 1 : 0;
	$total_files++;
	$existing_files += check_file( 'includes/tutorials/class-aiddata-lms-tutorial-ajax.php', 'Tutorial AJAX Handler' ) ? 1 : 0;
	$total_files++;
	$existing_files += check_file( 'assets/js/frontend/tutorial-navigation.js', 'Navigation JavaScript' ) ? 1 : 0;
	$total_files++;
	$existing_files += check_file( 'assets/css/frontend/tutorial-navigation.css', 'Navigation CSS' ) ? 1 : 0;

	// Supporting Files
	print_section( 'Supporting Classes and Integration' );
	$total_files++;
	$existing_files += check_file( 'includes/tutorials/class-aiddata-lms-tutorial-enrollment.php', 'Enrollment Manager Class' ) ? 1 : 0;
	$total_files++;
	$existing_files += check_file( 'includes/class-aiddata-lms-post-types.php', 'Post Types Class' ) ? 1 : 0;
	$total_files++;
	$existing_files += check_file( 'includes/class-aiddata-lms-taxonomies.php', 'Taxonomies Class' ) ? 1 : 0;
	$total_files++;
	$existing_files += check_file( 'includes/class-aiddata-lms-frontend-assets.php', 'Frontend Assets Class' ) ? 1 : 0;

	// Validation Files
	print_section( 'Validation System' );
	$total_files++;
	$existing_files += check_file( 'includes/admin/class-aiddata-lms-phase-2-validation.php', 'Phase 2 Validation Class' ) ? 1 : 0;
	$total_files++;
	$existing_files += check_file( 'includes/admin/class-aiddata-lms-admin-validation-page.php', 'Admin Validation Page Class' ) ? 1 : 0;
	$total_files++;
	$existing_files += check_file( 'includes/admin/views/phase-2-validation.php', 'Validation Page View' ) ? 1 : 0;
	$total_files++;
	$existing_files += check_file( 'includes/admin/run-phase-2-validation.php', 'CLI Validation Runner' ) ? 1 : 0;

	// Calculate stats
	$pass_rate = round( ( $existing_files / $total_files ) * 100, 2 );

	// Summary
	echo "\n";
	echo str_repeat( '=', 80 ) . "\n";
	echo "                           " . cli_color( 'SUMMARY', 'yellow' ) . "\n";
	echo str_repeat( '=', 80 ) . "\n";
	printf( "  Total Files Checked:    %d\n", $total_files );
	printf( "  Files Found:            %s%d%s\n", cli_color( '', 'green' ), $existing_files, "\033[0m" );
	printf( "  Files Missing:          %s%d%s\n", cli_color( '', 'red' ), $total_files - $existing_files, "\033[0m" );

	if ( $pass_rate >= 90 ) {
		printf( "  Completion Rate:        %s%.2f%%%s\n", cli_color( '', 'green' ), $pass_rate, "\033[0m" );
		echo "\n" . cli_color( '✓ EXCELLENT! All critical Phase 2 files are present.', 'green' ) . "\n";
	} elseif ( $pass_rate >= 75 ) {
		printf( "  Completion Rate:        %s%.2f%%%s\n", cli_color( '', 'yellow' ), $pass_rate, "\033[0m" );
		echo "\n" . cli_color( '⚠ GOOD PROGRESS. Some files are missing.', 'yellow' ) . "\n";
	} else {
		printf( "  Completion Rate:        %s%.2f%%%s\n", cli_color( '', 'red' ), $pass_rate, "\033[0m" );
		echo "\n" . cli_color( '✗ ACTION REQUIRED. Several critical files are missing.', 'red' ) . "\n";
	}

	echo str_repeat( '=', 80 ) . "\n";
	echo "\n";

	// Next steps
	if ( $existing_files === $total_files ) {
		echo "Next Step: Run full validation with WordPress loaded:\n";
		echo "  Admin: WordPress Admin → Tutorials → Phase 2 Validation\n";
		echo "  CLI:   wp eval-file includes/admin/run-phase-2-validation.php\n";
	} else {
		echo "Next Step: Complete Phase 2 implementation to create missing files.\n";
	}

	echo "\n";

	// Return exit code
	return ( $existing_files === $total_files ) ? 0 : 1;
}

// Run validation
$exit_code = run_validation();
exit( $exit_code );

