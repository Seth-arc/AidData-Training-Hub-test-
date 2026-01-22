<?php
/**
 * Class LP_Addon_Auto_Certificates
 */
if (!defined('ABSPATH')) {
    exit;
}

class LP_Addon_Auto_Certificates extends LP_Addon {

    public $version = '4.0.0';
    public $require_version = '4.0.0'; // Require LearnPress 4.0.0+

    const TABLE = 'lp_auto_certs';
    const OPT_THRESHOLD = 'lpac_threshold';

    public function __construct() {
        parent::__construct();
    }

    protected function _define_constants() {
        // Define constants if needed
    }

    protected function _includes() {
        // Include other files if needed
    }

    protected function _init_hooks() {
        // LearnPress hook: quiz completed
        add_action('learn-press/quiz-completed', [$this, 'on_quiz_completed'], 10, 4);

        // AJAX: check popup + fetch cert
        add_action('wp_ajax_lpac_should_popup', [$this, 'ajax_should_popup']);
        add_action('wp_ajax_lpac_get_cert', [$this, 'ajax_get_cert']);

        // Public verification endpoint via shortcode
        add_shortcode('lpac_verify', [$this, 'shortcode_verify']);

        // View certificate endpoint
        add_action('template_redirect', [$this, 'handle_certificate_view']);
    }

    protected function _enqueue_assets() {
        if (!is_user_logged_in()) return;

        wp_register_script(
            'lpac-frontend',
            $this->get_plugin_url('assets/lpac-frontend.js'),
            ['jquery', 'wp-data', 'wp-element', 'wp-components'],
            $this->version,
            true
        );

        wp_localize_script('lpac-frontend', 'LPAC', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce'   => wp_create_nonce('lpac_nonce'),
        ]);

