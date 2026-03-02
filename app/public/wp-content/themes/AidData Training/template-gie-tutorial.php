<?php
/**
 * Template Name: Geospatial Impact Evaluation Tutorial Page
 * Template Post Type: page
 *
 * Tutorial template for Geospatial Impact Evaluation
 * Based on the course page design
 *
 * @package WordPress
 * @subpackage Twenty_Twenty_Five
 * @since Twenty Twenty-Five 1.0
 */

get_header();

// Enqueue authentication-specific styles
wp_enqueue_style('auth-styles', get_template_directory_uri() . '/assets/css/auth-styles.css', array(), '1.0.0');

// Enqueue loading screen styles
wp_enqueue_style('loading-screen', get_template_directory_uri() . '/assets/css/loading-screen.css', array(), '1.0.0');

// Enqueue LMS styles for header and footer
wp_enqueue_style('lms-styles', get_template_directory_uri() . '/assets/css/lms.css', array(), '1.0.0');

$aiddata_gie_course_id = 1691;
$aiddata_gie_destination = function_exists( 'aiddata_get_course_start_url' )
    ? aiddata_get_course_start_url( $aiddata_gie_course_id )
    : get_permalink( $aiddata_gie_course_id );

if ( ! $aiddata_gie_destination ) {
    $aiddata_gie_destination = home_url( '/' );
}

$aiddata_gie_enroll_url = add_query_arg(
    array(
        'enroll-course' => $aiddata_gie_course_id,
        'redirect_to'   => $aiddata_gie_destination,
    ),
    home_url( '/' )
);
?>

