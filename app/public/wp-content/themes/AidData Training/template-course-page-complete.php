<?php
/**
 * Template Name: Course Page Template (Complete)
 * Template Post Type: page
 *
 * A comprehensive course page template that follows the AidData Training Hub front-page theme
 * Includes complete styling, modals, loading screen, and responsive design
 *
 * @package WordPress
 * @subpackage Twenty_Twenty_Five
 * @since Twenty Twenty-Five 1.0
 */

get_header();

// Enqueue Inter font
wp_enqueue_style('inter-font', 'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap', array(), null);

// Enqueue authentication-specific styles
wp_enqueue_style('auth-styles', get_template_directory_uri() . '/assets/css/auth-styles.css', array(), '1.0.0');
wp_enqueue_style('loading-screen', get_template_directory_uri() . '/assets/css/loading-screen.css', array(), '1.0.0');

// Helper function to get field values with ACF fallback
function course_get_field($field_name, $post_id = false, $default = '') {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    if (function_exists('get_field')) {
        $value = get_field($field_name, $post_id);
        return $value !== '' ? $value : $default;
    }
    
    $value = get_post_meta($post_id, $field_name, true);
    return $value !== '' ? $value : $default;
}

function course_has_rows($field_name, $post_id = false) {
    if (function_exists('have_rows')) {
        return have_rows($field_name, $post_id);
    }
    
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    $value = get_post_meta($post_id, $field_name, true);
    return !empty($value);
}

function course_get_sub_field($field_name, $default = '') {
    if (function_exists('get_sub_field')) {
        $value = get_sub_field($field_name);
        return $value !== '' ? $value : $default;
    }
    return $default;
}
?>

