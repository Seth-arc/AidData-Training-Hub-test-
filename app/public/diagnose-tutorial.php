<?php
/**
 * Tutorial Loading Diagnostic
 * 
 * This script diagnoses why the tutorial isn't loading
 * Visit: http://localhost:10016/diagnose-tutorial.php
 */

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html>
<html>
<head>
    <title>Tutorial Diagnostic</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; padding: 40px; background: #f5f5f5; }
        .container { max-width: 900px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #004E38; margin-top: 0; }
        h2 { color: #333; border-bottom: 2px solid #004E38; padding-bottom: 10px; margin-top: 30px; }
        .success { color: #2e7d32; background: #e8f5e9; padding: 10px; border-radius: 4px; margin: 10px 0; }
        .error { color: #c62828; background: #ffebee; padding: 10px; border-radius: 4px; margin: 10px 0; }
        .warning { color: #f57c00; background: #fff3e0; padding: 10px; border-radius: 4px; margin: 10px 0; }
        .info { color: #1976d2; background: #e3f2fd; padding: 10px; border-radius: 4px; margin: 10px 0; }
        pre { background: #f5f5f5; padding: 15px; border-radius: 4px; overflow-x: auto; }
        .btn { display: inline-block; background: #004E38; color: white; padding: 12px 24px; text-decoration: none; border-radius: 4px; margin: 10px 10px 10px 0; }
        .btn:hover { background: #04a971; }
        code { background: #f5f5f5; padding: 2px 6px; border-radius: 3px; font-family: 'Courier New', monospace; }
    </style>
</head>
<body>
<div class='container'>";

echo "<h1>🔍 Tutorial Loading Diagnostic</h1>";
echo "<p>Checking why the Global Development Finance tutorial isn't loading...</p>";

$tutorial_id = 227;
$issues_found = [];
$warnings_found = [];

// Test 1: Load WordPress
echo "<h2>1. WordPress Loading</h2>";
try {
    require_once(__DIR__ . '/wp-load.php');
    echo "<div class='success'>✓ WordPress loaded successfully</div>";
} catch (Exception $e) {
    echo "<div class='error'>✗ Failed to load WordPress: " . $e->getMessage() . "</div>";
    echo "</div></body></html>";
    exit;
}

// Test 2: Check if tutorial post exists
echo "<h2>2. Tutorial Post Check</h2>";
$tutorial_post = get_post($tutorial_id);
if ($tutorial_post) {
    echo "<div class='success'>✓ Tutorial post exists (ID: $tutorial_id)</div>";
    echo "<div class='info'>";
    echo "<strong>Title:</strong> " . esc_html($tutorial_post->post_title) . "<br>";
    echo "<strong>Status:</strong> " . esc_html($tutorial_post->post_status) . "<br>";
    echo "<strong>Type:</strong> " . esc_html($tutorial_post->post_type) . "<br>";
    echo "<strong>URL:</strong> <a href='" . get_permalink($tutorial_id) . "' target='_blank'>" . get_permalink($tutorial_id) . "</a>";
    echo "</div>";
    
    if ($tutorial_post->post_status !== 'publish') {
        $issues_found[] = "Tutorial status is '{$tutorial_post->post_status}' instead of 'publish'";
        echo "<div class='error'>✗ Tutorial is NOT published (status: {$tutorial_post->post_status})</div>";
    }
} else {
    $issues_found[] = "Tutorial post with ID $tutorial_id does not exist";
    echo "<div class='error'>✗ Tutorial post not found (ID: $tutorial_id)</div>";
}

// Test 3: Check AidData LMS plugin
echo "<h2>3. Plugin Check</h2>";
if (class_exists('AidData_LMS_Tutorial')) {
    echo "<div class='success'>✓ AidData LMS plugin is active</div>";
    
    // Try to instantiate tutorial
    try {
        $tutorial = new AidData_LMS_Tutorial($tutorial_id);
        if ($tutorial && method_exists($tutorial, 'get_id') && $tutorial->get_id()) {
            echo "<div class='success'>✓ Tutorial class instantiated successfully</div>";
            
            // Get tutorial data
            try {
                $steps = $tutorial->get_steps();
                $step_count = is_array($steps) ? count($steps) : 0;
                echo "<div class='info'>Tutorial has <strong>$step_count steps</strong></div>";
                
                if ($step_count === 0) {
                    $warnings_found[] = "Tutorial has no steps configured";
                    echo "<div class='warning'>⚠ Tutorial has no steps</div>";
                }
            } catch (Exception $e) {
                $issues_found[] = "Error getting tutorial steps: " . $e->getMessage();
                echo "<div class='error'>✗ Error getting steps: " . $e->getMessage() . "</div>";
            }
        } else {
            $issues_found[] = "Tutorial class failed to load properly";
            echo "<div class='error'>✗ Tutorial class instantiated but failed validation</div>";
        }
    } catch (Exception $e) {
        $issues_found[] = "Error instantiating tutorial: " . $e->getMessage();
        echo "<div class='error'>✗ Error creating tutorial object: " . $e->getMessage() . "</div>";
    }
} else {
    $issues_found[] = "AidData LMS plugin is not active or class not found";
    echo "<div class='error'>✗ AidData_LMS_Tutorial class not found - plugin may not be active</div>";
}

// Test 4: Check template files
echo "<h2>4. Template Files Check</h2>";
$theme_dir = get_template_directory();
$single_template = $theme_dir . '/single-aiddata_tutorial.php';
$page_builder_template = $theme_dir . '/template-tutorial-page-builder.php';

if (file_exists($single_template)) {
    echo "<div class='success'>✓ Single tutorial template exists</div>";
    echo "<div class='info'>Path: <code>$single_template</code></div>";
    
    // Check for syntax errors
    $content = file_get_contents($single_template);
    if (strpos($content, 'exit;') !== false) {
        echo "<div class='success'>✓ Template has correct exit statement</div>";
    } else {
        $warnings_found[] = "Single template may not have correct exit statement";
        echo "<div class='warning'>⚠ Template may not have correct exit statement</div>";
    }
} else {
    $issues_found[] = "Single tutorial template file not found";
    echo "<div class='error'>✗ Single template not found: $single_template</div>";
}

if (file_exists($page_builder_template)) {
    echo "<div class='success'>✓ Page builder template exists</div>";
    echo "<div class='info'>Path: <code>$page_builder_template</code></div>";
} else {
    $issues_found[] = "Page builder template file not found";
    echo "<div class='error'>✗ Page builder template not found: $page_builder_template</div>";
}

// Test 5: Check template meta
echo "<h2>5. Template Configuration</h2>";
$use_page_builder = get_post_meta($tutorial_id, '_use_page_builder_template', true);
echo "<div class='info'>";
echo "<strong>Meta Key:</strong> _use_page_builder_template<br>";
echo "<strong>Meta Value:</strong> " . var_export($use_page_builder, true) . "<br>";
echo "<strong>Template:</strong> ";
if ($use_page_builder === '1') {
    echo "Page Builder Template (New)";
} else {
    echo "Classic Template (Default)";
}
echo "</div>";

// Test 6: Check for PHP errors in template
echo "<h2>6. Template Syntax Check</h2>";
if (file_exists($single_template)) {
    $output = [];
    $return_var = 0;
    exec("php -l " . escapeshellarg($single_template) . " 2>&1", $output, $return_var);
    
    if ($return_var === 0) {
        echo "<div class='success'>✓ Single template has no syntax errors</div>";
    } else {
        $issues_found[] = "Single template has syntax errors";
        echo "<div class='error'>✗ Single template has syntax errors:</div>";
        echo "<pre>" . implode("\n", $output) . "</pre>";
    }
}

if (file_exists($page_builder_template)) {
    $output = [];
    $return_var = 0;
    exec("php -l " . escapeshellarg($page_builder_template) . " 2>&1", $output, $return_var);
    
    if ($return_var === 0) {
        echo "<div class='success'>✓ Page builder template has no syntax errors</div>";
    } else {
        $issues_found[] = "Page builder template has syntax errors";
        echo "<div class='error'>✗ Page builder template has syntax errors:</div>";
        echo "<pre>" . implode("\n", $output) . "</pre>";
    }
}

// Test 7: Check permalink structure
echo "<h2>7. Permalink Structure</h2>";
$permalink_structure = get_option('permalink_structure');
if (!empty($permalink_structure)) {
    echo "<div class='success'>✓ Permalinks are configured</div>";
    echo "<div class='info'>Structure: <code>$permalink_structure</code></div>";
} else {
    $warnings_found[] = "Permalinks not configured (using default)";
    echo "<div class='warning'>⚠ Using default permalinks - may cause issues</div>";
}

// Test 8: Check debug.log for errors
echo "<h2>8. Recent Errors</h2>";
$debug_log = WP_CONTENT_DIR . '/debug.log';
if (file_exists($debug_log)) {
    $log_content = file_get_contents($debug_log);
    $recent_lines = array_slice(explode("\n", $log_content), -20);
    $tutorial_errors = array_filter($recent_lines, function($line) use ($tutorial_id) {
        return strpos($line, 'Tutorial') !== false || strpos($line, (string)$tutorial_id) !== false;
    });
    
    if (!empty($tutorial_errors)) {
        echo "<div class='warning'>⚠ Found recent tutorial-related errors:</div>";
        echo "<pre>" . implode("\n", $tutorial_errors) . "</pre>";
    } else {
        echo "<div class='success'>✓ No recent tutorial errors in debug log</div>";
    }
} else {
    echo "<div class='info'>ℹ Debug log not found (debugging may be disabled)</div>";
}

// Summary
echo "<h2>📊 Diagnostic Summary</h2>";

if (empty($issues_found) && empty($warnings_found)) {
    echo "<div class='success'>";
    echo "<h3>✓ No Issues Found!</h3>";
    echo "<p>The tutorial should be loading. Try these steps:</p>";
    echo "<ol>";
    echo "<li>Clear your browser cache (Ctrl+Shift+Delete)</li>";
    echo "<li>Try viewing in incognito/private mode</li>";
    echo "<li>Check browser console (F12) for JavaScript errors</li>";
    echo "</ol>";
    echo "</div>";
} else {
    if (!empty($issues_found)) {
        echo "<div class='error'>";
        echo "<h3>❌ Critical Issues Found:</h3>";
        echo "<ul>";
        foreach ($issues_found as $issue) {
            echo "<li>$issue</li>";
        }
        echo "</ul>";
        echo "</div>";
    }
    
    if (!empty($warnings_found)) {
        echo "<div class='warning'>";
        echo "<h3>⚠ Warnings:</h3>";
        echo "<ul>";
        foreach ($warnings_found as $warning) {
            echo "<li>$warning</li>";
        }
        echo "</ul>";
        echo "</div>";
    }
}

// Action buttons
echo "<h2>🔧 Quick Actions</h2>";

// Publish tutorial button
if ($tutorial_post && $tutorial_post->post_status !== 'publish') {
    echo "<a href='?action=publish' class='btn'>Publish Tutorial</a>";
}

// Apply template button
echo "<a href='?action=apply_template' class='btn'>Apply Page Builder Template</a>";

// Flush permalinks button
echo "<a href='?action=flush_permalinks' class='btn'>Flush Permalinks</a>";

// View tutorial button
if ($tutorial_post) {
    echo "<a href='" . get_permalink($tutorial_id) . "' target='_blank' class='btn'>View Tutorial</a>";
}

// Handle actions
if (isset($_GET['action'])) {
    echo "<div style='margin-top: 20px;'>";
    
    switch ($_GET['action']) {
        case 'publish':
            wp_update_post(['ID' => $tutorial_id, 'post_status' => 'publish']);
            echo "<div class='success'>✓ Tutorial published! <a href='?'>Refresh page</a></div>";
            break;
            
        case 'apply_template':
            update_post_meta($tutorial_id, '_use_page_builder_template', '1');
            echo "<div class='success'>✓ Page Builder template applied! <a href='?'>Refresh page</a></div>";
            break;
            
        case 'flush_permalinks':
            flush_rewrite_rules();
            echo "<div class='success'>✓ Permalinks flushed! <a href='?'>Refresh page</a></div>";
            break;
    }
    
    echo "</div>";
}

echo "</div></body></html>";

