<?php
/**
 * Helper to create nested pages for custom tutorial path and link tutorial
 * Visit: /setup-custom-tutorial-path.php
 */

define('WP_USE_THEMES', false);
require_once __DIR__ . '/wp-load.php';

header('Content-Type: text/html; charset=utf-8');

function esc_html_out($html) {
	echo esc_html($html);
}

echo '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Setup Custom Tutorial Path</title>';
echo '<style>body{font-family:Arial,sans-serif;max-width:820px;margin:40px auto;padding:20px;background:#f8fafc}';
echo '.card{background:#fff;padding:18px 20px;border-radius:8px;box-shadow:0 2px 10px rgba(0,0,0,.06);margin:14px 0}';
echo '.ok{color:#166534;background:#ecfdf5;padding:8px 12px;border-radius:6px;margin:8px 0;display:inline-block}';
echo '.warn{color:#92400e;background:#fffbeb;padding:8px 12px;border-radius:6px;margin:8px 0;display:inline-block}';
echo '.err{color:#991b1b;background:#fef2f2;padding:8px 12px;border-radius:6px;margin:8px 0;display:inline-block}';
echo '.btn{display:inline-block;background:#004E38;color:#fff;text-decoration:none;padding:10px 14px;border-radius:6px;margin:8px 8px 0 0}</style></head><body>';
echo '<h1>Setup Custom Tutorial Path</h1>';

// Desired path and tutorial mapping
$segments = array('t-h', 'china-aiddata-dashboard', 'global-development-finance-tutorial');
$tutorial_slug_source = 'global-development-finance'; // existing tutorial CPT slug

// Resolve tutorial by slug
$tutorial = get_page_by_path($tutorial_slug_source, OBJECT, 'aiddata_tutorial');
if (!$tutorial) {
	echo '<div class="card"><span class="err">Tutorial with slug "' . esc_html($tutorial_slug_source) . '" not found.</span></div>';
	echo '<p>Try visiting <code>/setup-tutorial-page.php</code> first to verify the tutorial exists.</p>';
	echo '</body></html>';
	exit;
}

$parent_id = 0;
$created_pages = array();
foreach ($segments as $index => $slug) {
	// See if a page with this slug under current parent exists
	$page = null;
	if ($parent_id) {
		$q = new WP_Query(array(
			'post_type' => 'page',
			'name' => $slug,
			'post_parent' => $parent_id,
			'posts_per_page' => 1,
		));
		if ($q->have_posts()) { $page = $q->posts[0]; }
		wp_reset_postdata();
	} else {
		$page = get_page_by_path($slug, OBJECT, 'page');
	}

	if ($page) {
		$parent_id = (int) $page->ID;
		$created_pages[] = array('id' => $page->ID, 'slug' => $slug, 'status' => 'exists');
		continue;
	}

	// Create page
	$page_id = wp_insert_post(array(
		'post_title' => ucwords(str_replace('-', ' ', $slug)),
		'post_name' => $slug,
		'post_type' => 'page',
		'post_status' => 'publish',
		'post_parent' => $parent_id,
	));

	if (is_wp_error($page_id) || !$page_id) {
		echo '<div class="card"><span class="err">Failed to create page: ' . esc_html($slug) . '</span></div>';
		echo '</body></html>';
		exit;
	}

	// If this is the final segment, set the page builder template and link tutorial
	if ($index === count($segments) - 1) {
		update_post_meta($page_id, '_wp_page_template', 'template-tutorial-page-builder.php');
		update_post_meta($page_id, '_tutorial_page_id', (int) $tutorial->ID);
	}

	$parent_id = (int) $page_id;
	$created_pages[] = array('id' => $page_id, 'slug' => $slug, 'status' => 'created');
}

// Flush permalinks
flush_rewrite_rules();

echo '<div class="card">';
echo '<h2>Result</h2>';
foreach ($created_pages as $info) {
	$link = get_permalink($info['id']);
	echo '<div class="' . ($info['status']==='created' ? 'ok' : 'warn') . '">';
	echo ($info['status']==='created' ? 'Created' : 'Exists') . ': ' . esc_html($info['slug']) . ' — ';
	echo '<a class="btn" target="_blank" href="' . esc_url($link) . '">Open</a>';
	echo '</div>';
}

$final_id = $parent_id;
$final_url = get_permalink($final_id);
echo '<div class="card"><h3>Final Path</h3>';
echo '<p><a class="btn" target="_blank" href="' . esc_url($final_url) . '">Open Tutorial Page</a></p>';
echo '<p>Expected URL: ' . esc_html(home_url('/' . implode('/', $segments) . '/')) . '</p>';
echo '</div>';

echo '</body></html>';