<!-- Complete Course Page Styles -->
<style>
     /* Import Inter font from Google Fonts */
     @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
     
     /* CSS Variables - Matching main LMS theme */
     :root {
         /* Light mode colors (default) */
         --primary-color: #115740;
         --primary-light: #2a7d60;
         --primary-dark: #0a3d2c;
         --secondary-color: #2d89ef;
         --accent-color: #06a181;
         --bg-color: #ffffff;
         --card-bg: #ffffff;
         --text-color: #333333;
         --light-text: #6c757d;
         --border-color: #e9ecef;
         --header-bg: #ffffff;
         --footer-bg: #f8f9fa;
         --footer-text: #6c757d;
         --shadow: rgba(0, 0, 0, 0.1);
         --hover-bg: rgba(0, 0, 0, 0.05);
         
         /* Dark mode colors */
         --dark-primary-color: #1a9c6e;
         --dark-primary-light: #2cbd8a;
         --dark-primary-dark: #0d7353;
         --dark-secondary-color: #4a9cf5;
         --dark-accent-color: #ffcd39;
         --dark-bg-color: #121212;
         --dark-card-bg: #1e1e1e;
         --dark-text-color: #e0e0e0;
         --dark-light-text: #a0a0a0;
         --dark-border-color: #333333;
         --dark-header-bg: #1a1a1a;
         --dark-footer-bg: #1a1a1a;
         --dark-footer-text: #a0a0a0;
         --dark-shadow: rgba(0, 0, 0, 0.3);
         --dark-hover-bg: rgba(255, 255, 255, 0.05);
         
         /* Course-specific variables */
         --aiddata-primary: #026447;
         --aiddata-primary-dark: #004E38;
         --aiddata-secondary: #00b388;
         --aiddata-accent: #789d4a;
         
         /* Font family */
         --font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
     }
     
     /* Base Styles with Inter Font */
     * {
         margin: 0;
         padding: 0;
         box-sizing: border-box;
         font-family: var(--font-family);
     }
     
     body, html {
         margin: 0;
         padding: 0;
         height: 100%;
         width: 100%;
         overflow-x: hidden;
         font-family: var(--font-family);
         background-color: #f5f5f5;
         color: #333;
         line-height: 1.5;
         min-height: 100vh;
         letter-spacing: -0.02em;
         -webkit-font-smoothing: antialiased;
         -moz-osx-font-smoothing: grayscale;
         display: flex;
         flex-direction: column;
     }
     
     /* Ensure all text elements use Inter */
     h1, h2, h3, h4, h5, h6,
     p, span, div, a, button,
     input, textarea, select,
     label, li, td, th {
         font-family: var(--font-family);
     }
    
    /* Custom Scrollbar */
    ::-webkit-scrollbar {
        width: 8px;
    }
    
    ::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }
    
    ::-webkit-scrollbar-thumb {
        background: var(--aiddata-primary);
        border-radius: 4px;
    }
    
    ::-webkit-scrollbar-thumb:hover {
        background: var(--aiddata-primary-dark);
    }
    
    * {
        scrollbar-width: thin;
        scrollbar-color: var(--aiddata-primary) #f1f1f1;
    }
    
    /* Dark Mode Scrollbar */
    body.dark-mode::-webkit-scrollbar-track {
        background: var(--dark-bg-color);
    }
    
    body.dark-mode::-webkit-scrollbar-thumb {
        background-color: #444;
        border-radius: 6px;
    }
    
    body.dark-mode::-webkit-scrollbar-thumb:hover {
        background-color: #555;
    }
    
    /* Dark Mode Base */
    body.dark-mode {
        background-color: var(--dark-bg-color);
        color: var(--dark-text-color);
    }
    
    .lms-main {
        flex: 1;
        display: flex;
        flex-direction: column;
        margin-top: 0; /* No gap between header and content */
    }
    
    /* Header Styles - Matching main LMS theme */
    .lms-header {
        background: rgba(255, 255, 255, 0.98);
        position: fixed;
        width: 100%;
        top: 0;
        z-index: 1000;
        padding: 1.5rem 0;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        backdrop-filter: blur(10px);
        box-shadow: 0 1px 3px rgba(17, 87, 64, 0.05);
    }
    
    .lms-header::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 1px;
        background: linear-gradient(to right, 
            transparent 0%, 
            rgba(17, 87, 64, 0.1) 20%, 
            rgba(17, 87, 64, 0.1) 80%, 
            transparent 100%
        );
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
        transition: opacity 0.3s ease;
    }
    
    .logo:hover {
        opacity: 1;
    }
    
    .header-actions {
        justify-self: end;
        display: flex;
        align-items: center;
        gap: 1.5rem;
    }
    
    .header-button {
        background: none;
        border: none;
        color: var(--text-color);
        cursor: pointer;
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        font-weight: 500;
        font-size: 0.9375rem;
        transition: all 0.3s ease;
        position: relative;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .header-button:hover {
        background-color: rgba(17, 87, 64, 0.08);
        color: var(--primary-color);
        transform: translateY(-1px);
    }
    
    .login-button {
        border: 1.5px solid rgba(17, 87, 64, 0.2);
    }
    
    .signup-button {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
        color: white;
        box-shadow: 0 2px 8px rgba(17, 87, 64, 0.2);
    }
    
    .signup-button:hover {
        background: linear-gradient(135deg, var(--primary-dark) 0%, #083d29 100%);
        box-shadow: 0 4px 16px rgba(17, 87, 64, 0.3);
        color: white;
    }
    
    /* Dark mode header */
    body.dark-mode .lms-header {
        background-color: var(--dark-header-bg);
        box-shadow: 0 2px 4px var(--dark-shadow);
    }
    
    body.dark-mode .header-button {
        color: var(--dark-text-color);
    }
    
    body.dark-mode .header-button:hover {
        background-color: var(--dark-hover-bg);
    }
    
    /* Header Icons and Dropdown Styles */
    .header-icons {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        position: relative;
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
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0% { transform: scale(1); opacity: 1; }
        50% { transform: scale(1.2); opacity: 0.7; }
        100% { transform: scale(1); opacity: 1; }
    }
    
    /* Profile Dropdown */
    .profile-dropdown {
        position: absolute;
        top: calc(100% + 8px);
        right: 0;
        width: 280px;
        background: white;
        border-radius: 8px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        padding: 1.5rem;
        opacity: 0;
        visibility: hidden;
        transform: translateY(-10px);
        transition: all 0.2s ease-in-out;
        z-index: 1000;
        border: 1px solid rgba(17, 87, 64, 0.1);
    }
    
    .profile-dropdown.active {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }
    
    .profile-dropdown .dropdown-header {
        padding-bottom: 1rem;
        border-bottom: 1px solid rgba(17, 87, 64, 0.1);
        margin-bottom: 1rem;
    }
    
    .profile-dropdown .dropdown-user-info {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }
    
    .profile-dropdown .user-name {
        font-size: 1rem;
        font-weight: 600;
        color: var(--text-color);
    }
    
    .profile-dropdown .user-email {
        font-size: 0.9rem;
        color: var(--light-text);
    }
    
    .profile-dropdown .dropdown-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem 1rem;
        color: var(--text-color);
        text-decoration: none;
        font-size: 0.95rem;
        border-radius: 6px;
        transition: all 0.2s ease;
        width: 100%;
        border: none;
        background: none;
        text-align: left;
        cursor: pointer;
    }
    
    .profile-dropdown .dropdown-item:hover {
        background: rgba(17, 87, 64, 0.05);
        color: var(--primary-color);
    }
    
    .profile-dropdown .dropdown-divider {
        height: 1px;
        background: rgba(17, 87, 64, 0.1);
        margin: 0.5rem 0;
    }
    
    .profile-dropdown .logout-button {
        color: #dc3545;
    }
    
    .profile-dropdown .logout-button:hover {
        background: rgba(220, 53, 69, 0.05);
        color: #dc3545;
    }
    
    /* Dark mode dropdown */
    body.dark-mode .profile-dropdown {
        background: var(--dark-card-bg);
        border-color: var(--dark-border-color);
    }
    
    body.dark-mode .profile-dropdown .dropdown-header {
        border-bottom-color: var(--dark-border-color);
    }
    
    body.dark-mode .profile-dropdown .user-name {
        color: var(--dark-text-color);
    }
    
    body.dark-mode .profile-dropdown .user-email {
        color: var(--dark-light-text);
    }
    
    body.dark-mode .profile-dropdown .dropdown-item {
        color: var(--dark-text-color);
    }
    
    body.dark-mode .profile-dropdown .dropdown-item:hover {
        background: var(--dark-hover-bg);
        color: var(--dark-primary-color);
    }
    
    /* Loading Screen Styles */
    .loading-screen {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        transition: opacity 0.5s ease;
    }
    
    .loading-triangles-container {
        position: absolute;
        width: 100%;
        height: 100%;
        overflow: hidden;
        pointer-events: none;
    }
    
    .loading-triangle {
        position: absolute;
        width: 0;
        height: 0;
        opacity: 0.1;
    }
    
    .triangle-move-1 {
        border-left: 50px solid transparent;
        border-right: 50px solid transparent;
        border-bottom: 87px solid var(--primary-color);
        top: 20%;
        left: 10%;
        animation: float1 6s ease-in-out infinite;
    }
    
    .triangle-move-2 {
        border-left: 30px solid transparent;
        border-right: 30px solid transparent;
        border-bottom: 52px solid var(--accent-color);
        top: 60%;
        right: 15%;
        animation: float2 8s ease-in-out infinite;
    }
    
    .triangle-move-3 {
        border-left: 40px solid transparent;
        border-right: 40px solid transparent;
        border-bottom: 69px solid var(--secondary-color);
        bottom: 30%;
        left: 70%;
        animation: float3 7s ease-in-out infinite;
    }
    
    @keyframes float1 {
        0%, 100% { transform: translateY(0px) rotate(0deg); }
        50% { transform: translateY(-20px) rotate(5deg); }
    }
    
    @keyframes float2 {
        0%, 100% { transform: translateY(0px) rotate(0deg); }
        50% { transform: translateY(-15px) rotate(-3deg); }
    }
    
    @keyframes float3 {
        0%, 100% { transform: translateY(0px) rotate(0deg); }
        50% { transform: translateY(-25px) rotate(7deg); }
    }
    
    .loading-content {
        text-align: center;
        z-index: 2;
    }
    
    .loading-logo {
        height: 60px;
        margin-bottom: 2rem;
        opacity: 0.9;
    }
    
    .loading-spinner {
        margin: 2rem 0;
    }
    
    .spinner-ring {
        width: 40px;
        height: 40px;
        border: 3px solid rgba(17, 87, 64, 0.2);
        border-top: 3px solid var(--primary-color);
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin: 0 auto;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    .loading-text {
        color: var(--light-text);
        font-size: 1rem;
        font-weight: 500;
        margin: 0;
    }
    
    body.dark-mode .loading-screen {
        background: linear-gradient(135deg, var(--dark-bg-color) 0%, #0a0a0a 100%);
    }
    
    body.dark-mode .loading-text {
        color: var(--dark-light-text);
    }
    
    /* Course Hero Section - Matching Front Page Welcome Section */
    .course-hero {
        margin-top: 0;
        padding: calc(8rem + 85px) 2rem 8rem 2rem; /* Add header height to top padding */
        position: relative;
        overflow: hidden;
        flex-shrink: 0;
        background: linear-gradient(135deg, 
            rgba(17, 87, 64, 0.02) 0%,
            rgba(26, 128, 95, 0.06) 100%
        );
    }
    
    body.dark-mode .course-hero {
        background-color: var(--dark-bg-color);
    }
    
    .course-hero-content {
        position: relative;
        z-index: 100;
        max-width: 800px;
        margin: 0 auto;
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center;
        color: var(--primary-dark);
    }
    
    /* Welcome Shapes for Course Hero */
    .course-hero-shapes {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
        z-index: 1;
    }
    
    .hero-shape {
        position: absolute;
        border-radius: 50%;
        opacity: 0.1;
    }
    
    .hero-shape-1 {
        width: 400px;
        height: 400px;
        background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
        top: -200px;
        left: -100px;
        animation: float1 8s ease-in-out infinite;
    }
    
    .hero-shape-2 {
        width: 300px;
        height: 300px;
        background: linear-gradient(135deg, var(--accent-color), var(--secondary-color));
        bottom: -150px;
        right: -75px;
        animation: float2 10s ease-in-out infinite reverse;
    }
    
    .hero-shape-3 {
        width: 200px;
        height: 200px;
        background: linear-gradient(135deg, var(--aiddata-secondary), var(--aiddata-accent));
        top: 50%;
        right: 10%;
        transform: translateY(-50%) rotate(45deg);
        animation: rotate 12s linear infinite;
    }
    
    @keyframes float1 {
        0%, 100% { transform: translateY(0px) rotate(0deg); }
        50% { transform: translateY(-30px) rotate(10deg); }
    }
    
    @keyframes float2 {
        0%, 100% { transform: translateY(0px) rotate(0deg); }
        50% { transform: translateY(-20px) rotate(-5deg); }
    }
    
    @keyframes rotate {
        0% { transform: translateY(-50%) rotate(45deg) scale(1); }
        50% { transform: translateY(-50%) rotate(55deg) scale(1.1); }
        100% { transform: translateY(-50%) rotate(45deg) scale(1); }
    }
    
    /* Course Badge (matching welcome-badge) */
    .course-badge {
        display: inline-block;
        padding: 0.5rem 1rem;
        background: rgba(17, 87, 64, 0.1);
        color: var(--primary-color);
        border-radius: 100px;
        font-size: 0.9rem;
        font-weight: 500;
        letter-spacing: 0.05em;
        margin-bottom: 2rem;
        transform: translateY(20px);
        opacity: 0;
        animation: fadeInUp 0.6s ease forwards;
    }
    
    /* Course Hero Title (matching welcome h2) */
    .course-hero-content h2 {
        font-size: clamp(2.5rem, 5vw, 3.5rem);
        margin-bottom: 1.5rem;
        font-weight: 400;
        line-height: 1.2;
        color: var(--primary-dark);
        letter-spacing: -0.03em;
        transform: translateY(20px);
        opacity: 0;
        animation: fadeInUp 0.6s 0.2s ease forwards;
        z-index: 100;
        font-family: var(--font-family);
    }
    
    /* Course Hero Description (matching welcome p) */
    .course-hero-content p {
        font-size: clamp(1.1rem, 2vw, 1.25rem);
        line-height: 1.6;
        color: var(--light-text);
        max-width: 600px;
        margin: 0 auto 2rem;
        transform: translateY(20px);
        opacity: 0;
        animation: fadeInUp 0.6s 0.4s ease forwards;
        z-index: 100;
    }
    
    /* Course Meta Inline */
    .course-meta-inline {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 1.5rem;
        margin-bottom: 2.5rem;
        transform: translateY(20px);
        opacity: 0;
        animation: fadeInUp 0.6s 0.6s ease forwards;
    }
    
    .course-meta-inline .meta-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.9375rem;
        color: var(--light-text);
        background: rgba(255, 255, 255, 0.8);
        padding: 0.5rem 1rem;
        border-radius: 20px;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(17, 87, 64, 0.1);
    }
    
    .course-meta-inline .meta-item svg {
        width: 16px;
        height: 16px;
        color: var(--primary-color);
    }
    
    /* Course Hero Actions (matching learn-more-btn) */
    .course-hero-actions {
        display: flex;
        gap: 1rem;
        justify-content: center;
        flex-wrap: wrap;
        transform: translateY(20px);
        opacity: 0;
        animation: fadeInUp 0.6s 0.8s ease forwards;
    }
    
    .primary-action-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.75rem;
        padding: 1rem 2rem;
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
        color: white;
        text-decoration: none;
        border-radius: 50px;
        font-weight: 600;
        font-size: 1rem;
        letter-spacing: 0.02em;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 4px 20px rgba(17, 87, 64, 0.3);
        border: none;
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }
    
    .primary-action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 30px rgba(17, 87, 64, 0.4);
    }
    
    .secondary-action-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.75rem;
        padding: 1rem 2rem;
        background: rgba(255, 255, 255, 0.9);
        color: var(--primary-color);
        text-decoration: none;
        border-radius: 50px;
        font-weight: 600;
        font-size: 1rem;
        letter-spacing: 0.02em;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        backdrop-filter: blur(10px);
        border: 2px solid rgba(17, 87, 64, 0.2);
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }
    
    .secondary-action-btn:hover {
        background: var(--primary-color);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 8px 30px rgba(17, 87, 64, 0.3);
    }
    
    .secondary-action-btn svg {
        transition: transform 0.3s ease;
    }
    
    .secondary-action-btn:hover svg {
        transform: scale(1.1);
    }
    
    /* Fade In Animation */
    @keyframes fadeInUp {
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    /* Dark Mode Hero Styles */
    body.dark-mode .course-hero-content h2 {
        color: var(--dark-text-color);
    }
    
    body.dark-mode .course-hero-content p {
        color: var(--dark-light-text);
    }
    
    body.dark-mode .course-meta-inline .meta-item {
        background: rgba(30, 30, 30, 0.8);
        border-color: var(--dark-border-color);
        color: var(--dark-light-text);
    }
    
    body.dark-mode .secondary-action-btn {
        background: rgba(30, 30, 30, 0.9);
        color: var(--dark-primary-color);
        border-color: var(--dark-border-color);
    }
    
    body.dark-mode .secondary-action-btn:hover {
        background: var(--dark-primary-color);
        color: white;
    }
    
    /* Mobile Responsive */
    @media (max-width: 768px) {
        .course-hero {
            padding: calc(6rem + 75px) 1.5rem 6rem 1.5rem; /* Add mobile header height to top padding */
        }
        
        .hero-shape-1 {
            width: 300px;
            height: 300px;
            top: -150px;
            left: -75px;
        }
        
        .hero-shape-2 {
            width: 200px;
            height: 200px;
            bottom: -100px;
            right: -25px;
        }
        
        .hero-shape-3 {
            width: 150px;
            height: 150px;
        }
        
        .course-meta-inline {
            flex-direction: column;
            align-items: center;
            gap: 1rem;
        }
        
        .course-hero-actions {
            flex-direction: column;
            align-items: center;
        }
        
        .primary-action-btn,
        .secondary-action-btn {
            width: 100%;
            max-width: 280px;
            justify-content: center;
        }
    }
    
    /* Breadcrumb Navigation */
    .course-breadcrumb {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 1.5rem;
        font-size: 0.875rem;
        color: var(--light-text);
    }
    
    .course-breadcrumb a {
        color: var(--aiddata-primary);
        text-decoration: none;
        transition: color 0.2s ease;
    }
    
    .course-breadcrumb a:hover {
        color: var(--aiddata-primary-dark);
    }
    
    .course-breadcrumb svg {
        width: 12px;
        height: 12px;
        opacity: 0.5;
    }
    
    /* Hero Grid Layout */
    .course-hero-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 3rem;
        align-items: start;
    }
    
    /* Hero Left Content */
    .course-hero-left h1 {
        font-size: 2.75rem;
        font-weight: 700;
        color: var(--aiddata-primary);
        margin: 0 0 1rem 0;
        line-height: 1.2;
        letter-spacing: -0.02em;
    }
    
    .course-subtitle {
        font-size: 1.25rem;
        color: var(--light-text);
        margin-bottom: 2rem;
        line-height: 1.6;
        font-weight: 400;
    }
    
    /* Course Meta Information */
    .course-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    
    .meta-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.9375rem;
        color: #495057;
    }
    
    .meta-item svg {
        width: 18px;
        height: 18px;
        color: var(--aiddata-primary);
    }
    
    /* Course Tags */
    .course-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        margin-bottom: 2rem;
    }
    
    .course-tag {
        background: linear-gradient(135deg, rgba(0, 179, 136, 0.1) 0%, rgba(120, 157, 74, 0.1) 100%);
        color: var(--aiddata-primary);
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.8125rem;
        font-weight: 600;
        border: 1px solid rgba(0, 179, 136, 0.2);
    }
    
    /* Hero Right - Preview Card */
    .course-hero-right {
        position: sticky;
        top: 2rem;
    }
    
    .course-preview-card {
        background: var(--bg-color);
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
        border: 1px solid rgba(0, 0, 0, 0.05);
    }
    
    .course-preview-image {
        width: 100%;
        height: 200px;
        border-radius: 12px;
        object-fit: cover;
        margin-bottom: 1.5rem;
        background: linear-gradient(135deg, var(--aiddata-primary) 0%, var(--aiddata-primary-dark) 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
    }
    
    /* Pricing Section */
    .course-price {
        text-align: center;
        margin-bottom: 1.5rem;
    }
    
    .price-amount {
        font-size: 2.5rem;
        font-weight: 700;
        color: var(--aiddata-primary);
        margin-bottom: 0.5rem;
    }
    
    .price-note {
        font-size: 0.875rem;
        color: var(--light-text);
    }
    
    /* Course Actions */
    .course-actions {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        margin-bottom: 1.5rem;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, var(--aiddata-primary) 0%, var(--aiddata-primary-dark) 100%);
        color: white;
        padding: 0.875rem 1.5rem;
        border-radius: 8px;
        font-weight: 600;
        text-decoration: none;
        text-align: center;
        border: none;
        cursor: pointer;
        transition: all 0.2s ease;
        font-size: 0.9375rem;
    }
    
    .btn-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 16px rgba(2, 100, 71, 0.3);
    }
    
    .btn-secondary {
        background: transparent;
        color: var(--aiddata-primary);
        padding: 0.875rem 1.5rem;
        border-radius: 8px;
        font-weight: 600;
        text-decoration: none;
        text-align: center;
        border: 2px solid var(--aiddata-primary);
        cursor: pointer;
        transition: all 0.2s ease;
        font-size: 0.9375rem;
    }
    
    .btn-secondary:hover {
        background: var(--aiddata-primary);
        color: white;
        transform: translateY(-1px);
    }
    
    /* Course Features List */
    .course-features {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .course-features li {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.5rem 0;
        font-size: 0.875rem;
        color: #495057;
    }
    
    .course-features li::before {
        content: '✓';
        color: var(--aiddata-primary);
        font-weight: 700;
        width: 16px;
        text-align: center;
    }
    
     /* Main Content Section */
     .course-content {
         max-width: 1200px;
         margin: 0 auto;
         padding: 4rem 20px;
     }
     
     .content-grid {
         display: grid;
         grid-template-columns: 2fr 1fr;
         gap: 3rem;
     }
     
     .content-main {
         background: var(--bg-color);
         border-radius: 16px;
         padding: 0;
         box-shadow: 0 4px 16px rgba(0, 0, 0, 0.05);
         overflow: hidden;
     }
     
     /* Course Tabs */
     .course-tabs {
         display: flex;
         border-bottom: 1px solid var(--border-color);
         background: var(--bg-color);
     }
     
     .tab-button {
         background: none;
         border: none;
         padding: 1rem 2rem;
         font-size: 0.9375rem;
         font-weight: 500;
         color: var(--light-text);
         cursor: pointer;
         border-bottom: 2px solid transparent;
         transition: all 0.2s ease;
         position: relative;
     }
     
     .tab-button.active {
         color: var(--primary-color);
         border-bottom-color: var(--primary-color);
         background: rgba(17, 87, 64, 0.02);
     }
     
     .tab-button:hover {
         color: var(--primary-color);
         background: rgba(17, 87, 64, 0.05);
     }
     
     /* Tab Content */
     .tab-content {
         display: none;
         padding: 2.5rem;
     }
     
     .tab-content.active {
         display: block;
     }
     
     /* Course Info Section */
     .course-info-section {
         max-width: none;
     }
     
     .course-description {
         font-size: 1rem;
         line-height: 1.6;
         color: #495057;
         margin-bottom: 2rem;
     }
     
     .subsection-title {
         font-size: 1.25rem;
         font-weight: 600;
         color: var(--text-color);
         margin: 2rem 0 1.5rem 0;
         display: flex;
         align-items: center;
         gap: 0.5rem;
     }
     
     .subsection-title svg {
         width: 20px;
         height: 20px;
         color: var(--primary-color);
     }
     
     /* Learning Section */
     .learning-section {
         margin-bottom: 2.5rem;
     }
     
     .learning-list {
         list-style: none;
         padding: 0;
         margin: 0;
         display: grid;
         grid-template-columns: 1fr 1fr;
         gap: 1rem;
     }
     
     .learning-list li {
         display: flex;
         align-items: center;
         gap: 0.75rem;
         padding: 0.75rem;
         background: rgba(0, 179, 136, 0.03);
         border-radius: 8px;
         font-size: 0.9375rem;
         color: #495057;
     }
     
     .learning-list li svg {
         width: 16px;
         height: 16px;
         color: var(--primary-color);
         flex-shrink: 0;
     }
     
     /* Curriculum Section */
     .curriculum-section {
         margin-bottom: 2.5rem;
     }
     
     .curriculum-header {
         display: flex;
         justify-content: space-between;
         align-items: center;
         margin-bottom: 1.5rem;
     }
     
     .expand-all-btn {
         background: none;
         border: 1px solid var(--border-color);
         padding: 0.5rem;
         border-radius: 6px;
         cursor: pointer;
         color: var(--light-text);
         transition: all 0.2s ease;
     }
     
     .expand-all-btn:hover {
         border-color: var(--primary-color);
         color: var(--primary-color);
     }
     
     .expand-all-btn svg {
         width: 16px;
         height: 16px;
     }
     
     .curriculum-list {
         border: 1px solid var(--border-color);
         border-radius: 8px;
         overflow: hidden;
     }
     
     .curriculum-item {
         border-bottom: 1px solid var(--border-color);
     }
     
     .curriculum-item:last-child {
         border-bottom: none;
     }
     
     .curriculum-header-item {
         display: flex;
         justify-content: space-between;
         align-items: center;
         padding: 1rem 1.5rem;
         background: var(--bg-color);
         cursor: pointer;
         transition: background-color 0.2s ease;
     }
     
     .curriculum-header-item:hover {
         background: rgba(0, 0, 0, 0.02);
     }
     
     .curriculum-title {
         display: flex;
         align-items: center;
         gap: 0.75rem;
         font-weight: 500;
         color: var(--text-color);
     }
     
     .module-number {
         font-size: 0.875rem;
         color: var(--light-text);
         font-weight: 600;
     }
     
     .curriculum-meta {
         display: flex;
         align-items: center;
         gap: 1rem;
     }
     
     .duration {
         font-size: 0.875rem;
         color: var(--light-text);
     }
     
     .toggle-btn {
         background: none;
         border: none;
         padding: 0.25rem;
         cursor: pointer;
         color: var(--light-text);
         transition: color 0.2s ease;
     }
     
     .toggle-btn:hover {
         color: var(--primary-color);
     }
     
     .toggle-btn svg {
         width: 16px;
         height: 16px;
     }
     
     .curriculum-content {
         padding: 0 1.5rem 1rem 1.5rem;
         background: rgba(0, 0, 0, 0.01);
         border-top: 1px solid var(--border-color);
     }
     
     .lesson-item {
         display: flex;
         align-items: center;
         gap: 0.75rem;
         padding: 0.75rem 0;
         border-bottom: 1px solid rgba(0, 0, 0, 0.05);
         font-size: 0.875rem;
     }
     
     .lesson-item:last-child {
         border-bottom: none;
     }
     
     .lesson-item svg {
         width: 16px;
         height: 16px;
         color: var(--light-text);
         flex-shrink: 0;
     }
     
     .lesson-title {
         flex: 1;
         color: var(--text-color);
     }
     
     .lesson-duration {
         color: var(--light-text);
         font-size: 0.8125rem;
         margin-left: auto;
         margin-right: 0.5rem;
     }
     
     .preview-btn {
         background: none;
         border: none;
         padding: 0.25rem;
         cursor: pointer;
         color: var(--primary-color);
         opacity: 0.7;
         transition: opacity 0.2s ease;
     }
     
     .preview-btn:hover {
         opacity: 1;
     }
     
     .preview-btn svg {
         width: 14px;
         height: 14px;
     }
     
     .lock-icon {
         width: 14px;
         height: 14px;
         color: var(--light-text);
         opacity: 0.5;
     }
     
     /* Sidebar Styles */
     .content-sidebar {
         display: flex;
         flex-direction: column;
         gap: 2rem;
     }
     
     .sidebar-card {
         background: var(--bg-color);
         border-radius: 16px;
         padding: 2rem;
         box-shadow: 0 4px 16px rgba(0, 0, 0, 0.05);
         border: 1px solid rgba(0, 0, 0, 0.05);
     }
     
     .sidebar-title {
         font-size: 1.125rem;
         font-weight: 600;
         color: var(--text-color);
         margin: 0 0 1.5rem 0;
     }
     
     /* Instructor Card Sidebar */
     .instructor-card-sidebar {
         text-align: center;
     }
     
     .instructor-profile {
         display: flex;
         flex-direction: column;
         align-items: center;
         text-align: center;
     }
     
     .instructor-avatar-large {
         width: 80px;
         height: 80px;
         border-radius: 50%;
         object-fit: cover;
         margin-bottom: 1rem;
         border: 3px solid rgba(17, 87, 64, 0.1);
     }
     
     .instructor-name {
         font-size: 1.125rem;
         font-weight: 600;
         color: var(--text-color);
         margin: 0 0 0.5rem 0;
     }
     
     .instructor-title {
         font-size: 0.9375rem;
         color: var(--light-text);
         margin: 0 0 1rem 0;
     }
     
     .instructor-bio {
         font-size: 0.875rem;
         color: var(--light-text);
         line-height: 1.5;
         margin: 0;
     }
     
     /* Material List */
     .material-list {
         list-style: none;
         padding: 0;
         margin: 0;
     }
     
     .material-list li {
         display: flex;
         align-items: center;
         gap: 0.75rem;
         padding: 0.75rem 0;
         font-size: 0.9375rem;
         color: var(--text-color);
         border-bottom: 1px solid rgba(0, 0, 0, 0.05);
     }
     
     .material-list li:last-child {
         border-bottom: none;
     }
     
     .material-list li svg {
         width: 18px;
         height: 18px;
         color: var(--primary-color);
         flex-shrink: 0;
     }
     
     /* Requirements List */
     .requirements-list {
         list-style: none;
         padding: 0;
         margin: 0;
     }
     
     .requirements-list li {
         display: flex;
         align-items: flex-start;
         gap: 0.75rem;
         padding: 0.5rem 0;
         font-size: 0.9375rem;
         color: var(--text-color);
         line-height: 1.5;
     }
     
     .requirements-list li::before {
         content: '•';
         color: var(--primary-color);
         font-weight: bold;
         flex-shrink: 0;
         margin-top: 0.1rem;
     }
     
     /* Tags Container */
     .tags-container {
         display: flex;
         flex-wrap: wrap;
         gap: 0.5rem;
     }
     
     .tag {
         background: rgba(17, 87, 64, 0.08);
         color: var(--primary-color);
         padding: 0.375rem 0.75rem;
         border-radius: 20px;
         font-size: 0.8125rem;
         font-weight: 500;
         border: 1px solid rgba(17, 87, 64, 0.15);
         transition: all 0.2s ease;
     }
     
     .tag:hover {
         background: rgba(17, 87, 64, 0.12);
         transform: translateY(-1px);
     }
     
     /* Target Audience List */
     .target-audience-list {
         list-style: none;
         padding: 0;
         margin: 0;
     }
     
     .target-audience-list li {
         display: flex;
         align-items: flex-start;
         gap: 0.75rem;
         padding: 0.5rem 0;
         font-size: 0.9375rem;
         color: var(--text-color);
         line-height: 1.5;
     }
     
     .target-audience-list li::before {
         content: '•';
         color: var(--primary-color);
         font-weight: bold;
         flex-shrink: 0;
         margin-top: 0.1rem;
     }
     
     /* Reviews Section */
     .reviews-section {
         text-align: center;
         padding: 3rem 0;
     }
     
     .reviews-placeholder {
         color: var(--light-text);
         font-style: italic;
         margin: 0;
     }
    
    /* Typography */
    .section-title {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--aiddata-primary);
        margin: 0 0 1.5rem 0;
        letter-spacing: -0.02em;
    }
    
    .section-subtitle {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--aiddata-primary);
        margin: 2rem 0 1rem 0;
    }
    
    .content-text {
        font-size: 1.0625rem;
        line-height: 1.7;
        color: #495057;
        margin-bottom: 1.5rem;
    }
    
    /* Highlight Box */
    .highlight-box {
        background: linear-gradient(135deg, rgba(0, 179, 136, 0.05) 0%, rgba(120, 157, 74, 0.05) 100%);
        border-left: 4px solid var(--aiddata-secondary);
        padding: 1.5rem;
        margin: 2rem 0;
        border-radius: 0 8px 8px 0;
    }
    
    .highlight-box h4 {
        color: var(--aiddata-primary);
        margin: 0 0 1rem 0;
        font-size: 1.125rem;
        font-weight: 600;
    }
    
    .highlight-box p {
        margin: 0;
        color: #495057;
        line-height: 1.6;
    }
    
    /* Learning Outcomes */
    .learning-outcomes {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .learning-outcomes li {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        margin-bottom: 1rem;
        padding: 1rem;
        background: rgba(0, 179, 136, 0.03);
        border-radius: 8px;
        border-left: 3px solid var(--aiddata-secondary);
    }
    
    .learning-outcomes li::before {
        content: '🎯';
        font-size: 1.2rem;
        margin-top: 0.1rem;
    }
    
    /* Instructor Cards */
    .instructor-card {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1.5rem;
        background: rgba(0, 179, 136, 0.03);
        border-radius: 12px;
        margin-bottom: 1rem;
    }
    
    .instructor-avatar {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid white;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
    
    .instructor-info h4 {
        margin: 0 0 0.25rem 0;
        color: var(--aiddata-primary);
        font-size: 1rem;
        font-weight: 600;
    }
    
    .instructor-info p {
        margin: 0;
        color: var(--light-text);
        font-size: 0.875rem;
    }
    
    /* Course Modules */
    .course-modules {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .module-item {
        background: var(--bg-color);
        border: 1px solid rgba(0, 0, 0, 0.08);
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1rem;
        transition: all 0.2s ease;
    }
    
    .module-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
        border-color: rgba(2, 100, 71, 0.2);
    }
    
    .module-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 0.75rem;
    }
    
    .module-number {
        background: linear-gradient(135deg, var(--aiddata-primary) 0%, var(--aiddata-primary-dark) 100%);
        color: white;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.875rem;
    }
    
    .module-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--aiddata-primary);
        margin: 0;
    }
    
    .module-duration {
        color: var(--light-text);
        font-size: 0.875rem;
        margin-left: auto;
    }
    
    .module-description {
        color: #495057;
        line-height: 1.6;
        margin: 0;
    }
    
    /* Partnership Section */
    .partnership-section {
        text-align: center;
        padding: 1.5rem;
        background: rgba(17, 87, 64, 0.03);
        border-radius: 12px;
        border: 1px solid rgba(17, 87, 64, 0.1);
    }
    
    .partnership-logo {
        height: 40px;
        margin-bottom: 0.75rem;
    }
    
    .partnership-text {
        font-size: 0.875rem;
        color: var(--light-text);
        margin: 0;
    }
    
    /* Modal Styles */
    .video-modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.9);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 10000;
        backdrop-filter: blur(4px);
    }
    
    .video-modal .video-container {
        position: relative;
        width: 90%;
        max-width: 900px;
        aspect-ratio: 16 / 9;
        background: #000;
        border-radius: 12px;
        overflow: hidden;
    }
    
    .video-modal .close-modal {
        position: absolute;
        top: 20px;
        right: 20px;
        background: rgba(255, 255, 255, 0.2);
        color: white;
        border: none;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        font-size: 1.5rem;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .video-modal .close-modal:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: scale(1.1);
    }
    
    /* Footer Styles - Matching Front Page Exactly */
    .site-footer {
        background-color: var(--primary-color);
        padding: 4rem 2rem 0;
        margin-top: 4rem;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .footer-content {
        max-width: 1200px;
        margin: 0 auto;
        display: grid;
        grid-template-columns: 2fr 1fr 1fr 1fr;
        gap: 3rem;
        padding-bottom: 3rem;
    }
    
    .footer-section {
        display: flex;
        flex-direction: column;
    }
    
    .footer-section:first-child {
        max-width: 320px;
    }
    
    .footer-logo {
        width: 140px;
        height: auto;
        margin-bottom: 1.25rem;
    }
    
     .footer-section h4 {
         color: #d2bb93;
         font-size: 1rem;
         font-weight: 500;
         margin: 0 0 1.25rem;
         font-family: var(--font-family);
         text-transform: uppercase;
     }
     
     .footer-section p {
         color: rgba(255, 255, 255, 0.8);
         font-size: 0.95rem;
         line-height: 1.5;
         margin: 0 0 1.5rem;
         font-family: var(--font-family);
     }
    
    .footer-section ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .footer-section ul li {
        margin-bottom: 0.75rem;
    }
    
    .footer-section ul li:last-child {
        margin-bottom: 0;
    }
    
    .footer-section ul a {
        color: rgba(255, 255, 255, 0.8);
        text-decoration: none;
        font-size: 0.95rem;
        transition: color 0.2s ease;
    }
    
    .footer-section ul a:hover {
        color: white;
    }
    
    .footer-section address {
        font-style: normal;
        color: rgba(255, 255, 255, 0.8);
        font-size: 0.95rem;
        line-height: 1.5;
    }
    
    .footer-section address a {
        color: #ffffff;
        text-decoration: none;
        transition: color 0.2s ease;
    }
    
    .footer-section address a:hover {
        color: #d2bb93;
    }
    
    .social-links {
        display: flex;
        gap: 16px;
        margin-top: 10px;
    }
    
    .social-links a {
        color: rgba(255, 255, 255, 0.8);
        transition: color 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
    }
    
    .social-links a:hover {
        color: white;
        transform: translateY(-2px);
    }
    
    .social-links svg {
        width: 20px;
        height: 20px;
    }
    
     .newsletter-button {
         display: block;
         background-color: #026447;
         color: white;
         padding: 10px 18px;
         border-radius: 6px;
         border: none;
         cursor: pointer;
         margin-top: 20px;
         font-weight: 600;
         transition: all 0.3s ease;
         font-family: var(--font-family);
         font-size: 13px;
         width: fit-content;
         box-shadow: 0 2px 4px rgba(0,0,0,0.15);
         letter-spacing: 0.3px;
         text-transform: uppercase;
     }
    
    .newsletter-button:hover {
        background-color: #004E38;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }
    
    .footer-bottom {
        background-color: rgba(0, 0, 0, 0.1);
        padding: 1.5rem 2rem;
        text-align: center;
    }
    
    .footer-bottom-content {
        max-width: 1200px;
        margin: 0 auto;
        display: flex;
        justify-content: center;
        align-items: center;
    }
    
    .footer-bottom-logo {
        max-height: 60px;
        opacity: 0.9;
        transition: opacity 0.2s ease;
    }
    
    .footer-bottom-logo:hover {
        opacity: 1;
    }
    
    /* Dark Mode Footer */
    body.dark-mode .site-footer {
        background-color: var(--dark-footer-bg);
        color: var(--dark-footer-text);
    }
    
    body.dark-mode .footer-section h4 {
        color: var(--dark-text-color);
    }
    
    body.dark-mode .footer-section p {
        color: var(--dark-light-text);
    }
    
    body.dark-mode .footer-section ul a {
        color: var(--dark-light-text);
    }
    
    body.dark-mode .footer-section ul a:hover {
        color: rgba(255, 255, 255, 0.9);
    }
    
    body.dark-mode .footer-bottom {
        background-color: #1a1a1a;
    }
    
    /* Responsive Footer */
    @media (max-width: 1024px) {
        .footer-content {
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
        }
    }
    
    @media (max-width: 768px) {
        .site-footer {
            padding: 3rem 1rem 0;
        }
        
        .footer-content {
            grid-template-columns: 1fr;
            gap: 2rem;
            text-align: center;
        }
        
        .footer-section:first-child {
            max-width: none;
        }
        
        .social-links {
            justify-content: center;
        }
        
        .newsletter-button {
            margin: 20px auto 0;
        }
        
        .footer-bottom {
            padding: 1rem;
        }
    }
    
    @media (max-width: 480px) {
        .site-footer {
            padding: 2rem 1rem 0;
        }
        
        .footer-content {
            gap: 1.5rem;
        }
        
        .footer-section h4 {
            font-size: 0.9rem;
        }
        
        .footer-section p,
        .footer-section ul a {
            font-size: 0.875rem;
        }
    }
    
    /* Dark Mode Course Styles */
    body.dark-mode .course-preview-card,
    body.dark-mode .content-main,
    body.dark-mode .sidebar-card {
        background: var(--dark-card-bg);
        color: var(--dark-text-color);
    }
    
    body.dark-mode .section-title,
    body.dark-mode .section-subtitle {
        color: var(--dark-text-color);
    }
    
    body.dark-mode .content-text {
        color: var(--dark-light-text);
    }
    
    /* Responsive Design */
    @media (max-width: 1200px) {
        .header-content {
            padding: 0 2rem;
            gap: 2rem;
        }
    }
    
    @media (max-width: 1024px) {
        .course-hero-grid,
        .content-grid {
            grid-template-columns: 1fr;
            gap: 2rem;
        }
        
        .course-hero-right {
            position: static;
        }
        
        .course-hero-left h1 {
            font-size: 2.25rem;
        }
        
        .header-content {
            grid-template-columns: 1fr;
            gap: 1rem;
            text-align: center;
        }
        
        .header-actions {
            justify-self: center;
        }
    }
    
     @media (max-width: 768px) {
         .lms-header {
             padding: 1rem 0;
         }
         
         .header-content {
             padding: 0 1rem;
         }
         
         .header-actions {
             flex-direction: column;
             gap: 0.75rem;
         }
         
         .header-button {
             padding: 0.625rem 1.25rem;
             font-size: 0.875rem;
         }
         
         .lms-main {
             margin-top: 0;
         }
         
         .course-hero {
             padding: 2rem 0 1.5rem 0;
         }
         
         .course-hero-content,
         .course-content {
             padding-left: 1rem;
             padding-right: 1rem;
         }
         
         .course-hero-left h1 {
             font-size: 1.875rem;
         }
         
         .course-subtitle {
             font-size: 1.125rem;
         }
         
         .content-main {
             border-radius: 12px;
         }
         
         .sidebar-card {
             padding: 1.5rem;
             border-radius: 12px;
         }
         
         .course-meta {
             flex-direction: column;
             gap: 1rem;
         }
         
         .course-actions {
             flex-direction: column;
         }
         
         .instructor-card {
             flex-direction: column;
             text-align: center;
         }
         
         .module-header {
             flex-wrap: wrap;
         }
         
         .module-duration {
             margin-left: 0;
             margin-top: 0.5rem;
         }
         
         .footer-content {
             grid-template-columns: 1fr;
             gap: 2rem;
             padding: 0 1rem;
         }
         
         /* Mobile tab styles */
         .course-tabs {
             flex-direction: column;
         }
         
         .tab-button {
             padding: 0.75rem 1.5rem;
             border-bottom: 1px solid var(--border-color);
             border-right: none;
         }
         
         .tab-button.active {
             border-bottom-color: var(--primary-color);
             border-left: 3px solid var(--primary-color);
         }
         
         .tab-content {
             padding: 1.5rem;
         }
         
         /* Mobile learning list */
         .learning-list {
             grid-template-columns: 1fr;
             gap: 0.75rem;
         }
         
         /* Mobile curriculum */
         .curriculum-header-item {
             padding: 0.75rem 1rem;
         }
         
         .curriculum-content {
             padding: 0 1rem 0.75rem 1rem;
         }
         
         .curriculum-title {
             font-size: 0.9375rem;
         }
         
         .lesson-item {
             padding: 0.5rem 0;
             font-size: 0.8125rem;
         }
         
         /* Mobile sidebar */
         .instructor-avatar-large {
             width: 60px;
             height: 60px;
         }
         
         .material-list li,
         .requirements-list li,
         .target-audience-list li {
             font-size: 0.875rem;
         }
         
         .tags-container {
             gap: 0.375rem;
         }
         
         .tag {
             font-size: 0.75rem;
             padding: 0.25rem 0.5rem;
         }
     }
    
    @media (max-width: 480px) {
        .header-content {
            padding: 0 0.75rem;
        }
        
        .header-actions {
            gap: 0.5rem;
        }
        
        .course-hero-left h1 {
            font-size: 1.625rem;
        }
        
        .content-main,
        .sidebar-card {
            padding: 1rem;
        }
        
        .course-preview-card {
            padding: 1rem;
        }
        
        .price-amount {
            font-size: 2rem;
        }
        
        .section-title {
            font-size: 1.5rem;
        }
        
        .section-subtitle {
            font-size: 1.125rem;
        }
        
        .instructor-card {
            flex-direction: column;
            text-align: center;
        }
    }
    
    /* Accessibility */
    @media (prefers-reduced-motion: reduce) {
        * {
            transition: none !important;
            animation: none !important;
        }
    }
    
    /* High contrast mode */
    @media (prefers-contrast: high) {
        :root {
            --aiddata-primary: #004d36;
            --aiddata-primary-dark: #003d2b;
            --text-color: #000000;
            --light-text: #222222;
        }
    }
    
    /* Print styles */
    @media print {
        .lms-header,
        .loading-screen,
        .video-modal,
        .course-actions,
        .btn-primary,
        .btn-secondary {
            display: none !important;
        }
        
        .lms-main {
            margin-top: 0;
        }
        
        .course-hero {
            background: none !important;
            padding: 1rem 0;
        }
        
        .course-hero-grid,
        .content-grid {
            grid-template-columns: 1fr;
            gap: 1rem;
        }
        
        * {
            color: #000 !important;
            background: #fff !important;
        }
    }
</style>

<!-- Loading Screen -->
<div class="loading-screen" id="loadingScreen">
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
        <p class="loading-text">Loading Course</p>
    </div>
</div>

<header class="lms-header">
    <div class="header-content">
        <div class="logo-section">
            <a href="https://www.aiddata.org" target="_blank">
                <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/logodark.png" alt="AidData Logo" class="logo">
            </a>
        </div>
        
        <div class="header-actions">
            <div class="auth-only" style="display: none;">
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
                                <span class="user-name">Your Name</span>
                                <span class="user-email">your.email@example.com</span>
                            </div>
                        </div>
                        <a href="<?php echo esc_url(home_url('/')); ?>" class="dropdown-item">Home</a>
                        <a href="<?php echo esc_url(home_url('/lp-profile/')); ?>" class="dropdown-item">My Account</a>
                        <button class="dropdown-item logout-button">Sign Out</button>
                    </div>
                </div>
            </div>
            <div class="guest-only" style="display: flex;">
                <button class="header-button login-button">Log In</button>
                <button class="header-button signup-button">Sign Up</button>
            </div>
        </div>
    </div>
</header>

<main class="lms-main">
    <!-- Course Hero Section -->
    <section class="course-hero">
        <div class="course-hero-content">
            <!-- Course Badge -->
            <div class="course-badge">
                <?php echo esc_html(course_get_field('course_category', false, 'Course')); ?>
            </div>
            
            <!-- Main Course Title -->
            <h2><?php the_title(); ?></h2>
            
            <!-- Course Description -->
            <p><?php echo esc_html(course_get_field('course_subtitle', false, get_the_excerpt())); ?></p>
            
            <!-- Course Meta Information -->
            <div class="course-meta-inline">
                <?php if (course_get_field('course_duration')) : ?>
                <span class="meta-item">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/>
                        <polyline points="12 6 12 12 16 14"/>
                    </svg>
                    <?php echo esc_html(course_get_field('course_duration')); ?>
                </span>
                <?php endif; ?>
                
                <?php if (course_get_field('course_level')) : ?>
                <span class="meta-item">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                    </svg>
                    <?php echo esc_html(course_get_field('course_level')); ?>
                </span>
                <?php endif; ?>
                
                <?php if (course_get_field('course_students')) : ?>
                <span class="meta-item">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                    <?php echo esc_html(course_get_field('course_students')); ?> students
                </span>
                <?php endif; ?>
            </div>
            
            <!-- Course Action Buttons -->
            <div class="course-hero-actions">
                <?php if (course_get_field('enroll_button_url')) : ?>
                <a href="<?php echo esc_url(course_get_field('enroll_button_url')); ?>" class="primary-action-btn">
                    <span><?php echo esc_html(course_get_field('enroll_button_text', false, 'Enroll Now')); ?></span>
                </a>
                <?php endif; ?>
                
                <?php if (course_get_field('preview_button_enabled')) : ?>
                <button class="secondary-action-btn trailer-button" data-video="<?php echo esc_url(course_get_field('course_video')); ?>">
                    <span>Preview Course</span>
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2">
                        <polygon points="5 3 19 12 5 21 5 3"/>
                    </svg>
                </button>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Hero Shapes (matching front-page welcome shapes) -->
        <div class="course-hero-shapes">
            <div class="hero-shape hero-shape-1"></div>
            <div class="hero-shape hero-shape-2"></div>
            <div class="hero-shape hero-shape-3"></div>
        </div>
    </section>
    
     <!-- Course Content Section -->
     <section class="course-content">
         <div class="content-grid">
             <div class="content-main">
                 <!-- Course Info Tabs -->
                 <div class="course-tabs">
                     <button class="tab-button active" data-tab="info">Course Info</button>
                     <button class="tab-button" data-tab="reviews">Reviews</button>
                 </div>
                 
                 <!-- Course Info Tab Content -->
                 <div class="tab-content active" id="info-tab">
                     <div class="course-info-section">
                         <h2 class="section-title">About Course</h2>
                         
                         <?php if (get_the_content()) : ?>
                         <div class="course-description">
                             <?php the_content(); ?>
                         </div>
                         <?php endif; ?>
                         
                         <?php if (course_get_field('course_overview')) : ?>
                         <div class="course-description">
                             <?php echo wp_kses_post(course_get_field('course_overview')); ?>
                         </div>
                         <?php endif; ?>
                         
                         <?php if (course_has_rows('learning_outcomes')) : ?>
                         <div class="learning-section">
                             <h3 class="subsection-title">What Will I Learn?</h3>
                             <ul class="learning-list">
                                 <?php 
                                 if (function_exists('have_rows')) :
                                     while (have_rows('learning_outcomes')) : the_row(); 
                                 ?>
                                 <li>
                                     <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                         <polyline points="20 6 9 17 4 12"/>
                                     </svg>
                                     <span><?php echo esc_html(get_sub_field('outcome_title')); ?></span>
                                 </li>
                                 <?php 
                                     endwhile;
                                 endif;
                                 ?>
                             </ul>
                         </div>
                         <?php endif; ?>
                         
                         <?php if (course_has_rows('course_modules')) : ?>
                         <div class="curriculum-section">
                             <div class="curriculum-header">
                                 <h3 class="subsection-title">
                                     <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                         <path d="M9 11H5a2 2 0 0 0-2 2v3c0 1.1.9 2 2 2h4m6-6h4a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2h-4m-6 0a2 2 0 0 0-2-2v-3a2 2 0 0 0 2-2m6 0a2 2 0 0 1 2-2v-3a2 2 0 0 1-2-2"/>
                                     </svg>
                                     Overview
                                 </h3>
                                 <button class="expand-all-btn">
                                     <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                         <path d="M6 9l6 6 6-6"/>
                                     </svg>
                                 </button>
                             </div>
                             
                             <div class="curriculum-list">
                                 <?php 
                                 $module_count = 1;
                                 if (function_exists('have_rows')) :
                                     while (have_rows('course_modules')) : the_row(); 
                                 ?>
                                 <div class="curriculum-item">
                                     <div class="curriculum-header-item">
                                         <div class="curriculum-title">
                                             <span class="module-number"><?php echo sprintf('%02d', $module_count); ?>.</span>
                                             <span class="module-title"><?php echo esc_html(get_sub_field('module_title')); ?></span>
                                         </div>
                                         <div class="curriculum-meta">
                                             <?php if (get_sub_field('module_duration')) : ?>
                                             <span class="duration"><?php echo esc_html(get_sub_field('module_duration')); ?></span>
                                             <?php endif; ?>
                                             <button class="toggle-btn">
                                                 <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                     <path d="M6 9l6 6 6-6"/>
                                                 </svg>
                                             </button>
                                         </div>
                                     </div>
                                     <?php if (get_sub_field('module_description')) : ?>
                                     <div class="curriculum-content">
                                         <div class="lesson-item">
                                             <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                 <polygon points="23 7 16 12 23 17 23 7"/>
                                                 <rect x="1" y="5" width="15" height="14" rx="2" ry="2"/>
                                             </svg>
                                             <span class="lesson-title">Introduction</span>
                                             <span class="lesson-duration">1m 10s</span>
                                             <button class="preview-btn">
                                                 <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                     <circle cx="12" cy="12" r="3"/>
                                                     <path d="M12 1v6M12 17v6M4.22 4.22l4.24 4.24M15.54 15.54l4.24 4.24M1 12h6M17 12h6M4.22 19.78l4.24-4.24M15.54 8.46l4.24-4.24"/>
                                                 </svg>
                                             </button>
                                         </div>
                                         <div class="lesson-item">
                                             <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                 <polygon points="23 7 16 12 23 17 23 7"/>
                                                 <rect x="1" y="5" width="15" height="14" rx="2" ry="2"/>
                                             </svg>
                                             <span class="lesson-title"><?php echo esc_html(get_sub_field('module_description')); ?></span>
                                             <span class="lesson-duration">1m 10s</span>
                                             <svg class="lock-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                 <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                                 <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                                             </svg>
                                         </div>
                                     </div>
                                     <?php endif; ?>
                                 </div>
                                 <?php 
                                     $module_count++;
                                     endwhile;
                                 endif;
                                 ?>
                             </div>
                         </div>
                         <?php endif; ?>
                     </div>
                 </div>
                 
                 <!-- Reviews Tab Content -->
                 <div class="tab-content" id="reviews-tab">
                     <div class="reviews-section">
                         <h2 class="section-title">Student Reviews</h2>
                         <p class="reviews-placeholder">Reviews will be displayed here once available.</p>
                     </div>
                 </div>
             </div>
             
             <div class="content-sidebar">
                 <!-- Course by Instructor -->
                 <?php if (course_has_rows('course_instructors')) : ?>
                 <div class="sidebar-card instructor-card-sidebar">
                     <h3 class="sidebar-title">A course by</h3>
                     <?php 
                     if (function_exists('have_rows')) :
                         while (have_rows('course_instructors')) : the_row();
                             $instructor_image = get_sub_field('instructor_image');
                     ?>
                     <div class="instructor-profile">
                         <?php if ($instructor_image) : ?>
                         <img src="<?php echo esc_url($instructor_image); ?>" alt="<?php echo esc_attr(get_sub_field('instructor_name')); ?>" class="instructor-avatar-large">
                         <?php endif; ?>
                         <div class="instructor-details">
                             <h4 class="instructor-name"><?php echo esc_html(get_sub_field('instructor_name')); ?></h4>
                             <p class="instructor-title"><?php echo esc_html(get_sub_field('instructor_title')); ?></p>
                             <?php if (get_sub_field('instructor_bio')) : ?>
                             <p class="instructor-bio"><?php echo esc_html(get_sub_field('instructor_bio')); ?></p>
                             <?php endif; ?>
                         </div>
                     </div>
                     <?php 
                         endwhile;
                     endif;
                     ?>
                 </div>
                 <?php endif; ?>
                 
                 <!-- Material Includes -->
                 <div class="sidebar-card">
                     <ul class="material-list">
                         <li>
                             <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                 <polygon points="23 7 16 12 23 17 23 7"/>
                                 <rect x="1" y="5" width="15" height="14" rx="2" ry="2"/>
                             </svg>
                             <span><?php echo esc_html(course_get_field('course_duration', false, '34 hours')); ?> on-demand video</span>
                         </li>
                         <li>
                             <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                 <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                 <polyline points="14 2 14 8 20 8"/>
                                 <line x1="16" y1="13" x2="8" y2="13"/>
                                 <line x1="16" y1="17" x2="8" y2="17"/>
                                 <polyline points="10 9 9 9 8 9"/>
                             </svg>
                             <span><?php echo esc_html(course_get_field('articles_count', false, '45')); ?> articles</span>
                         </li>
                         <li>
                             <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                 <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                             </svg>
                             <span>Full lifetime access</span>
                         </li>
                         <li>
                             <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                 <rect x="2" y="3" width="20" height="14" rx="2" ry="2"/>
                                 <line x1="8" y1="21" x2="16" y2="21"/>
                                 <line x1="12" y1="17" x2="12" y2="21"/>
                             </svg>
                             <span>Access on mobile and TV</span>
                         </li>
                     </ul>
                 </div>
                 
                 <!-- Requirements -->
                 <?php if (course_get_field('course_requirements')) : ?>
                 <div class="sidebar-card">
                     <h3 class="sidebar-title">Requirements</h3>
                     <ul class="requirements-list">
                         <li><?php echo wp_kses_post(course_get_field('course_requirements')); ?></li>
                     </ul>
                 </div>
                 <?php endif; ?>
                 
                 <!-- Tags -->
                 <div class="sidebar-card">
                     <h3 class="sidebar-title">Tags</h3>
                     <div class="tags-container">
                         <?php if (course_get_field('course_type')) : ?>
                         <span class="tag"><?php echo esc_html(course_get_field('course_type')); ?></span>
                         <?php endif; ?>
                         
                         <?php if (course_get_field('badge_type')) : ?>
                         <span class="tag"><?php echo esc_html(course_get_field('badge_type')); ?></span>
                         <?php endif; ?>
                         
                         <?php if (course_get_field('course_format')) : ?>
                         <span class="tag"><?php echo esc_html(course_get_field('course_format')); ?></span>
                         <?php endif; ?>
                         
                         <span class="tag">Craft</span>
                         <span class="tag">Illustration</span>
                         <span class="tag">Graphic Design</span>
                         <span class="tag">UI & UX</span>
                     </div>
                 </div>
                 
                 <!-- Target Audience -->
                 <div class="sidebar-card">
                     <h3 class="sidebar-title">Target Audience</h3>
                     <ul class="target-audience-list">
                         <li>Understand the core concepts</li>
                         <li>Start a drop shipping store</li>
                         <li>Learn the formulas to making profits</li>
                     </ul>
                 </div>
             </div>
         </div>
     </section>

    <!-- Video Modal -->
    <div class="video-modal" id="courseTrailer" role="dialog" aria-hidden="true" aria-labelledby="courseTrailerTitle">
        <button class="close-modal" aria-label="Close video">×</button>
        <h3 id="courseTrailerTitle" class="sr-only">Course Preview</h3>
        <div class="video-container">
            <video 
                controls
                preload="none"
                style="width: 100%; height: 100%;"
                controlsList="nodownload"
                oncontextmenu="return false;">
                <source src="" type="video/mp4">
                Your browser does not support the video tag.
            </video>
        </div>
    </div>
</main>

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
  
    <hr style="border: 0; height: 1px; background-color: white; width: 100%; margin: 20px 0;">
  
    <div class="footer-bottom" style="background: transparent;">
        <div class="footer-bottom-content">
            <a href="https://www.wm.edu" target="_blank" rel="noopener noreferrer">
                <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/wm_logo_white.png" alt="William & Mary Logo" class="footer-bottom-logo" style="max-height: 60px;">
            </a>
        </div>
    </div>
</footer>

<?php
get_footer();
?>

<!-- Enhanced JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Loading screen
    const loadingScreen = document.getElementById('loadingScreen');
    
    window.addEventListener('load', function() {
        if (loadingScreen) {
            loadingScreen.style.opacity = '0';
            setTimeout(() => {
                loadingScreen.style.display = 'none';
            }, 500);
        }
    });
    
    // Header dropdown functionality
    const menuButton = document.querySelector('.menu-button');
    const profileDropdown = document.querySelector('.profile-dropdown');
    
    if (menuButton && profileDropdown) {
        menuButton.addEventListener('click', function(e) {
            e.stopPropagation();
            profileDropdown.classList.toggle('active');
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!profileDropdown.contains(e.target) && !menuButton.contains(e.target)) {
                profileDropdown.classList.remove('active');
            }
        });
        
        // Close dropdown on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                profileDropdown.classList.remove('active');
            }
        });
    }
    
    // Auth state management (placeholder - would be connected to actual auth system)
    const authOnly = document.querySelector('.auth-only');
    const guestOnly = document.querySelector('.guest-only');
    const isAuthenticated = false; // This would come from your auth system
    
    if (authOnly && guestOnly) {
        if (isAuthenticated) {
            authOnly.style.display = 'flex';
            guestOnly.style.display = 'none';
        } else {
            authOnly.style.display = 'none';
            guestOnly.style.display = 'flex';
        }
    }
    
    // Notification functionality (placeholder)
    const notificationButton = document.getElementById('notificationsButton');
    const notificationBadge = document.querySelector('.notification-badge');
    
    if (notificationButton) {
        notificationButton.addEventListener('click', function() {
            // Placeholder for notification panel toggle
            console.log('Notifications clicked');
        });
    }
    
    // Login/Signup button handlers
    const loginButton = document.querySelector('.login-button');
    const signupButton = document.querySelector('.signup-button');
    
    if (loginButton) {
        loginButton.addEventListener('click', function() {
            // Placeholder for login modal
            console.log('Login clicked');
        });
    }
    
    if (signupButton) {
        signupButton.addEventListener('click', function() {
            // Placeholder for signup modal
            console.log('Signup clicked');
        });
    }
    
     // Video modal functionality
     const trailerButtons = document.querySelectorAll('.trailer-button');
     const videoModal = document.getElementById('courseTrailer');
     const closeModal = document.querySelector('.close-modal');
     const video = videoModal.querySelector('video source');
     
     trailerButtons.forEach(button => {
         button.addEventListener('click', function() {
             const videoUrl = this.getAttribute('data-video');
             if (videoUrl) {
                 video.src = videoUrl;
                 videoModal.querySelector('video').load();
                 videoModal.style.display = 'flex';
                 document.body.style.overflow = 'hidden';
                 
                 // Fade in
                 setTimeout(() => {
                     videoModal.style.opacity = '1';
                 }, 10);
             }
         });
     });
     
     function closeVideoModal() {
         videoModal.style.opacity = '0';
         setTimeout(() => {
             videoModal.style.display = 'none';
             videoModal.querySelector('video').pause();
             document.body.style.overflow = 'auto';
         }, 300);
     }
     
     closeModal.addEventListener('click', closeVideoModal);
     
     // Close modal on outside click
     videoModal.addEventListener('click', function(e) {
         if (e.target === videoModal) {
             closeVideoModal();
         }
     });
     
     // Close modal on escape key
     document.addEventListener('keydown', function(e) {
         if (e.key === 'Escape' && videoModal.style.display === 'flex') {
             closeVideoModal();
         }
     });
     
     // Tab functionality
     const tabButtons = document.querySelectorAll('.tab-button');
     const tabContents = document.querySelectorAll('.tab-content');
     
     tabButtons.forEach(button => {
         button.addEventListener('click', function() {
             const targetTab = this.getAttribute('data-tab');
             
             // Remove active class from all buttons and contents
             tabButtons.forEach(btn => btn.classList.remove('active'));
             tabContents.forEach(content => content.classList.remove('active'));
             
             // Add active class to clicked button and corresponding content
             this.classList.add('active');
             document.getElementById(targetTab + '-tab').classList.add('active');
         });
     });
     
     // Curriculum toggle functionality
     const curriculumItems = document.querySelectorAll('.curriculum-item');
     
     curriculumItems.forEach(item => {
         const header = item.querySelector('.curriculum-header-item');
         const content = item.querySelector('.curriculum-content');
         const toggleBtn = item.querySelector('.toggle-btn');
         
         if (header && content && toggleBtn) {
             // Initially hide content
             content.style.display = 'none';
             
             header.addEventListener('click', function() {
                 const isExpanded = content.style.display === 'block';
                 
                 if (isExpanded) {
                     content.style.display = 'none';
                     toggleBtn.style.transform = 'rotate(0deg)';
                 } else {
                     content.style.display = 'block';
                     toggleBtn.style.transform = 'rotate(180deg)';
                 }
             });
         }
     });
     
     // Expand all functionality
     const expandAllBtn = document.querySelector('.expand-all-btn');
     if (expandAllBtn) {
         let allExpanded = false;
         
         expandAllBtn.addEventListener('click', function() {
             const allContents = document.querySelectorAll('.curriculum-content');
             const allToggleBtns = document.querySelectorAll('.curriculum-item .toggle-btn');
             
             allExpanded = !allExpanded;
             
             allContents.forEach(content => {
                 content.style.display = allExpanded ? 'block' : 'none';
             });
             
             allToggleBtns.forEach(btn => {
                 btn.style.transform = allExpanded ? 'rotate(180deg)' : 'rotate(0deg)';
             });
             
             this.style.transform = allExpanded ? 'rotate(180deg)' : 'rotate(0deg)';
         });
     }
    
    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    // Header scroll effect
    const header = document.querySelector('.lms-header');
    let lastScrollTop = 0;
    
    window.addEventListener('scroll', function() {
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        
        if (scrollTop > 100) {
            header.style.background = 'rgba(255, 255, 255, 0.95)';
            header.style.backdropFilter = 'blur(15px)';
        } else {
            header.style.background = 'rgba(255, 255, 255, 0.98)';
            header.style.backdropFilter = 'blur(10px)';
        }
        
        lastScrollTop = scrollTop;
    });
    
    // Intersection Observer for animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);
    
    // Observe elements for animation
    document.querySelectorAll('.module-item, .instructor-card, .sidebar-card').forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(20px)';
        el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(el);
    });
});
</script>