<!-- Scrollbar Styling -->
<style>
    /* Import Inter font from Google Fonts */
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
    
    /* Webkit browsers (Chrome, Safari, newer versions of Opera and Edge) */
    ::-webkit-scrollbar {
        width: 8px;
    }
    
    ::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }
    
    ::-webkit-scrollbar-thumb {
        background: #004E38;
        border-radius: 4px;
    }
    
    ::-webkit-scrollbar-thumb:hover {
        background: #004E38; /* Darker green for hover state */
    }
    
    /* Firefox */
    * {
        scrollbar-width: thin;
        scrollbar-color: #004E38 #f1f1f1;
    }

    /* Loading screen styles */
    .loading-screen {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: #f5f5f5;
        z-index: 9999;
        display: flex;
        justify-content: center;
        align-items: center;
        transition: opacity 0.5s ease;
    }

    .loading-content {
        text-align: center;
    }

    .loading-logo {
        width: 120px;
        height: auto;
        margin-bottom: 20px;
    }

    .loading-spinner {
        margin: 20px auto;
    }

    .spinner-ring {
        display: inline-block;
        width: 40px;
        height: 40px;
        border: 4px solid #f3f3f3;
        border-top: 4px solid #004E38;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .loading-text {
        color: #666;
        font-size: 14px;
        margin-top: 10px;
    }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter' !important;
        }

        body {
            font-family: 'Inter' !important;
            color: #333;
            line-height: 1.6;
            background-color: #f5f5f5;
        }

        /* Force Inter font on all text elements */
        h1, h2, h3, h4, h5, h6, p, span, div, a, button, input, textarea, select, label, li, td, th {
            font-family: 'Inter' !important;
        }

        /* Header */
        .header {
            background-color: white;
            padding: 1rem 2rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header-content {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 1.25rem;
            font-weight: 600;
            color: #004E38;
        }

        .logo-icon {
            width: 32px;
            height: 32px;
            background: linear-gradient(135deg, #4CAF50 0%, #2E7D32 50%, #004E38 100%);
            border-radius: 4px;
        }

        .header-actions {
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        .icon-btn {
            background: none;
            border: none;
            cursor: pointer;
            padding: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #666;
            transition: color 0.3s;
        }

        .icon-btn:hover {
            color: #004E38;
        }

        /* Profile Dropdown */
        .profile-dropdown {
            position: absolute;
            top: 100%;
            right: 0;
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            min-width: 200px;
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.3s ease;
        }

        .profile-dropdown.active {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .dropdown-header {
            padding: 1rem;
            border-bottom: 1px solid #f0f0f0;
        }

        .dropdown-user-info .user-name {
            font-weight: 600;
            color: #004E38;
            display: block;
        }

        .dropdown-user-info .user-email {
            font-size: 0.875rem;
            color: #666;
            display: block;
            margin-top: 0.25rem;
        }

        .dropdown-item {
            display: block;
            padding: 0.75rem 1rem;
            color: #333;
            text-decoration: none;
            border: none;
            background: none;
            width: 100%;
            text-align: left;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }

        .dropdown-item:hover {
            background-color: #f8f9fa;
        }

        .dropdown-item.logout-button {
            color: #dc3545;
            border-top: 1px solid #f0f0f0;
        }

        .dropdown-item.logout-button:hover {
            background-color: rgba(220, 53, 69, 0.05);
        }

        .header-actions {
            position: relative;
        }

        /* Hero Section */
        .hero {
            background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), 
                        url('<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/GIE_coursethumbnail.jpg');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 4rem 2rem;
        }

        .hero-content {
            max-width: 1200px;
            margin: 0 auto;
        }

        .breadcrumb {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 2rem;
            font-size: 0.875rem;
            opacity: 0.9;
        }

        .breadcrumb a {
            color: white;
            text-decoration: none;
        }

        .breadcrumb a:hover {
            text-decoration: underline;
        }

        .tutorial-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: white;
        }

        .tutorial-subtitle {
            font-size: 1.25rem;
            opacity: 0.95;
            margin-bottom: 2rem;
            max-width: 800px;
        }

        .tutorial-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-bottom: 2rem;
        }

        .tag {
            background-color: rgba(255,255,255,0.2);
            backdrop-filter: blur(10px);
            padding: 0.375rem 0.75rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .tutorial-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 2rem;
            font-size: 0.875rem;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .meta-icon {
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Main Content */
        .main-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 3rem 2rem;
            display: grid;
            grid-template-columns: 1fr 350px;
            gap: 3rem;
        }

        .content-section {
            background: white;
            border-radius: 8px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        .section-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #004E38;
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid #e0e0e0;
        }

        .section-content p {
            margin-bottom: 1rem;
            color: #555;
        }

        .section-content ul {
            list-style: none;
            padding: 0;
        }

        .section-content li {
            padding: 0.5rem 0;
            padding-left: 1.5rem;
            position: relative;
            color: #555;
        }

        .section-content li:before {
            content: "▸";
            position: absolute;
            left: 0;
            color: #004E38;
            font-weight: bold;
        }

        /* Sidebar */
        .sidebar {
            position: sticky;
            top: 100px;
            align-self: start;
        }

        .cta-card {
            background: white;
            border-radius: 8px;
            padding: 2rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.12);
            margin-bottom: 1.5rem;
        }

        .cta-card h3 {
            font-size: 1.25rem;
            margin-bottom: 1rem;
            color: #004E38;
        }

         .price {
             font-size: 3rem;
             font-weight: 700;
             color: #004E38;
             margin-bottom: 1rem;
             text-align: center;
         }

        .price-note {
            font-size: 0.875rem;
            color: #888;
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .btn {
            display: block;
            width: 100%;
            padding: 1rem;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-align: center;
            text-decoration: none;
        }

        .btn-primary {
            background-color: #004E38;
            color: white;
            margin-bottom: 0.75rem;
        }

        .btn-primary:hover {
            background-color: #164d40;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(26, 95, 79, 0.3);
        }

        .btn-secondary {
            background-color: white;
            color: #004E38;
            border: 2px solid #004E38;
            margin-bottom: 0.75rem;
        }

        .btn-secondary:hover {
            background-color: #f5f5f5;
        }

        .info-list {
            list-style: none;
            padding: 0;
        }

        .info-list li {
            padding: 0.75rem 0;
            border-bottom: 1px solid #e0e0e0;
            display: flex;
            justify-content: space-between;
            font-size: 0.875rem;
        }

        .info-list li:last-child {
            border-bottom: none;
        }

        .info-label {
            color: #666;
            font-weight: 500;
        }

        .info-value {
            color: #333;
            font-weight: 600;
        }

        .whats-included {
            margin-top: 1.5rem;
        }

        .whats-included h4 {
            margin: 0 0 1rem 0;
            font-size: 1rem;
            font-weight: 600;
            color: #333;
        }

        .included-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .included-list li {
            padding: 0.5rem 0;
            font-size: 0.875rem;
            color: #333;
            line-height: 1.4;
        }

        /* Learning Objectives Grid */
        .learning-objectives-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-top: 1.5rem;
        }

        .learning-card {
            background: #ffffff;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 1.5rem;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .learning-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .learning-icon {
            width: 48px;
            height: 48px;
            background: #004E38;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
            color: white;
        }

        .learning-card h4 {
            margin: 0 0 0.75rem 0;
            font-size: 1.1rem;
            font-weight: 600;
            color: #004E38;
            line-height: 1.3;
        }

        .learning-card p {
            margin: 0;
            font-size: 0.9rem;
            color: #555;
            line-height: 1.5;
        }

        @media (max-width: 768px) {
            .learning-objectives-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
            
            .learning-card {
                padding: 1.25rem;
            }
        }

        /* Tutorial Steps */
        .tutorial-step {
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            margin-bottom: 0.75rem;
            overflow: hidden;
        }

        .step-header {
            padding: 1rem 1.25rem;
            background-color: #f8f8f8;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: background-color 0.3s;
        }

        .step-header:hover {
            background-color: #efefef;
        }

        .step-title {
            font-weight: 600;
            color: #333;
        }

        .step-toggle {
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #004E38;
            transition: transform 0.3s;
        }

        .tutorial-step.active .step-toggle {
            transform: rotate(180deg);
        }

        .step-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
        }

        .tutorial-step.active .step-content {
            max-height: 500px;
        }

        .step-details {
            padding: 1rem 1.25rem;
        }

        .step-details p {
            margin-bottom: 0.75rem;
            color: #666;
            font-size: 0.9rem;
            line-height: 1.5;
        }

        /* Responsive */
        @media (max-width: 968px) {
            .main-content {
                grid-template-columns: 1fr;
            }

            .sidebar {
                position: static;
                order: -1;
            }

            .tutorial-title {
                font-size: 2rem;
            }
        }

        @media (max-width: 640px) {
            .hero {
                padding: 2rem 1rem;
            }

            .main-content {
                padding: 2rem 1rem;
            }

            .tutorial-title {
                font-size: 1.75rem;
            }

            .content-section {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <!-- Loading Screen -->
    <div class="loading-screen">
        <!-- Streamlined Triangles Background -->
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
            <p class="loading-text">Loading Training Hub</p>
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

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <nav class="breadcrumb">
                <a href="#">Training Hub</a>
                <span>/</span>
                <a href="#">Tutorials</a>
                <span>/</span>
                <span>Geospatial Impact Evaluation</span>
            </nav>
            
            <h1 class="tutorial-title">Geospatial Impact Evaluation</h1>
            <p class="tutorial-subtitle">Master the fundamentals of geospatial impact evaluation using satellite imagery and spatial data analysis techniques for development research.</p>
            
            <div class="tutorial-tags">
                <span class="tag">Tutorial</span>
                <span class="tag">Geospatial Data</span>
                <span class="tag">Certificate</span>
            </div>
            
            <div class="tutorial-meta">
                <div class="meta-item">
                    <div class="meta-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <polyline points="12 6 12 12 16 14"/>
                        </svg>
                    </div>
                    <span>45-60 minutes</span>
                </div>
                <div class="meta-item">
                    <div class="meta-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/>
                            <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>
                        </svg>
                    </div>
                    <span>Intermediate</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <main class="main-content">
        <div class="content-column">
            <!-- Overview -->
            <div class="content-section">
                <h2 class="section-title">Tutorial Overview</h2>
                <div class="section-content">
                    <p>Learn how to leverage geospatial data and satellite imagery for impact evaluation in development contexts. This tutorial introduces key concepts, methods, and tools for conducting rigorous spatial analysis of development interventions.</p>
                    
                    <p>Perfect for researchers, evaluators, and practitioners looking to incorporate geospatial methods into their impact assessment toolkit. No advanced GIS experience required—we'll start with the fundamentals and build from there.</p>
                </div>
            </div>

            <!-- What You'll Learn -->
            <div class="content-section">
                <h2 class="section-title">What You'll Learn</h2>
                <div class="section-content">
                    <div class="learning-objectives-grid">
                        <div class="learning-card">
                            <div class="learning-icon">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                                    <circle cx="12" cy="10" r="3"/>
                                </svg>
                            </div>
                            <h4>Spatial Data Fundamentals</h4>
                            <p>Understand the basics of geospatial data types, coordinate systems, and how to work with satellite imagery for development research.</p>
                        </div>
                        
                        <div class="learning-card">
                            <div class="learning-icon">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                                    <line x1="3" y1="9" x2="21" y2="9"/>
                                    <line x1="9" y1="21" x2="9" y2="9"/>
                                </svg>
                            </div>
                            <h4>Impact Evaluation Methods</h4>
                            <p>Learn spatial difference-in-differences, matching techniques, and other causal inference approaches adapted for geospatial analysis.</p>
                        </div>
                        
                        <div class="learning-card">
                            <div class="learning-icon">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
                                </svg>
                            </div>
                            <h4>Satellite Imagery Analysis</h4>
                            <p>Discover how to access, process, and analyze satellite imagery to measure changes in land use, infrastructure, and other development outcomes.</p>
                        </div>
                        
                        <div class="learning-card">
                            <div class="learning-icon">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>
                                </svg>
                            </div>
                            <h4>Practical Applications</h4>
                            <p>Apply these methods to real-world scenarios including infrastructure projects, agricultural programs, and environmental interventions.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tutorial Steps -->
            <div class="content-section">
                <h2 class="section-title">Tutorial Steps</h2>
                <div class="section-content">
                    <div class="tutorial-step">
                        <div class="step-header">
                            <span class="step-title">Introduction to Geospatial Impact Evaluation</span>
                            <div class="step-toggle">▼</div>
                        </div>
                        <div class="step-content">
                            <div class="step-details">
                                <p>Explore the fundamentals of geospatial impact evaluation, including when and why to use spatial methods, key terminology, and the types of research questions that benefit from geospatial approaches.</p>
                            </div>
                        </div>
                    </div>

                    <div class="tutorial-step">
                        <div class="step-header">
                            <span class="step-title">Understanding Spatial Data Sources</span>
                            <div class="step-toggle">▼</div>
                        </div>
                        <div class="step-content">
                            <div class="step-details">
                                <p>Learn about different types of spatial data including satellite imagery, vector data, and administrative boundaries. Discover where to find free and open-source geospatial datasets for development research.</p>
                            </div>
                        </div>
                    </div>

                    <div class="tutorial-step">
                        <div class="step-header">
                            <span class="step-title">Spatial Analysis Techniques</span>
                            <div class="step-toggle">▼</div>
                        </div>
                        <div class="step-content">
                            <div class="step-details">
                                <p>Master key spatial analysis techniques including buffer analysis, spatial joins, overlay operations, and proximity analysis. Learn how to prepare and process spatial data for impact evaluation.</p>
                            </div>
                        </div>
                    </div>

                    <div class="tutorial-step">
                        <div class="step-header">
                            <span class="step-title">Case Study: Evaluating Infrastructure Impact</span>
                            <div class="step-toggle">▼</div>
                        </div>
                        <div class="step-content">
                            <div class="step-details">
                                <p>Work through a practical case study evaluating the impact of road construction on economic development using satellite imagery and spatial analysis techniques covered in previous modules.</p>
                            </div>
                        </div>
                    </div>

                    <div class="tutorial-step">
                        <div class="step-header">
                            <span class="step-title">Best Practices and Next Steps</span>
                            <div class="step-toggle">▼</div>
                        </div>
                        <div class="step-content">
                            <div class="step-details">
                                <p>Review best practices for conducting and reporting geospatial impact evaluations, common pitfalls to avoid, and resources for continuing your learning journey in spatial analysis.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="cta-card">
                <div class="price">Free</div>
                <p class="price-note">Open Access</p>
               
                <a href="<?php echo esc_url( $aiddata_gie_enroll_url ); ?>" class="btn btn-primary">Start Tutorial</a>
                <button class="btn btn-secondary">Watch Preview</button>
                
                <div class="whats-included">
                    <h4>What's Included:</h4>
                    <ul class="included-list">
                        <li>Step-by-step video guides ✓</li>
                        <li>Interactive practice exercises ✓</li>
                        <li>Downloadable datasets ✓</li>
                        <li>Quiz and Certificate of Completion* ✓</li>
                    </ul>
                    <p style="font-size: 0.75rem; color: #666; margin-top: 0.5rem; font-style: italic;">*80%+ passing grade required</p>
                </div>
            </div>

            <div class="cta-card">
                <div style="margin-bottom: 1rem;">
                    <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/seeing_development_from_above_interview.png" 
                         alt="AidData researcher interview" 
                         style="width: 100%; height: auto; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                </div>
                <p style="font-size: 0.875rem; color: #555; line-height: 1.6; margin-bottom: 0.5rem;">
                    This tutorial includes footage from the Seeing Development from Above interview with Jacob Hall and Kunwar Singh.<br><br>Watch the interview <a href="https://www.aiddata.org/publications" target="_blank" style="color: #004E38; text-decoration: underline; font-weight: 500;">here</a>.
                </p>
            </div>
        </aside>
    </main>

    <!-- Video Modal -->
    <div id="videoModal" class="video-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.9); z-index: 10000; align-items: center; justify-content: center;">
        <div class="video-modal-overlay" style="position: relative; width: 90%; max-width: 900px; background: #000; border-radius: 8px; overflow: hidden; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);">
            <div class="video-modal-content" style="position: relative; width: 100%;">
                <button class="video-modal-close" style="position: absolute; top: 10px; right: 15px; background: rgba(0, 0, 0, 0.7); color: white; border: none; font-size: 32px; cursor: pointer; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; z-index: 10001; transition: background-color 0.3s;">&times;</button>
                <div style="position: relative; width: 100%; height: 0; padding-bottom: 56.25%;">
                    <iframe id="trailerIframe" src="about:blank" style="border: 1px solid #464646; position: absolute; top: 0; left: 0; width: 100%; height: 100%; box-sizing: border-box;" allowfullscreen allow="autoplay" aria-label="Panopto Embedded Video Player"></iframe>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Header menu functionality
        const menuButton = document.querySelector('.menu-button');
        const profileDropdown = document.querySelector('.profile-dropdown');
        const notificationsButton = document.getElementById('notificationsButton');

        // Toggle profile dropdown
        if (menuButton && profileDropdown) {
            menuButton.addEventListener('click', function(e) {
                e.stopPropagation();
                profileDropdown.classList.toggle('active');
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!menuButton.contains(e.target) && !profileDropdown.contains(e.target)) {
                    profileDropdown.classList.remove('active');
                }
            });

            // Close dropdown when pressing Escape
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    profileDropdown.classList.remove('active');
                }
            });
        }

        // Logout functionality
        const logoutButton = document.querySelector('.logout-button');
        if (logoutButton) {
            logoutButton.addEventListener('click', function() {
                if (confirm('Are you sure you want to sign out?')) {
                    // Add logout logic here
                    console.log('User logged out');
                    // For now, just redirect to home page
                    window.location.href = '/';
                }
            });
        }

        // Login/Signup button functionality
        const loginButton = document.querySelector('.login-button');
        const signupButton = document.querySelector('.signup-button');

        if (loginButton) {
            loginButton.addEventListener('click', function() {
                console.log('Login clicked');
                // Add login modal or redirect logic here
            });
        }

        if (signupButton) {
            signupButton.addEventListener('click', function() {
                console.log('Signup clicked');
                // Add signup modal or redirect logic here
            });
        }

        // Tutorial step accordion functionality
        const stepHeaders = document.querySelectorAll('.step-header');
        
        stepHeaders.forEach(header => {
            header.addEventListener('click', function() {
                const step = this.parentElement;
                const isActive = step.classList.contains('active');
                
                // Close all steps
                document.querySelectorAll('.tutorial-step').forEach(s => {
                    s.classList.remove('active');
                });
                
                // Open clicked step if it wasn't active
                if (!isActive) {
                    step.classList.add('active');
                }
            });
        });

        // Smooth scroll for breadcrumb links
        document.querySelectorAll('.breadcrumb a').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                console.log('Navigate to:', this.textContent);
            });
        });

        // CTA button handlers
        const watchPreviewBtn = document.querySelector('.btn-secondary');
        if (watchPreviewBtn && watchPreviewBtn.textContent.includes('Preview')) {
            watchPreviewBtn.addEventListener('click', function() {
                console.log('Watch Preview clicked');
                openVideoModal();
            });
        }

        // Video Modal Functions
        function openVideoModal() {
            const modal = document.getElementById('videoModal');
            const iframe = document.getElementById('trailerIframe');
            const videoUrl = '<?php echo esc_url(get_template_directory_uri()); ?>/assets/videos/tut1.mp4';

            modal.style.display = 'flex';
            iframe.src = videoUrl;
            document.body.style.overflow = 'hidden';
        }

        function closeVideoModal() {
            const modal = document.getElementById('videoModal');
            const iframe = document.getElementById('trailerIframe');

            modal.style.display = 'none';
            iframe.src = 'about:blank';
            document.body.style.overflow = '';
        }

        // Close modal when clicking close button
        document.querySelector('.video-modal-close').addEventListener('click', closeVideoModal);

        // Close modal when clicking outside the video
        document.getElementById('videoModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeVideoModal();
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeVideoModal();
            }
        });
    </script>

    <!-- Loading Screen Control Script -->
    <script>
        // Hide loading screen when page is fully loaded
        window.addEventListener('load', function() {
            const loadingScreen = document.querySelector('.loading-screen');
            if (loadingScreen) {
                loadingScreen.style.opacity = '0';
                setTimeout(function() {
                    loadingScreen.style.display = 'none';
                }, 500);
            }
        });

        // Also hide after a maximum of 3 seconds as fallback
        setTimeout(function() {
            const loadingScreen = document.querySelector('.loading-screen');
            if (loadingScreen) {
                loadingScreen.style.opacity = '0';
                setTimeout(function() {
                    loadingScreen.style.display = 'none';
                }, 500);
            }
        }, 3000);
    </script>


<?php
get_footer();
?>

<!-- Custom Video Player Scripts and Styles -->
<link rel="stylesheet" href="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/css/video-player.css">
<script src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/js/video-player.js"></script>
</body>
</html>