        wp_enqueue_script('lpac-frontend');
    }

    public function on_quiz_completed($quiz_id, $course_id, $user_id, $result) {
        $threshold = (int) get_option(self::OPT_THRESHOLD, 80);

        $percent = $this->extract_percent($result);

        if ($percent < $threshold) return;

        // Issue once per course per user
        $cert = $this->maybe_issue_certificate($user_id, $course_id, $quiz_id, $percent);
        if (!$cert) return;

        // Tell frontend to popup (5 min window)
        set_transient("lpac_cert_popup_{$user_id}", $cert['code'], 5 * MINUTE_IN_SECONDS);
    }

    private function extract_percent($result): float {
        // LearnPress commonly provides mark_percent (sometimes 0..1). Be defensive.
        if (is_array($result)) {
            if (isset($result['mark_percent'])) {
                $p = (float) $result['mark_percent'];
                return ($p <= 1.0) ? ($p * 100.0) : $p;
            }
            if (isset($result['percent'])) {
                $p = (float) $result['percent'];
                return ($p <= 1.0) ? ($p * 100.0) : $p;
            }
        }
        return 0.0;
    }

    private function maybe_issue_certificate($user_id, $course_id, $quiz_id, $percent) {
        global $wpdb;
        $table = $wpdb->prefix . self::TABLE;

        $existing = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$table} WHERE user_id=%d AND course_id=%d AND revoked_at IS NULL", $user_id, $course_id),
            ARRAY_A
        );
        if ($existing) return $existing;

        $code = wp_generate_password(24, false, false) . '-' . $user_id . '-' . $course_id;

        $ok = $wpdb->insert($table, [
            'code'      => $code,
            'user_id'   => (int) $user_id,
            'course_id' => (int) $course_id,
            'quiz_id'   => (int) $quiz_id,
            'percent'   => (float) $percent,
            'issued_at' => current_time('mysql'),
        ], ['%s','%d','%d','%d','%f','%s']);

        return $ok ? [
            'code' => $code,
            'user_id' => $user_id,
            'course_id' => $course_id,
            'quiz_id' => $quiz_id,
            'percent' => $percent,
        ] : null;
    }

    public function ajax_should_popup() {
        check_ajax_referer('lpac_nonce', 'nonce');

        $user_id = get_current_user_id();
        $code = get_transient("lpac_cert_popup_{$user_id}");
        if (!$code) wp_send_json_success(['show' => false]);

        // consume it so it doesn't popup forever
        delete_transient("lpac_cert_popup_{$user_id}");

        wp_send_json_success(['show' => true, 'code' => $code]);
    }

    public function ajax_get_cert() {
        check_ajax_referer('lpac_nonce', 'nonce');

        $user_id = get_current_user_id();
        $code = isset($_POST['code']) ? sanitize_text_field($_POST['code']) : '';

        global $wpdb;
        $table = $wpdb->prefix . self::TABLE;

        $row = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$table} WHERE code=%s AND user_id=%d AND revoked_at IS NULL", $code, $user_id),
            ARRAY_A
        );

        if (!$row) wp_send_json_error(['message' => 'Certificate not found.'], 404);

        $verify_url = add_query_arg(['lpac' => 'view', 'code' => $row['code']], home_url('/'));
        wp_send_json_success([
            'code' => $row['code'],
            'percent' => $row['percent'],
            'verifyUrl' => $verify_url,
        ]);
    }

    public function shortcode_verify($atts) {
        $code = isset($_GET['code']) ? sanitize_text_field($_GET['code']) : '';
        if (!$code) return '<p>Missing certificate code.</p>';

        global $wpdb;
        $table = $wpdb->prefix . self::TABLE;

        $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table} WHERE code=%s", $code), ARRAY_A);
        if (!$row || !empty($row['revoked_at'])) return '<p>Certificate is invalid or revoked.</p>';

        return sprintf(
            '<div><strong>Valid certificate</strong><br>Course ID: %d<br>User ID: %d<br>Score: %s%%<br>Issued: %s</div>',
            (int) $row['course_id'],
            (int) $row['user_id'],
            esc_html($row['percent']),
            esc_html($row['issued_at'])
        );
    }

    public function handle_certificate_view() {
        if (isset($_GET['lpac']) && $_GET['lpac'] === 'view' && !empty($_GET['code'])) {
            $code = sanitize_text_field($_GET['code']);
            global $wpdb;
            $table = $wpdb->prefix . self::TABLE;
            $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table} WHERE code=%s", $code), ARRAY_A);

            if ($row && empty($row['revoked_at'])) {
                // Fetch user and course info
                $user = get_userdata($row['user_id']);
                $course = get_post($row['course_id']);

                $args = [
                    'recipientName' => $user ? $user->display_name : 'Unknown User',
                    'courseName' => $course ? $course->post_title : 'Unknown Course',
                    'completionDate' => date('F j, Y', strtotime($row['issued_at'])),
                    'certificateId' => $row['code'],
                    'logoUrl' => content_url('plugins/learnpress/assets/images/aiddata_logodark.png')
                ];

                // Use get_template from LP_Addon to allow overriding
                $this->get_template('certificate_template.php', $args);
                exit;
            } else {
                wp_die('Certificate not found or revoked.', 'Certificate Error', ['response' => 404]);
            }
        }
    }

    public static function create_table() {
        global $wpdb;

        $table = $wpdb->prefix . self::TABLE;
        $charset = $wpdb->get_charset_collate();

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        $sql = "CREATE TABLE {$table} (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            code VARCHAR(64) NOT NULL,
            user_id BIGINT UNSIGNED NOT NULL,
            course_id BIGINT UNSIGNED NOT NULL,
            quiz_id BIGINT UNSIGNED NOT NULL,
            percent DECIMAL(5,2) NOT NULL,
            issued_at DATETIME NOT NULL,
            revoked_at DATETIME NULL,
            pdf_path TEXT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY code (code),
            UNIQUE KEY user_course (user_id, course_id)
        ) {$charset};";

        dbDelta($sql);

        add_option('lpac_threshold', 80);
    }
}
