<?php
/**
 * Template Name: AidData Course Page Replica
 * Template Post Type: page
 *
 * Exact replica of the aiddata-course-page (1).html design
 * No changes to the original design - replicated exactly as provided
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
        background: #026447;
        border-radius: 4px;
    }
    
    ::-webkit-scrollbar-thumb:hover {
        background: #004E38; /* Darker green for hover state */
    }
    
    /* Firefox */
    * {
        scrollbar-width: thin;
        scrollbar-color: #026447 #f1f1f1;
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
        border-top: 4px solid #026447;
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
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif !important;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif !important;
            color: #333;
            line-height: 1.6;
            background-color: #f5f5f5;
        }

        /* Force Inter font on all text elements */
        h1, h2, h3, h4, h5, h6, p, span, div, a, button, input, textarea, select, label, li, td, th {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif !important;
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

        /* Header Dropdown Menu Styles */
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

        .profile-dropdown.show {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .header-icons {
            position: relative;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .dropdown-header {
            padding: 1rem;
            border-bottom: 1px solid #e0e0e0;
        }

        .dropdown-user-info {
            display: flex;
            flex-direction: column;
        }

        .user-name {
            font-weight: 600;
            color: #333;
            margin-bottom: 0.25rem;
        }

        .user-email {
            font-size: 0.875rem;
            color: #666;
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
            transition: background-color 0.2s;
            font-family: inherit;
            font-size: inherit;
        }

        .dropdown-item:hover {
            background-color: #f5f5f5;
        }

        .header-button {
            background: none;
            border: none;
            cursor: pointer;
            padding: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #666;
            transition: color 0.3s;
            border-radius: 4px;
        }

        .header-button:hover {
            color: #004E38;
            background-color: #f5f5f5;
        }

        /* Hero Section */
        .hero {
            background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), 
                        url('<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/global_finance.png');
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

        .course-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: white;
        }

        .course-subtitle {
            font-size: 1.25rem;
            opacity: 0.95;
            margin-bottom: 2rem;
            max-width: 800px;
        }

        .course-tags {
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

        .course-meta {
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

        /* Curriculum */
        .curriculum-item {
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            margin-bottom: 0.75rem;
            overflow: hidden;
        }

        .curriculum-header {
            padding: 1rem 1.25rem;
            background-color: #f8f8f8;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: background-color 0.3s;
        }

        .curriculum-header:hover {
            background-color: #efefef;
        }

        .curriculum-title {
            font-weight: 600;
            color: #333;
        }

        .curriculum-toggle {
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #004E38;
            transition: transform 0.3s;
        }

        .curriculum-item.active .curriculum-toggle {
            transform: rotate(180deg);
        }

        .curriculum-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
        }

        .curriculum-item.active .curriculum-content {
            max-height: 500px;
        }

        .lesson-list {
            padding: 1rem 1.25rem;
            list-style: none;
        }

        .lesson-list li {
            padding: 0.5rem 0;
            color: #666;
            font-size: 0.9rem;
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

            .course-title {
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

            .course-title {
                font-size: 1.75rem;
            }

            .content-section {
                padding: 1.5rem;
            }
        }

        /* Video Modal Styles */
        .video-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 10000;
            background-color: rgba(0, 0, 0, 0.8);
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .video-modal-overlay {
            position: relative;
            width: 90%;
            max-width: 900px;
            background: #000;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
        }

        .video-modal-content {
            position: relative;
            width: 100%;
        }

        .video-modal-close {
            position: absolute;
            top: 10px;
            right: 15px;
            background: rgba(0, 0, 0, 0.7);
            color: white;
            border: none;
            font-size: 24px;
            cursor: pointer;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10001;
            transition: background-color 0.3s;
        }

        .video-modal-close:hover {
            background: rgba(0, 0, 0, 0.9);
        }

        .video-modal video {
            width: 100%;
            height: auto;
            display: block;
        }

        @media (max-width: 768px) {
            .video-modal-overlay {
                width: 95%;
                margin: 20px;
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
                <a href="#">Courses</a>
                <span>/</span>
                <span>Navigating Global Development Finance</span>
            </nav>
            
             <h1 class="course-title">Navigating Global Development Finance</h1>
             <p class="course-subtitle">Transform data into compelling stories that matter</p>
            
            <div class="course-tags">
                <span class="tag">Course</span>
                <span class="tag">Digital Badge</span>
                <span class="tag">Self-paced</span>
            </div>
            
             <div class="course-meta">
                 <div class="meta-item">
                     <div class="meta-icon">
                         <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                             <circle cx="12" cy="12" r="10"/>
                             <polyline points="12 6 12 12 16 14"/>
                         </svg>
                     </div>
                     <span>6-8 hours/week over 4 weeks</span>
                 </div>
                 <div class="meta-item">
                     <div class="meta-icon">
                         <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                             <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/>
                             <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>
                         </svg>
                     </div>
                     <span>Introductory</span>
                 </div>
             </div>
        </div>
    </section>

     <!-- Main Content -->
     <main class="main-content">
         <div class="content-column">
             <!-- Partnership Section -->
             <div class="content-section" style="padding: 1rem 2rem; margin-bottom: 1rem;">
                <div style="display: flex; align-items: center; justify-content: center; gap: 1rem; font-size: 0.9rem; color: #666;">
                <span>Delivered in partnership with the W&M Studio for Teaching and Learning Innovation</span>
                     <a href="https://stli.wm.edu/" target="_blank" rel="noopener noreferrer">
                         <img src="https://stli.wm.edu/files/2023/11/STLI-Logo_Honeycomb_full-copy.png" alt="STLI Logo" style="height: 40px; width: auto;">
                     </a>
                     
                </div>
             </div>
             
             <!-- Overview -->
            <div class="content-section">
                <h2 class="section-title">Course Overview</h2>
                <div class="section-content">
                    <p>You'll learn threshold concepts of foundational data analysis, data journalism narratives, critical data visualization techniques, and development finance models and dynamics.</p>
                </div>
            </div>

            <!-- What You'll Learn -->
            <div class="content-section">
                <h2 class="section-title">What You'll Learn</h2>
                <div class="section-content">
                    <div class="learning-objectives-grid">
                        <div class="learning-card">
                            <h4>Data Analysis Mastery</h4>
                            <p>Develop essential skills in analyzing and interpreting complex development finance datasets, with hands-on practice using real-world examples.</p>
                        </div>
                        
                        <div class="learning-card">
                            <h4>Storytelling with Data</h4>
                            <p>Learn to craft compelling narratives that make complex financial information accessible and impactful for diverse audiences.</p>
                        </div>
                        
                        <div class="learning-card">
                            <h4>Data Visualization Techniques</h4>
                            <p>Master the art of creating clear, effective visualizations that communicate key insights from development finance data.</p>
                        </div>
                        
                        <div class="learning-card">
                            <h4>Historical Context</h4>
                            <p>Understand the evolution of development finance from post-WWII to today, including the emergence of new providers like China.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Curriculum -->
            <div class="content-section">
                <h2 class="section-title">Course Curriculum</h2>
                <div class="section-content">
                    <div class="curriculum-item">
                        <div class="curriculum-header">
                            <span class="curriculum-title">Module 1: Data Foundations</span>
                            <div class="curriculum-toggle">▼</div>
                        </div>
                        <div class="curriculum-content">
                            <div class="lesson-list">
                                <p style="padding: 0; margin-bottom: 0.75rem;">This module delves into the intricate world of data as it pertains to development finance. It covers the foundational aspects of data understanding and sourcing. You'll be equipped with the essential skills needed to navigate the complex landscape of development finance data and transform raw information into insightful intelligence for public consumption.</p>
                            </div>
                        </div>
                    </div>

                    <div class="curriculum-item">
                        <div class="curriculum-header">
                            <span class="curriculum-title">Module 2: Data Journalism</span>
                            <div class="curriculum-toggle">▼</div>
                        </div>
                        <div class="curriculum-content">
                            <div class="lesson-list">
                                <p style="padding: 0; margin-bottom: 0.75rem;">This module offers an overview of data journalism, surveying its evolution from traditional number-crunching to a narrative tool that fuels transparency, accountability, and civic engagement. By examining its historical context, the role and relevance in today's media landscape, and its potent combination of statistical acumen with storytelling through dataviz, you'll will emerge with a clearer understanding of data journalism's capacity to inform and influence public discourse on a range of policy issues.</p>
                            </div>
                        </div>
                    </div>

                    <div class="curriculum-item">
                        <div class="curriculum-header">
                            <span class="curriculum-title">Module 3: Critical Data Analysis and Visualization</span>
                            <div class="curriculum-toggle">▼</div>
                        </div>
                        <div class="curriculum-content">
                            <div class="lesson-list">
                                <p style="padding: 0; margin-bottom: 0.75rem;">This module provides a comprehensive introduction to data visualization, covering both essential principles and practical applications. By exploring its historical background, contemporary relevance, and integration of statistical analysis with visual storytelling, you'll gain a deeper appreciation of how data visualization transforms complex data into accessible and engaging visuals.</p>
                            </div>
                        </div>
                    </div>

                    <div class="curriculum-item">
                        <div class="curriculum-header">
                            <span class="curriculum-title">Module 4: Development Finance Models and Credit Provision</span>
                            <div class="curriculum-toggle">▼</div>
                        </div>
                        <div class="curriculum-content">
                            <div class="lesson-list">
                                <p style="padding: 0; margin-bottom: 0.75rem;">This module offers an overview of the global development finance industry, surveying its evolution from emergence after World War II to developments over the past seven decades. It examines how traditional and emerging foreign development assistance providers organize their efforts, the factors that have shaped their strategies, and the use of global development finance as an instrument of statecraft. You'll emerge with a clearer understanding of global development finance's role in advancing the agendas of donor countries while supporting economic development in host countries.</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="curriculum-item">
                        <div class="curriculum-header">
                            <span class="curriculum-title">Professional Capstone Project</span>
                            <div class="curriculum-toggle">▼</div>
                        </div>
                        <div class="curriculum-content">
                            <div class="lesson-list">
                                <p style="padding: 0; margin-bottom: 0.75rem;">Apply all the skills and knowledge you've gained throughout the course in a comprehensive capstone project. Working with AidData datasets and tools, you'll analyze funding patterns, create visually appealing data visualizations, and craft a compelling narrative about a significant aspect of global development finance. The project concludes with a reflection on your methodology and learning journey, allowing you to synthesize your experience and articulate your professional growth.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


         </div>

        <!-- Sidebar -->
        <aside class="sidebar">
             <div class="cta-card">
                 <div class="price">$1525</div>
                 <p class="price-note"><a href="https://forms-six-mu.vercel.app/Navigating%20Global%20Development%20Finance/scholarship_form.html" target="_blank" rel="noopener noreferrer" style="color: #004E38; text-decoration: underline; font-weight: bold;">Scholarships Available</a></p>
                
                <a href="https://academy.wm.edu/product?catalog=NavigatingGlobalDevelopmentFinance_AID" target="_blank" rel="noopener noreferrer" class="btn btn-primary" style="text-decoration: none;">Enrol</a>
                <button class="btn btn-secondary">Watch Trailer</button>
                
                <div class="whats-included">
                    <h4>What's Included:</h4>
                    <ul class="included-list">
                        <li>Full course access for 12 months ✓</li>
                        <li>Complete the course at your own pace ✓</li>
                        <li>Instructor feedback ✓</li>
                        <li>Capstone project ✓</li>
                        <li><a href="https://www.credly.com/org/stli/badge/navigating-global-development-finance" target="_blank" rel="noopener noreferrer" style="color: #004E38; text-decoration: none;">Digital badge</a> upon completion, as a shareable credential for your profile ✓</li>
                    </ul>
                </div>
            </div>

             <div class="cta-card">
                 <h2 class="section-title">Professional Development</h2>
                 <div class="section-content">
                     <p>Interested in your team completing the course in multimodal form? Get in touch with the AidData Training team <a href="https://traininginquiryform.vercel.app/" target="_blank" rel="noopener noreferrer" style="color: #004E38; text-decoration: underline;">here</a>.</p>
                 </div>
             </div>
         </aside>
     </main>

    <!-- Video Modal -->
    <div id="videoModal" class="video-modal" style="display: none;">
        <div class="video-modal-overlay">
            <div class="video-modal-content">
                <button class="video-modal-close">&times;</button>
                <div style="position: relative; width: 100%; height: 0; padding-bottom: 56.25%;">
                    <iframe id="trailerIframe" src="https://wmedu.hosted.panopto.com/Panopto/Pages/Embed.aspx?id=db8251fc-f910-4fc0-a5d5-b379004d81da&autoplay=false&offerviewer=false&showtitle=false&showbrand=false&captions=false&interactivity=none" style="border: 1px solid #464646; position: absolute; top: 0; left: 0; width: 100%; height: 100%; box-sizing: border-box;" allowfullscreen allow="autoplay" aria-label="Panopto Embedded Video Player" aria-description="Introduction to AidData's GCDF Dataset"></iframe>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Header Menu Functionality
        document.addEventListener('DOMContentLoaded', function() {
            const menuButton = document.querySelector('.menu-button');
            const profileDropdown = document.querySelector('.profile-dropdown');
            
            if (menuButton && profileDropdown) {
                menuButton.addEventListener('click', function(e) {
                    e.stopPropagation();
                    profileDropdown.classList.toggle('show');
                });

                // Close dropdown when clicking outside
                document.addEventListener('click', function(e) {
                    if (!menuButton.contains(e.target) && !profileDropdown.contains(e.target)) {
                        profileDropdown.classList.remove('show');
                    }
                });

                // Close dropdown when pressing Escape
                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape') {
                        profileDropdown.classList.remove('show');
                    }
                });
            }

            // Login/Signup button functionality
            const loginButton = document.querySelector('.login-button');
            const signupButton = document.querySelector('.signup-button');
            
            if (loginButton) {
                loginButton.addEventListener('click', function() {
                    console.log('Login clicked');
                    // Add login functionality here
                    alert('Login functionality would be implemented here');
                });
            }
            
            if (signupButton) {
                signupButton.addEventListener('click', function() {
                    console.log('Signup clicked');
                    // Add signup functionality here
                    alert('Signup functionality would be implemented here');
                });
            }

            // Logout button functionality
            const logoutButton = document.querySelector('.logout-button');
            if (logoutButton) {
                logoutButton.addEventListener('click', function() {
                    console.log('Logout clicked');
                    // Add logout functionality here
                    alert('Logout functionality would be implemented here');
                });
            }
        });

        // Curriculum accordion functionality
        const curriculumHeaders = document.querySelectorAll('.curriculum-header');
        
        curriculumHeaders.forEach(header => {
            header.addEventListener('click', function() {
                const item = this.parentElement;
                const isActive = item.classList.contains('active');
                
                // Close all items
                document.querySelectorAll('.curriculum-item').forEach(i => {
                    i.classList.remove('active');
                });
                
                // Open clicked item if it wasn't active
                if (!isActive) {
                    item.classList.add('active');
                }
            });
        });

        // Copy link functionality
        const shareBtn = document.querySelector('.cta-card:last-child .btn-secondary');
        if (shareBtn) {
            shareBtn.addEventListener('click', function() {
                navigator.clipboard.writeText(window.location.href).then(() => {
                    const originalText = this.textContent;
                    this.textContent = 'Link Copied!';
                    setTimeout(() => {
                        this.textContent = originalText;
                    }, 2000);
                });
            });
        }

        // Smooth scroll for breadcrumb links
        document.querySelectorAll('.breadcrumb a').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                // Add navigation logic here
                console.log('Navigate to:', this.textContent);
            });
        });

        // CTA button handlers
        document.querySelector('.btn-primary').addEventListener('click', function() {
            console.log('Start Learning clicked');
            // Add enrollment logic here
            alert('Starting course enrollment...');
        });

        const watchTrailerBtn = document.querySelector('.btn-secondary');
        if (watchTrailerBtn && watchTrailerBtn.textContent.includes('Trailer')) {
            watchTrailerBtn.addEventListener('click', function() {
                console.log('Watch Trailer clicked');
                openVideoModal();
            });
        }

        // Video Modal Functions
        function openVideoModal() {
            const modal = document.getElementById('videoModal');
            const iframe = document.getElementById('trailerIframe');

            modal.style.display = 'flex';
            // Reload iframe to restart video
            const currentSrc = iframe.src;
            iframe.src = currentSrc;

            // Prevent body scroll when modal is open
            document.body.style.overflow = 'hidden';
        }

        function closeVideoModal() {
            const modal = document.getElementById('videoModal');
            const iframe = document.getElementById('trailerIframe');

            modal.style.display = 'none';
            // Stop iframe video by clearing and resetting src
            const currentSrc = iframe.src;
            iframe.src = 'about:blank';
            setTimeout(() => { iframe.src = currentSrc; }, 100);

            // Restore body scroll
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

<footer class="site-footer">
    <div class="footer-content">
        <div class="footer-section">
            <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/logo.png" alt="AidData Logo" class="footer-logo">
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

<!-- Custom Video Player Scripts and Styles -->
<link rel="stylesheet" href="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/css/video-player.css">
<script src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/js/video-player.js"></script>
</body>
</html>

