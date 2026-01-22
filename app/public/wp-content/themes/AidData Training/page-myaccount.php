<?php
/**
 * Template Name: LifterLMS My Account Dashboard
 * Description: Profile dashboard page for AidData LMS styled like the user dashboard
 *
 * @package WordPress
 * @subpackage Twenty_Twenty_Four
 * @since Twenty Twenty-Four 1.0
 */

get_header();

// Enqueue authentication-specific styles
wp_enqueue_style('auth-styles', get_template_directory_uri() . '/assets/css/auth-styles.css', array(), '1.0.0');
wp_enqueue_style('loading-screen', get_template_directory_uri() . '/assets/css/loading-screen.css', array(), '1.0.0');

// Check if user is logged in
if (!is_user_logged_in()) {
    wp_redirect(wp_login_url(get_permalink()));
    exit;
}

$current_user = wp_get_current_user();
$account_page_url = function_exists('llms_get_page_url') ? llms_get_page_url('myaccount') : home_url('/');
$account_settings_url = function_exists('llms_get_endpoint_url') ? llms_get_endpoint_url('edit-account', '', $account_page_url) : $account_page_url;
$courses_url = function_exists('llms_get_endpoint_url') ? llms_get_endpoint_url('view-courses', '', $account_page_url) : $account_page_url;
$certificates_url = function_exists('llms_get_endpoint_url') ? llms_get_endpoint_url('view-certificates', '', $account_page_url) : $account_page_url;
?>

