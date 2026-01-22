<?php
/**
 * Diagnose Template Loading
 * This script helps identify what's causing the infinite loop
 */

// Start output buffering to catch any errors
ob_start();

define('WP_USE_THEMES', false);
require('./wp-load.php');

$tutorial_id = 227;
$post = get_post($tutorial_id);

?>
<!DOCTYPE html>
<html>
<head>
    <title>Template Loading Diagnosis</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; max-width: 1000px; margin: 0 auto; }
        .success { background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .error { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .info { background: #d1ecf1; color: #0c5460; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .warning { background: #fff3cd; color: #856404; padding: 15px; border-radius: 5px; margin: 10px 0; }
        code { background: #f4f4f4; padding: 2px 6px; border-radius: 3px; }
        pre { background: #f4f4f4; padding: 15px; border-radius: 5px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>🔍 Template Loading Diagnosis</h1>
    
    <h2>1. Tutorial Post Status</h2>
    <?php if ($post): ?>
        <div class="success">
            <strong>✓ Tutorial Found</strong><br>
            ID: <?php echo $post->ID; ?><br>
            Title: <?php echo esc_html($post->post_title); ?><br>
            Status: <?php echo esc_html($post->post_status); ?><br>
            Type: <?php echo esc_html($post->post_type); ?>
        </div>
    <?php else: ?>
        <div class="error">✗ Tutorial not found!</div>
    <?php endif; ?>
    
    <h2>2. Template Meta</h2>
    <?php
    $use_page_builder = get_post_meta($tutorial_id, '_use_page_builder_template', true);
    ?>
    <div class="info">
        <strong>_use_page_builder_template:</strong> 
        <?php if (empty($use_page_builder)): ?>
            <span style="color: orange;">NOT SET (will use classic template)</span>
        <?php else: ?>
            <span style="color: green;"><?php echo esc_html($use_page_builder); ?> (will use page builder)</span>
        <?php endif; ?>
    </div>
    
    <h2>3. Template Files</h2>
    <?php
    $theme_dir = get_template_directory();
    $single_template = $theme_dir . '/single-aiddata_tutorial.php';
    $page_builder_template = $theme_dir . '/template-tutorial-page-builder.php';
    ?>
    <div class="info">
        <strong>Theme Directory:</strong> <?php echo esc_html($theme_dir); ?><br><br>
        
        <strong>single-aiddata_tutorial.php:</strong> 
        <?php if (file_exists($single_template)): ?>
            <span style="color: green;">✓ EXISTS</span>
            (<?php echo number_format(filesize($single_template)); ?> bytes)
        <?php else: ?>
            <span style="color: red;">✗ NOT FOUND</span>
        <?php endif; ?><br>
        
        <strong>template-tutorial-page-builder.php:</strong> 
        <?php if (file_exists($page_builder_template)): ?>
            <span style="color: green;">✓ EXISTS</span>
            (<?php echo number_format(filesize($page_builder_template)); ?> bytes)
        <?php else: ?>
            <span style="color: red;">✗ NOT FOUND</span>
        <?php endif; ?>
    </div>
    
    <h2>4. Tutorial Steps</h2>
    <?php
    $steps = get_post_meta($tutorial_id, '_tutorial_steps', true);
    ?>
    <div class="info">
        <?php if (empty($steps)): ?>
            <div class="warning">
                <strong>⚠️ NO STEPS FOUND!</strong><br>
                The tutorial has no steps. This might cause issues when rendering.
            </div>
        <?php else: ?>
            <strong>Steps Count:</strong> <?php echo count($steps); ?><br>
            <details>
                <summary>View Steps Data</summary>
                <pre><?php print_r($steps); ?></pre>
            </details>
        <?php endif; ?>
    </div>
    
    <h2>5. Template Loading Test</h2>
    <div class="info">
        <p>Testing if the template can be loaded without errors...</p>
        <?php
        // Check if the constant is defined
        if (defined('AIDDATA_TUTORIAL_TEMPLATE_LOADED')):
        ?>
            <div class="warning">
                <strong>⚠️ Template Already Loaded!</strong><br>
                The AIDDATA_TUTORIAL_TEMPLATE_LOADED constant is already defined.
                This means the template was loaded before this diagnostic script ran.
            </div>
        <?php else: ?>
            <div class="success">
                <strong>✓ Template Not Yet Loaded</strong><br>
                The template guard is working correctly.
            </div>
        <?php endif; ?>
    </div>
    
    <h2>6. Recommended Actions</h2>
    <div class="info">
        <?php if (empty($use_page_builder)): ?>
            <div class="warning">
                <h3>⚠️ Template Not Set</h3>
                <p>The tutorial is not configured to use the Page Builder template.</p>
                <p><strong>To fix:</strong></p>
                <ol>
                    <li>Go to Tutorial Builder</li>
                    <li>Edit "Global Development Finance"</li>
                    <li>Set "Display Template" to "Page Builder Template (New)"</li>
                    <li>Click "Update Tutorial"</li>
                </ol>
            </div>
        <?php endif; ?>
        
        <?php if (empty($steps)): ?>
            <div class="warning">
                <h3>⚠️ No Steps</h3>
                <p>The tutorial has no steps defined.</p>
                <p><strong>To fix:</strong></p>
                <ol>
                    <li>Go to Tutorial Builder</li>
                    <li>Edit "Global Development Finance"</li>
                    <li>Add at least one step</li>
                    <li>Click "Update Tutorial"</li>
                </ol>
            </div>
        <?php endif; ?>
        
        <h3>✓ Try Accessing the Tutorial</h3>
        <p>
            <a href="<?php echo get_permalink($tutorial_id); ?>" target="_blank" style="display: inline-block; background: #004E38; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">
                View Tutorial
            </a>
        </p>
        <p style="margin-top: 10px;">
            <small>URL: <?php echo get_permalink($tutorial_id); ?></small>
        </p>
    </div>
    
    <h2>7. Debug Log Check</h2>
    <?php
    $debug_log = WP_CONTENT_DIR . '/debug.log';
    if (file_exists($debug_log)):
        $log_size = filesize($debug_log);
        $recent_errors = array_slice(file($debug_log), -10);
    ?>
        <div class="info">
            <strong>Debug Log:</strong> <?php echo number_format($log_size); ?> bytes<br>
            <details>
                <summary>Last 10 Lines</summary>
                <pre><?php echo esc_html(implode('', $recent_errors)); ?></pre>
            </details>
        </div>
    <?php else: ?>
        <div class="success">
            <strong>✓ No Debug Log</strong><br>
            No errors have been logged yet.
        </div>
    <?php endif; ?>
    
</body>
</html>
<?php
// Get any buffered output
$output = ob_get_clean();
echo $output;
?>