<!-- Scrollbar Styling -->
<style>
    /* Import Inter font from Google Fonts */
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
    
    /* Global font family override for all elements */
    *, *::before, *::after {
        font-family: 'Inter', sans-serif !important;
    }
    
    /* Additional specific overrides for common elements */
    body, html, div, span, h1, h2, h3, h4, h5, h6, p, a, button, input, textarea, select, label {
        font-family: 'Inter', sans-serif !important;
    }
    
    /* CSS Variables */
    :root {
        --primary-color: #115740;
    }
    
    /* Webkit browsers (Chrome, Safari, newer versions of Opera and Edge) */
    ::-webkit-scrollbar {
        width: 8px;
    }
    
    ::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }
    
    ::-webkit-scrollbar-thumb {
        background: #115740;
        border-radius: 4px;
    }
    
    ::-webkit-scrollbar-thumb:hover {
        background: #0a3d2c;
    }
    
    /* Firefox */
    * {
        scrollbar-width: thin;
        scrollbar-color: #115740 #f1f1f1;
        font-family: 'Inter', sans-serif !important;
    }
    
    body, html {
        margin: 0;
        padding: 0;
        background: #ffffff;
        min-height: 100vh;
        color: #1a1a1a;
        line-height: 1.5;
        letter-spacing: -0.01em;
        font-family: 'Inter', sans-serif !important;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }
    
    /* Hide WordPress default header */
    .site-header, #masthead, header.wp-block-template-part {
        display: none !important;
    }
    
    /* Remove any hr elements or horizontal lines */
    hr {
        display: none !important;
    }
    
    /* Exception for footer hr */
    .site-footer hr {
        display: block !important;
        border: 0;
        height: 1px;
        background-color: white;
        width: 100%;
        margin: 20px 0;
    }
    
    /* Remove any potential border/line elements */
    .wp-block-separator,
    .wp-block-spacer,
    .has-border,
    .entry-header::after,
    .site-header::after,
    .header::after {
        display: none !important;
    }
    
    /* Dashboard Header - Exact match to front-page.php */
    .lms-header {
        background: rgba(255, 255, 255, 0.98);
        position: fixed;
        width: 100%;
        top: 0;
        z-index: 1000;
        padding: 1.5rem 0;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        backdrop-filter: blur(10px);
        margin: 0;
        border: none;
    }
    
    .header-content {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 4rem;
        display: grid;
        grid-template-columns: auto 1fr;
        gap: 4rem;
        align-items: center;
    }
    
    .logo-section {
        display: flex;
        align-items: center;
        gap: 2rem;
    }
    
    .logo {
        height: 30px;
        width: auto;
        opacity: 0.95;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        filter: drop-shadow(0 1px 2px rgba(17, 87, 64, 0.1));
    }
    
    .logo:hover {
        opacity: 1;
        transform: translateY(-1px);
    }
    
    .header-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        margin-left: auto;
        gap: 1rem;
    }
    
    .header-icons {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        position: relative;
    }
    
    .header-button {
        width: 40px;
        height: 40px;
        border: none;
        background: transparent;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        color: #333;
        position: relative;
    }
    
    .header-button svg {
        width: 20px;
        height: 20px;
    }
    
    .header-button:hover {
        background: rgba(17, 87, 64, 0.08);
        transform: translateY(-1px);
    }
    
    .notification-badge {
        position: absolute;
        top: 8px;
        right: 8px;
        width: 8px;
        height: 8px;
        background: #dc3545;
        border-radius: 50%;
        display: none;
    }
    
    .notification-badge.active {
        display: block;
    }
    
    .profile-dropdown {
        position: absolute;
        top: calc(100% + 10px);
        right: 0;
        background: white;
        border-radius: 12px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
        min-width: 280px;
        padding: 0;
        opacity: 0;
        visibility: hidden;
        transform: translateY(-10px);
        transition: all 0.3s ease;
        border: 1px solid rgba(0, 0, 0, 0.08);
    }
    
    .profile-dropdown.show {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }
    
    .dropdown-header {
        padding: 20px;
        border-bottom: 1px solid #f1f3f5;
    }
    
    .dropdown-user-info .user-name {
        font-weight: 600;
        font-size: 16px;
        color: #333;
        display: block;
        margin-bottom: 4px;
    }
    
    .dropdown-user-info .user-email {
        font-size: 14px;
        color: #6c757d;
    }
    
    .dropdown-item {
        display: block;
        padding: 12px 20px;
        color: #333;
        text-decoration: none;
        font-size: 14px;
        transition: all 0.2s ease;
        border: none;
        background: none;
        width: 100%;
        text-align: left;
        cursor: pointer;
    }
    
    .dropdown-item:hover {
        background: #f8f9fa;
        color: #115740;
    }
    
    .dropdown-item.logout-button {
        color: #dc3545;
        border-top: 1px solid #f1f3f5;
    }
    
    .dropdown-item.logout-button:hover {
        background: rgba(220, 53, 69, 0.05);
    }
    
    /* Dashboard Hero - Aligned with front-page welcome section */
    .dashboard-hero {
        margin-top: 0;
        padding: calc(90px + 2rem) 2rem 4rem; /* Reduced bottom padding for shorter height */
        position: relative;
        overflow: hidden;
        flex-shrink: 0;
        background: linear-gradient(135deg, 
            rgba(17, 87, 64, 0.02) 0%,
            rgba(26, 128, 95, 0.06) 100%
        );
        font-family: 'Inter', sans-serif !important;
    }
    
    .dashboard-hero-content {
        position: relative;
        z-index: 100;
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 2rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
    }
    
    .hero-top {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 2rem;
        margin-bottom: 3rem;
        flex-wrap: wrap;
    }
    
    .user-info {
        flex: 1;
        min-width: 300px;
        font-family: 'Inter', sans-serif !important;
    }
    
    .user-info h1 {
        font-size: clamp(2.5rem, 5vw, 3.5rem);
        font-weight: 400;
        margin: 0 0 0.5rem 0;
        letter-spacing: -0.03em;
        color: #1a1a1a;
        line-height: 1.2;
        font-family: 'Inter', sans-serif !important;
        transform: translateY(20px);
        opacity: 0;
        animation: fadeInUp 0.6s 0.2s ease forwards;
    }
    
    .user-info .user-email {
        font-size: clamp(1rem, 2vw, 1.2rem);
        color: #666;
        margin: 0;
        font-weight: 300;
        font-family: 'Inter', sans-serif !important;
        letter-spacing: -0.01em;
        transform: translateY(20px);
        opacity: 0;
        animation: fadeInUp 0.6s 0.4s ease forwards;
    }
    
    .user-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 3rem;
        max-width: 600px;
        margin: 0 auto 3rem;
        transform: translateY(20px);
        opacity: 0;
        animation: fadeInUp 0.6s 0.6s ease forwards;
    }
    
    .stat-item {
        text-align: center;
        position: relative;
    }
    
    .stat-number {
        font-size: 3rem;
        font-weight: 200;
        display: block;
        line-height: 1;
        color: #115740;
        margin-bottom: 0.5rem;
        font-family: 'Inter', sans-serif !important;
    }
    
    .stat-label {
        font-size: 0.9rem;
        color: #666;
        font-weight: 400;
        text-transform: none;
        letter-spacing: 0;
        line-height: 1.3;
        font-family: 'Inter', sans-serif !important;
    }
    
    /* Animated shapes matching front-page */
    .dashboard-shapes {
        position: absolute;
        inset: 0;
        z-index: 1;
        overflow: hidden;
    }
    
    .dashboard-shape {
        position: absolute;
        clip-path: polygon(50% 0%, 0% 100%, 100% 100%);
        transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .dashboard-shape-1 {
        width: 400px;
        height: 400px;
        background: linear-gradient(135deg, rgba(17, 87, 64, 0.04), rgba(26, 128, 95, 0.06));
        top: -200px;
        left: -100px;
        transform: rotate(15deg);
        animation: floatTriangle1 20s infinite;
    }
    
    .dashboard-shape-2 {
        width: 300px;
        height: 300px;
        background: linear-gradient(135deg, rgba(26, 128, 95, 0.03), rgba(17, 87, 64, 0.05));
        top: 50%;
        right: -150px;
        transform: rotate(-45deg);
        animation: floatTriangle2 25s infinite reverse;
    }
    
    .dashboard-shape-3 {
        width: 200px;
        height: 200px;
        background: linear-gradient(135deg, rgba(17, 87, 64, 0.02), rgba(26, 128, 95, 0.04));
        bottom: -100px;
        left: 50%;
        transform: translateX(-50%) rotate(30deg);
        animation: floatTriangle3 30s infinite;
    }
    
    @keyframes fadeInUp {
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    @keyframes floatTriangle1 {
        0%, 100% { 
            transform: rotate(15deg) translateY(0px) translateX(0px); 
        }
        33% { 
            transform: rotate(25deg) translateY(-20px) translateX(10px); 
        }
        66% { 
            transform: rotate(10deg) translateY(-10px) translateX(-15px); 
        }
    }
    
    @keyframes floatTriangle2 {
        0%, 100% { 
            transform: rotate(-45deg) translateY(0px) translateX(0px); 
        }
        50% { 
            transform: rotate(-35deg) translateY(-15px) translateX(-20px); 
        }
    }
    
    @keyframes floatTriangle3 {
        0%, 100% { 
            transform: translateX(-50%) rotate(30deg) translateY(0px); 
        }
        50% { 
            transform: translateX(-50%) rotate(40deg) translateY(-10px); 
        }
    }
    
    /* Dashboard Content */
    .dashboard-content {
        margin-top: 0;
        padding: 0;
        min-height: 100vh;
        padding-top: 0; /* Remove all top padding */
        font-family: 'Inter', sans-serif !important;
    }
    
    /* Main Dashboard Grid */
    .dashboard-main {
        max-width: 1200px;
        margin: 0 auto;
        padding: 3rem 2rem;
    }
    
    .dashboard-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 3rem;
        margin-bottom: 4rem;
    }
    
    .dashboard-section {
        background: #ffffff;
        border: 1px solid #f0f0f0;
        padding: 2rem;
        transition: border-color 0.2s ease;
    }
    
    .dashboard-section:hover {
        border-color: #e0e0e0;
    }
    
    .section-header {
        margin-bottom: 2rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #f0f0f0;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    
    .section-title {
        font-size: 1.125rem;
        font-weight: 500;
        color: #1a1a1a;
        margin: 0;
        font-family: 'Inter', sans-serif !important;
    }
    
    .section-action {
        color: #115740;
        text-decoration: none;
        font-weight: 400;
        font-size: 0.875rem;
        transition: color 0.2s ease;
    }
    
    .section-action:hover {
        color: #0a3d2c;
    }
    
    /* Progress Items */
    .progress-item {
        padding: 1.5rem 0;
        border-bottom: 1px solid #f0f0f0;
        display: flex;
        align-items: flex-start;
        gap: 1rem;
    }
    
    .progress-item:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }
    
    .course-avatar {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        overflow: hidden;
        flex-shrink: 0;
        box-shadow: 0 4px 12px rgba(17, 87, 64, 0.1);
        border: 2px solid rgba(17, 87, 64, 0.08);
        background: #f8fafc;
    }
    
    .course-avatar-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }
    
    .course-avatar:hover .course-avatar-img {
        transform: scale(1.05);
    }
    
    .progress-info {
        flex: 1;
        min-width: 0; /* Allows text truncation */
    }
    
    .progress-actions {
        flex-shrink: 0;
        margin-left: 1rem;
    }
    
    .continue-btn {
        padding: 0.5rem 1rem;
        background: var(--primary-color);
        color: white;
        border: none;
        border-radius: 3px;
        font-size: 0.75rem;
        font-weight: 500;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 2px 8px rgba(17, 87, 64, 0.2);
        white-space: nowrap;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        overflow: hidden;
    }
    
    .continue-btn::after {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        width: 0;
        height: 100%;
        background-color: rgba(255, 255, 255, 0.2);
        transition: all 0.3s ease;
        z-index: -1;
    }
    
    .continue-btn:hover::after {
        width: 100%;
    }
    
    .continue-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }
    
    .continue-btn:active {
        transform: translateY(0);
        box-shadow: 0 2px 6px rgba(17, 87, 64, 0.25);
    }
    
    .progress-title {
        font-weight: 500;
        color: #1a1a1a;
        margin-bottom: 0.5rem;
        font-size: 1rem;
    }
    
    .progress-meta {
        font-size: 0.875rem;
        color: #666;
        margin-bottom: 1rem;
    }
    
    .progress-bar-container {
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    
    .progress-bar {
        flex: 1;
        height: 2px;
        background: #f0f0f0;
        overflow: hidden;
    }
    
    .progress-fill {
        height: 100%;
        background: #115740;
        transition: width 0.8s ease;
    }
    
    .progress-percentage {
        font-size: 0.875rem;
        font-weight: 500;
        color: #1a1a1a;
        min-width: 40px;
        text-align: right;
    }
    
    /* Certificates Grid */
    .certificates-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
    }
    
    .certificate-card {
        background: #ffffff;
        border: 1px solid #f0f0f0;
        padding: 1.5rem;
        text-align: center;
        transition: border-color 0.2s ease;
    }
    
    .certificate-card:hover {
        border-color: #e0e0e0;
    }
    
    .certificate-name {
        font-weight: 500;
        color: #1a1a1a;
        margin-bottom: 0.5rem;
        font-size: 0.875rem;
        line-height: 1.4;
    }
    
    .certificate-date {
        font-size: 0.75rem;
        color: #666;
    }
    
    .empty-state {
        text-align: center;
        padding: 3rem 1rem;
        color: #666;
    }

    /* LifterLMS profile overrides */
    .profile-account-section {
        margin-top: 2rem;
    }
    
    .profile-account-wrapper {
        border: 1px solid #f0f0f0;
        border-radius: 16px;
        padding: 2.5rem;
        background: #ffffff;
        box-shadow: 0 20px 60px rgba(17, 87, 64, 0.08);
    }
    
    .profile-account-wrapper .llms-student-dashboard {
        background: transparent;
        border: none;
        padding: 0;
        box-shadow: none;
    }
    
    .profile-account-wrapper .llms-sd-section {
        background: #fbfbfb;
        border: 1px solid #edf2f7;
        border-radius: 14px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 12px 30px rgba(15, 23, 42, 0.08);
    }
    
    .profile-account-wrapper .llms-sd-section-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #0f2d20;
        border-bottom: 2px solid rgba(17, 87, 64, 0.15);
        padding-bottom: 0.75rem;
        margin-bottom: 1.5rem;
    }
    
    .profile-account-wrapper .llms-loop {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 1.5rem;
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .profile-account-wrapper .llms-loop-item {
        border: 1px solid #edf2f7;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        background: #ffffff;
    }
    
    .profile-account-wrapper .llms-loop-item:hover {
        transform: translateY(-3px);
        box-shadow: 0 18px 35px rgba(15, 23, 42, 0.12);
    }
    
    .profile-account-wrapper .llms-progress {
        background: #edf2f7;
        border-radius: 999px;
        height: 6px;
        overflow: hidden;
    }
    
    .profile-account-wrapper .llms-progress .llms-progress-bar,
    .profile-account-wrapper .llms-progress .llms-progress-complete {
        background: var(--primary-color);
    }
    
    .profile-account-wrapper .llms-table {
        width: 100%;
        border-collapse: collapse;
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid #e7edf3;
        box-shadow: 0 16px 40px rgba(15, 23, 42, 0.08);
    }
    
    .profile-account-wrapper .llms-table th {
        background: linear-gradient(135deg, #115740, #0a3d2c);
        color: #fff;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.08em;
        padding: 1rem 1.25rem;
    }
    
    .profile-account-wrapper .llms-table td {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #eef2f7;
    }
    
    .profile-account-wrapper .llms-table tr:last-child td {
        border-bottom: none;
    }
    
    .profile-account-wrapper .llms-form-field label {
        font-weight: 600;
        color: #0f2d20;
        margin-bottom: 0.35rem;
        display: block;
    }
    
    .profile-account-wrapper .llms-form-field input,
    .profile-account-wrapper .llms-form-field select,
    .profile-account-wrapper .llms-form-field textarea {
        width: 100%;
        border: 1px solid #d7e0e8;
        border-radius: 10px;
        padding: 0.85rem 1rem;
        font-size: 1rem;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }
    
    .profile-account-wrapper .llms-form-field input:focus,
    .profile-account-wrapper .llms-form-field select:focus,
    .profile-account-wrapper .llms-form-field textarea:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(17, 87, 64, 0.15);
    }
    
    .profile-account-wrapper .llms-button,
    .profile-account-wrapper .llms-button-primary,
    .profile-account-wrapper .llms-button-secondary,
    .profile-account-wrapper button.llms-button-primary,
    .profile-account-wrapper input[type="submit"].llms-button {
        background: var(--primary-color);
        border: none;
        color: #fff;
        padding: 0.85rem 1.75rem;
        border-radius: 999px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        box-shadow: 0 10px 30px rgba(17, 87, 64, 0.25);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    
    .profile-account-wrapper .llms-button:hover,
    .profile-account-wrapper .llms-button-primary:hover,
    .profile-account-wrapper .llms-button-secondary:hover,
    .profile-account-wrapper button.llms-button-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 18px 30px rgba(17, 87, 64, 0.35);
    }
    
    .profile-account-wrapper .llms-notice {
        border-left: 4px solid var(--primary-color);
        border-radius: 12px;
        background: rgba(17, 87, 64, 0.08);
        padding: 1rem 1.25rem;
        color: #0f2d20;
    }
    
    @media (max-width: 768px) {
        .profile-account-wrapper {
            padding: 1.5rem;
        }
        
        .profile-account-wrapper .llms-loop {
            grid-template-columns: 1fr;
        }
    }
    
    /* Footer Styles */
    .site-footer {
        background-color: #115740;
        color: white;
        padding: 3rem 1rem 0;
        margin-top: 4rem;
    }
    
    .footer-content {
        display: grid;
        grid-template-columns: 1fr;
        gap: 2rem;
        max-width: 1200px;
        margin: 0 auto;
    }
    
    @media (min-width: 768px) {
        .footer-content {
            grid-template-columns: 2fr 1fr 1fr 1fr;
        }
    }
    
    .footer-section h4 {
        font-size: 1rem;
        margin-bottom: 1rem;
        font-weight: 500;
        font-family: 'Inter', sans-serif !important;
        text-transform: uppercase;
        color: #d2bb93;
    }
    
    .footer-section ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .footer-section ul li {
        margin-bottom: 0.5rem;
        font-family: 'Inter', sans-serif !important;
    }
    
    .footer-section a {
        color: rgba(255, 255, 255, 0.8);
        text-decoration: none;
        transition: color 0.2s;
        font-family: 'Inter', sans-serif !important;
    }
    
    .footer-section a:hover {
        color: white;
        text-decoration: underline;
    }
    
    .footer-logo {
        height: 40px;
        margin-bottom: 1rem;
    }

    .footer-section p {
        color: rgba(255, 255, 255, 0.8);
        font-size: 0.95rem;
        line-height: 1.5;
        margin: 0 0 1.5rem;
        font-family: 'Inter', sans-serif !important;
    }
    
    .social-links {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-top: 1.5rem;
    }
    
    .social-links a {
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.1);
        transition: all 0.3s ease;
    }
    
    .social-links a:hover {
        background: rgba(255, 255, 255, 0.2);
        transform: translateY(-2px);
    }
    
    .social-links svg {
        width: 20px;
        height: 20px;
    }
    
    .footer-bottom-content {
        display: flex;
        justify-content: center;
        align-items: center;
        padding-bottom: 2rem;
    }
    
    address {
        font-style: normal;
        line-height: 1.6;
    }
    
    /* Responsive Design */
    @media (max-width: 1024px) {
        .header-content {
            padding: 0 2rem;
            gap: 2rem;
        }
        
        .dashboard-content {
            padding-top: 0; /* No top padding */
        }
        
        .dashboard-hero {
            padding: calc(80px + 1.5rem) 2rem 3rem; /* Reduced bottom padding for tablet */
        }
        
        .hero-top {
            margin-bottom: 2.5rem;
        }
        
        .user-info h1 {
            font-size: clamp(2rem, 4vw, 2.8rem);
        }
        
        .user-stats {
            gap: 2rem;
        }
        
        .dashboard-grid {
            grid-template-columns: 1fr;
        }
        
        .dashboard-shape-1 {
            width: 300px;
            height: 300px;
        }
        
        .dashboard-shape-2 {
            width: 250px;
            height: 250px;
        }
    }
    
    @media (max-width: 768px) {
        .lms-header {
            padding: 1rem 0;
        }
        
        .header-content {
            padding: 0 1.5rem;
        }
        
        .dashboard-content {
            padding-top: 0; /* No top padding */
        }
        
        .dashboard-hero {
            padding: calc(70px + 1rem) 1.5rem 2.5rem; /* Reduced bottom padding for mobile */
        }
        
        .dashboard-hero-content {
            padding: 0 1rem;
        }
        
        .hero-top {
            flex-direction: column;
            align-items: center;
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .user-info {
            text-align: center;
            min-width: auto;
        }
        
        .user-info h1 {
            font-size: clamp(1.8rem, 6vw, 2.5rem);
        }
        
        .user-info .user-email {
            font-size: clamp(0.9rem, 3vw, 1.1rem);
        }
        
        .user-stats {
            grid-template-columns: 1fr;
            gap: 2rem;
            max-width: 300px;
            margin-bottom: 2.5rem;
        }
        
        .stat-number {
            font-size: 2.5rem;
        }
        
        .dashboard-main {
            padding: 2rem 1rem;
        }
        
        .dashboard-section {
            padding: 1.5rem;
        }
        
        .course-avatar {
            width: 50px;
            height: 50px;
        }
        
        .progress-item {
            padding: 1.25rem 0;
            gap: 0.75rem;
        }
        
        .continue-btn {
            padding: 0.4rem 0.8rem;
            font-size: 0.7rem;
        }
        
        .dashboard-shape-1 {
            width: 250px;
            height: 250px;
            top: -150px;
            left: -80px;
        }
        
        .dashboard-shape-2 {
            width: 200px;
            height: 200px;
            right: -100px;
        }
        
        .dashboard-shape-3 {
            width: 150px;
            height: 150px;
        }
    }
    
    @media (max-width: 480px) {
        .dashboard-content {
            padding-top: 0; /* No top padding */
        }
        
        .dashboard-hero {
            padding: calc(60px + 1rem) 1rem 2rem; /* Reduced bottom padding for small mobile */
        }
        
        .user-info h1 {
            font-size: clamp(1.5rem, 8vw, 2rem);
        }
        
        .user-stats {
            margin-bottom: 2rem;
        }
        
        .stat-number {
            font-size: 2rem;
        }
        
        .course-avatar {
            width: 45px;
            height: 45px;
        }
        
        .progress-item {
            padding: 1rem 0;
            gap: 0.5rem;
        }
        
        .progress-title {
            font-size: 0.9rem;
        }
        
        .progress-meta {
            font-size: 0.8rem;
        }
        
        .continue-btn {
            padding: 0.3rem 0.6rem;
            font-size: 0.65rem;
            min-width: 60px;
        }
        
        .dashboard-shape-1 {
            width: 200px;
            height: 200px;
            top: -120px;
            left: -60px;
        }
        
        .dashboard-shape-2 {
            width: 150px;
            height: 150px;
            right: -80px;
        }
        
        .dashboard-shape-3 {
            width: 120px;
            height: 120px;
        }
    }
    
    /* Accessibility and motion preferences */
    @media (prefers-reduced-motion: reduce) {
        .user-info h1,
        .user-info .user-email,
        .user-stats,
        .dashboard-shape {
            animation: none !important;
            transform: none !important;
            opacity: 1 !important;
        }
    }
</style>

<!-- Loading Screen -->
<div class="loading-screen">
    <div class="loading-triangles-container">
        <div class="loading-triangle triangle-move-1"></div>
        <div class="loading-triangle triangle-move-2"></div>
        <div class="loading-triangle triangle-move-3"></div>
    </div>
    
    <div class="loading-content">
        <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/logodark.png" alt="AidData Logo" class="loading-logo">
        <div class="loading-spinner">
            <div class="spinner-ring"></div>
        </div>
        <p class="loading-text">Loading Dashboard</p>
    </div>
</div>

<!-- Header - Exact match to front-page.php -->
<header class="lms-header">
    <div class="header-content">
        <div class="logo-section">
            <a href="https://www.aiddata.org" target="_blank">
                <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/logodark.png" alt="AidData Logo" class="logo">
            </a>
        </div>
        
        <div class="header-actions">
            <div class="auth-only visible">
                <div class="header-icons">
                    <button class="header-button" id="notificationsButton" aria-label="Notifications">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                            <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                        </svg>
                        <span class="notification-badge"></span>
                    </button>
                    <button class="header-button menu-button" aria-label="Menu" aria-haspopup="true">
                        <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M3 12h18M3 6h18M3 18h18"/>
                        </svg>
                    </button>
                    <div class="profile-dropdown">
                        <div class="dropdown-header">
                            <div class="dropdown-user-info">
                                <span class="user-name"><?php echo esc_html($current_user->display_name); ?></span>
                                <span class="user-email"><?php echo esc_html($current_user->user_email); ?></span>
                            </div>
                        </div>
                        <a href="<?php echo esc_url(home_url('/')); ?>" class="dropdown-item">Home</a>
                        <button class="dropdown-item logout-button" onclick="location.href='<?php echo wp_logout_url(home_url()); ?>'">Sign Out</button>
                    </div>

                    <div class="dashboard-section profile-account-section">
                        <div class="section-header">
                            <h2 class="section-title">My Account</h2>
                            <a href="<?php echo esc_url($account_settings_url); ?>" class="section-action">Manage Settings</a>
                        </div>
                        <div class="profile-account-wrapper">
                            <?php echo do_shortcode('[lifterlms_my_account layout="stacked"]'); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </header>

<div class="dashboard-content">
    <div class="dashboard-hero">
        <!-- Animated shapes matching front-page -->
        <div class="dashboard-shapes">
            <div class="dashboard-shape dashboard-shape-1"></div>
            <div class="dashboard-shape dashboard-shape-2"></div>
            <div class="dashboard-shape dashboard-shape-3"></div>
        </div>
        
        <div class="dashboard-hero-content">
            <div class="hero-top">
                <div class="user-info">
                    <h1><?php echo esc_html($current_user->display_name); ?></h1>
                    <div class="user-email"><?php echo esc_html($current_user->user_email); ?></div>
                </div>
            </div>
            
            <div class="user-stats">
                <div class="stat-item">
                    <span class="stat-number" id="stat-enrolled">...</span>
                    <span class="stat-label">Courses Enrolled</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number" id="stat-completed">...</span>
                    <span class="stat-label">Completed</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number" id="stat-certificates">...</span>
                    <span class="stat-label">Certificates</span>
                </div>
            </div>
        </div>
    </div>

    <div class="dashboard-main">
        <div class="dashboard-grid">
                   <!-- Current Progress -->
                   <div class="dashboard-section">
                       <div class="section-header">
                           <h2 class="section-title">Current Progress</h2>
                           <a href="<?php echo esc_url($courses_url); ?>" class="section-action">View All</a>
                       </div>
                       
                       <div class="progress-item">
                           <div class="course-avatar">
                               <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/global_finance.png" alt="Global Development Finance" class="course-avatar-img">
                           </div>
                           <div class="progress-info">
                               <div class="progress-title">Navigating Global Development Finance</div>
                               <div class="progress-meta">Course • Started 3 days ago</div>
                               <div class="progress-bar-container">
                                   <div class="progress-bar">
                                       <div class="progress-fill" style="width: 65%;"></div>
                                   </div>
                                   <span class="progress-percentage">65%</span>
                               </div>
                           </div>
                           <div class="progress-actions">
<button class="continue-btn" data-course="<?php echo home_url('/t-h/navigating-global-development-finance/'); ?>">
    Continue
</button>
                           </div>
                       </div>
                       
                       <div class="progress-item">
                           <div class="course-avatar">
                               <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/port_project.png" alt="Securing Development Funding" class="course-avatar-img">
                           </div>
                           <div class="progress-info">
                               <div class="progress-title">Securing Development Funding</div>
                               <div class="progress-meta">Simulation • Started 1 week ago</div>
                               <div class="progress-bar-container">
                                   <div class="progress-bar">
                                       <div class="progress-fill" style="width: 30%;"></div>
                                   </div>
                                   <span class="progress-percentage">30%</span>
                               </div>
                           </div>
                           <div class="progress-actions">
                               <button class="continue-btn" data-course="courses/securing-development/">
                                   Continue
                               </button>
                           </div>
                       </div>
                       
                       <div class="progress-item">
                           <div class="course-avatar">
                               <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/china_dashboard.png" alt="China Dashboard Tutorial" class="course-avatar-img">
                           </div>
                           <div class="progress-info">
                               <div class="progress-title">China Dashboard Tutorial</div>
                               <div class="progress-meta">Tutorial • Started 2 days ago</div>
                               <div class="progress-bar-container">
                                   <div class="progress-bar">
                                       <div class="progress-fill" style="width: 80%;"></div>
                                   </div>
                                   <span class="progress-percentage">80%</span>
                               </div>
                           </div>
                           <div class="progress-actions">
                               <button class="continue-btn" data-course="courses/china-aiddata-dashboard-tutorial/">
                                   Continue
                               </button>
                           </div>
                       </div>
                   </div>
            
               <!-- Certificates Section -->
               <div class="dashboard-section">
                   <div class="section-header">
                       <h2 class="section-title">My Certificates</h2>
                       <a href="<?php echo esc_url($certificates_url); ?>" class="section-action">View All</a>
                   </div>
                   
                   <div class="certificates-grid">
                       <div class="certificate-card">
                           <div class="certificate-name">China's Global Diplomacy</div>
                           <div class="certificate-date">Earned Dec 15, 2024</div>
                       </div>
                       
                       <div class="certificate-card">
                           <div class="certificate-name">Data Analysis Fundamentals</div>
                           <div class="certificate-date">Earned Nov 28, 2024</div>
                       </div>
                   </div>
               </div>
    </div>
</div>

<footer class="site-footer">
    <div class="footer-content">
        <div class="footer-section">
            <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/logo.png" alt="AidData Logo" class="footer-logo">
            <p> </p>
            <div class="social-links">
                <a href="https://twitter.com/AidData" target="_blank" rel="noopener noreferrer" aria-label="Follow us on Twitter">
                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z"></path></svg>
                </a>
                <a href="https://www.linkedin.com/company/aiddata" target="_blank" rel="noopener noreferrer" aria-label="Connect with us on LinkedIn">
                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"></path><rect x="2" y="9" width="4" height="12"></rect><circle cx="4" cy="4" r="2"></circle></svg>
                </a>
                <a href="https://github.com/aiddata" target="_blank" rel="noopener noreferrer" aria-label="View our code on GitHub">
                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22"></path></svg>
                </a>
                <a href="https://www.instagram.com/aiddata?utm_source=ig_web_button_share_sheet&igsh=ZDNlZDc0MzIxNw==" target="_blank" rel="noopener noreferrer" aria-label="Follow us on Instagram">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/>
                    </svg>
                </a>
            </div>
            <button onclick="window.open('https://www.aiddata.org/newsletter', '_blank')" class="newsletter-button" aria-label="Get our Newsletter" style="display: block; background-color: #026447; color: white; padding: 10px 18px; border-radius: 6px; border: none; cursor: pointer; margin-top: 20px; font-weight: 600; transition: all 0.3s ease; font-family: inherit; font-size: 13px; width: fit-content; box-shadow: 0 2px 4px rgba(0,0,0,0.15); letter-spacing: 0.3px; text-transform: uppercase;">
                <span>Subscribe to Newsletter</span>
            </button>
        </div>

        <div class="footer-section">
            <h4>Quick Links</h4>
            <ul>
                <li><a href="https://www.aiddata.org/about" target="_blank">About Us</a></li>
                <li><a href="https://www.aiddata.org/data" target="_blank">Data</a></li>
                <li><a href="https://www.aiddata.org/publications" target="_blank">Publications</a></li>
                <li><a href="https://www.aiddata.org/blog" target="_blank">Blog</a></li>
                <li><a href="https://www.aiddata.org/careers" target="_blank">Careers</a></li>
            </ul>
        </div>

        <div class="footer-section">
            <h4>Resources</h4>
            <ul>
                <li><a href="https://www.aiddata.org/methods" target="_blank">Methods</a></li>
                <li><a href="https://www.aiddata.org/datasets" target="_blank">Datasets</a></li>
                <li><a href="https://www.aiddata.org/geoquery" target="_blank">GeoQuery</a></li>
                <li><a href="https://www.aiddata.org/china-research-lab" target="_blank">China Research Lab</a></li>
            </ul>
        </div>

        <div class="footer-section">
            <h4>Contact</h4>
            <address>
                If you're looking to partner up for online, hybrid or in-person trainings, please reach out to<br>
                <a href="mailto:training@aiddata.org" style="color: #ffffff;">training@aiddata.org</a>
            </address>
        </div>
    </div>
  
    <hr>
  
    <div class="footer-bottom" style="background: transparent;">
        <div class="footer-bottom-content">
            <a href="https://www.wm.edu" target="_blank" rel="noopener noreferrer">
                <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/wm_logo_white.png" alt="William & Mary Logo" class="footer-bottom-logo" style="max-height: 60px;">
            </a>
        </div>
    </div>
</footer>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Load real-time dashboard data
    loadDashboardData();
    
    // Refresh data every 30 seconds
    setInterval(loadDashboardData, 30000);
    
    // Hide loading screen after content is loaded
    setTimeout(function() {
        const loadingScreen = document.querySelector('.loading-screen');
        if (loadingScreen) {
            loadingScreen.style.opacity = '0';
            setTimeout(function() {
                loadingScreen.style.display = 'none';
            }, 500);
        }
    }, 1000);
    
    // Keep LifterLMS loaders hidden to match dashboard experience
    function suppressLifterLoaders() {
        document.body.classList.add('no-loading-screen');
        document.querySelectorAll('.loading-screen, .llms-loading, .llms-spinner, .llms-ajax-loader, .spinner, .loader').forEach(element => {
            element.style.display = 'none';
            element.style.opacity = '0';
            element.style.visibility = 'hidden';
        });
    }

    suppressLifterLoaders();
    setTimeout(suppressLifterLoaders, 250);
    setTimeout(suppressLifterLoaders, 1000);
    setInterval(suppressLifterLoaders, 10000);

    const lifterObserver = new MutationObserver((mutations) => {
        mutations.forEach(mutation => {
            mutation.addedNodes.forEach(node => {
                if (node.nodeType === 1) {
                    if (node.matches && node.matches('.loading-screen, .llms-loading, .llms-spinner, .llms-ajax-loader, .spinner, .loader')) {
                        suppressLifterLoaders();
                    }
                }
            });
        });
    });

    lifterObserver.observe(document.body, { childList: true, subtree: true });
    
    // Profile dropdown functionality
    const menuButton = document.querySelector('.menu-button');
    const profileDropdown = document.querySelector('.profile-dropdown');
    
    if (menuButton && profileDropdown) {
        menuButton.addEventListener('click', function(e) {
            e.stopPropagation();
            profileDropdown.classList.toggle('show');
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function() {
            profileDropdown.classList.remove('show');
        });
        
        profileDropdown.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    }
    
    // Animate progress bars
    const progressBars = document.querySelectorAll('.progress-fill');
    progressBars.forEach(bar => {
        const width = bar.style.width;
        bar.style.width = '0';
        setTimeout(() => {
            bar.style.width = width;
        }, 800);
    });
    
    // Smooth scroll for certificate link
    const certificateLink = document.querySelector('a[href="#certificates"]');
    if (certificateLink) {
        certificateLink.addEventListener('click', function(e) {
            e.preventDefault();
            const certificatesSection = document.querySelector('.certificates-grid').closest('.dashboard-section');
            if (certificatesSection) {
                certificatesSection.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
                
                // Add a subtle highlight effect
                certificatesSection.style.transition = 'box-shadow 0.3s ease';
                certificatesSection.style.boxShadow = '0 0 20px rgba(17, 87, 64, 0.15)';
                setTimeout(() => {
                    certificatesSection.style.boxShadow = '';
                }, 2000);
            }
        });
    }
    
    // Handle continue button clicks
    const continueButtons = document.querySelectorAll('.continue-btn');
    continueButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const courseUrl = this.getAttribute('data-course');
            const courseTitle = this.closest('.progress-item').querySelector('.progress-title').textContent;
            
            // Add visual feedback
            this.style.transform = 'scale(0.95)';
            this.style.opacity = '0.8';
            
            setTimeout(() => {
                this.style.transform = '';
                this.style.opacity = '';
            }, 150);
            
            // Use the existing course loading system if available
            if (window.CourseLoader) {
                const loader = new window.CourseLoader();
                loader.showLoading(courseTitle, this);
                loader.navigateToCourse(courseUrl);
            } else {
                // Fallback: direct navigation with loading state
                this.innerHTML = `
                    <span>Loading...</span>
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" class="loading-spinner">
                        <circle cx="12" cy="12" r="10" opacity="0.3"/>
                        <path d="M12 2 A10 10 0 0 1 22 12" stroke-dasharray="31.416" stroke-dashoffset="31.416">
                            <animate attributeName="stroke-dashoffset" values="31.416;0" dur="1s" repeatCount="indefinite"/>
                        </path>
                    </svg>
                `;
                
                setTimeout(() => {
                    window.location.href = courseUrl;
                }, 800);
            }
        });
        
        // Add hover effect for better UX
        button.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
        });
        
        button.addEventListener('mouseleave', function() {
            if (!this.matches(':hover')) {
                this.style.transform = '';
            }
        });
    });
    
    /**
     * Load dashboard data via AJAX
     */
    function loadDashboardData() {
        fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                'action': 'get_dashboard_stats',
                'nonce': '<?php echo wp_create_nonce('dashboard_nonce'); ?>'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateDashboardStats(data.data);
            } else {
                console.error('Failed to load dashboard stats:', data);
            }
        })
        .catch(error => {
            console.error('Error loading dashboard data:', error);
        });
        
        // Load tutorial progress
        loadTutorialProgress();
        loadCertificates();
    }
    
    /**
     * Update dashboard statistics
     */
    function updateDashboardStats(stats) {
        // Calculate total enrolled (courses + tutorials)
        const totalEnrolled = (stats.courses?.enrolled_courses || 0) + (stats.tutorials?.total_tutorials || 0);
        const totalCompleted = (stats.courses?.completed_courses || 0) + (stats.tutorials?.completed_tutorials || 0);
        const totalCertificates = stats.certificates?.total_certificates || 0;
        
        // Animate number changes
        animateNumber('stat-enrolled', totalEnrolled);
        animateNumber('stat-completed', totalCompleted);
        animateNumber('stat-certificates', totalCertificates);
    }
    
    /**
     * Animate number from current to target
     */
    function animateNumber(elementId, targetNumber) {
        const element = document.getElementById(elementId);
        if (!element) return;
        
        const currentText = element.textContent;
        const currentNumber = currentText === '...' ? 0 : parseInt(currentText) || 0;
        
        if (currentNumber === targetNumber) return;
        
        const duration = 800; // ms
        const steps = 20;
        const increment = (targetNumber - currentNumber) / steps;
        const stepDuration = duration / steps;
        
        let currentStep = 0;
        const timer = setInterval(() => {
            currentStep++;
            const newValue = Math.round(currentNumber + (increment * currentStep));
            element.textContent = newValue;
            
            if (currentStep >= steps) {
                element.textContent = targetNumber;
                clearInterval(timer);
            }
        }, stepDuration);
    }
    
    /**
     * Load tutorial progress
     */
    function loadTutorialProgress() {
        fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                'action': 'get_tutorial_progress',
                'nonce': '<?php echo wp_create_nonce('dashboard_nonce'); ?>'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data.length > 0) {
                updateProgressSection(data.data);
            }
        })
        .catch(error => {
            console.error('Error loading tutorial progress:', error);
        });
    }
    
    /**
     * Update progress section with real data
     */
    function updateProgressSection(tutorials) {
        // Find progress section
        const progressSection = document.querySelector('.dashboard-section');
        if (!progressSection) return;
        
        // Get only in-progress tutorials
        const inProgressTutorials = tutorials.filter(t => t.status === 'in_progress').slice(0, 3);
        
        if (inProgressTutorials.length === 0) return;
        
        // Update the progress items dynamically
        const progressItems = progressSection.querySelectorAll('.progress-item');
        inProgressTutorials.forEach((tutorial, index) => {
            if (progressItems[index]) {
                const progressFill = progressItems[index].querySelector('.progress-fill');
                const progressPercentage = progressItems[index].querySelector('.progress-percentage');
                
                if (progressFill && progressPercentage) {
                    progressFill.style.width = tutorial.progress_percent + '%';
                    progressPercentage.textContent = tutorial.progress_percent + '%';
                }
                
                // Update tutorial URL
                const continueBtn = progressItems[index].querySelector('.continue-btn');
                if (continueBtn && tutorial.url) {
                    continueBtn.setAttribute('data-course', tutorial.url);
                }
            }
        });
    }
    
    /**
     * Load certificates
     */
    function loadCertificates() {
        fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                'action': 'get_certificate_list',
                'nonce': '<?php echo wp_create_nonce('dashboard_nonce'); ?>'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data.length > 0) {
                updateCertificatesSection(data.data);
            }
        })
        .catch(error => {
            console.error('Error loading certificates:', error);
        });
    }
    
    /**
     * Update certificates section
     */
    function updateCertificatesSection(certificates) {
        const certificatesGrid = document.querySelector('.certificates-grid');
        if (!certificatesGrid || certificates.length === 0) return;
        
        // Update existing certificates or add new ones
        certificates.slice(0, 2).forEach((cert, index) => {
            const certCards = certificatesGrid.querySelectorAll('.certificate-card');
            if (certCards[index]) {
                const certName = certCards[index].querySelector('.certificate-name');
                const certDate = certCards[index].querySelector('.certificate-date');
                
                if (certName) {
                    certName.textContent = cert.content_title;
                }
                if (certDate) {
                    const earnedDate = new Date(cert.earned_date);
                    certDate.textContent = 'Earned ' + earnedDate.toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'short',
                        day: 'numeric'
                    });
                }
                
                // Make certificate clickable
                certCards[index].style.cursor = 'pointer';
                certCards[index].onclick = function() {
                    window.open(cert.download_url, '_blank');
                };
            }
        });
    }
    
});
</script>

<?php get_footer(); ?>
