<?php
/**
 * Template Name: AidData Simulation Page Replica
 * Template Post Type: page
 *
 * Simulation template based on the course page design
 * Modified for interactive simulation-specific content and functionality
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

        /* Hero Section */
        .hero {
            background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), 
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

        .simulation-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: white;
        }

        .simulation-subtitle {
            font-size: 1.25rem;
            opacity: 0.95;
            margin-bottom: 2rem;
            max-width: 800px;
        }

        .simulation-tags {
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

        .simulation-meta {
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

        .btn-simulation {
            background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%);
            color: white;
            margin-bottom: 0.75rem;
            position: relative;
            overflow: hidden;
        }

        .btn-simulation:hover {
            background: linear-gradient(135deg, #e55a2b 0%, #e8841a 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 107, 53, 0.4);
        }

        .btn-simulation::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }

        .btn-simulation:hover::before {
            left: 100%;
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
            background: #f8f9fa;
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

        /* Simulation Scenarios */
        .scenario-item {
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            margin-bottom: 0.75rem;
            overflow: hidden;
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
        }

        .scenario-header {
            padding: 1rem 1.25rem;
            background: linear-gradient(135deg, #004E38 0%, #26704f 100%);
            color: white;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.3s;
        }

        .scenario-header:hover {
            background: linear-gradient(135deg, #164d40 0%, #1f5a42 100%);
        }

        .scenario-title {
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .scenario-number {
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            width: 28px;
            height: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.875rem;
            font-weight: 700;
        }

        .scenario-toggle {
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            transition: transform 0.3s;
        }

        .scenario-item.active .scenario-toggle {
            transform: rotate(180deg);
        }

        .scenario-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
        }

        .scenario-item.active .scenario-content {
            max-height: 600px;
        }

        .scenario-details {
            padding: 1.5rem 1.25rem;
        }

        .scenario-details p {
            margin-bottom: 1rem;
            color: #555;
            font-size: 0.9rem;
            line-height: 1.6;
        }

        .scenario-objectives {
            background: #f0f8ff;
            border-left: 4px solid #004E38;
            padding: 1rem;
            margin: 1rem 0;
            border-radius: 0 6px 6px 0;
        }

        .scenario-objectives h5 {
            margin: 0 0 0.5rem 0;
            color: #004E38;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .scenario-objectives ul {
            margin: 0;
            padding-left: 1rem;
        }

        .scenario-objectives li {
            color: #555;
            font-size: 0.85rem;
            line-height: 1.4;
            margin-bottom: 0.25rem;
        }

        .difficulty-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .difficulty-beginner {
            background: #d4edda;
            color: #155724;
        }

        .difficulty-intermediate {
            background: #fff3cd;
            color: #856404;
        }

        .difficulty-advanced {
            background: #f8d7da;
            color: #721c24;
        }

        /* Responsive */
        @media (max-width: 968px) {
            .main-content {
                grid-template-columns: 1fr;
            }

            .sidebar {
                position: static;
            }

            .simulation-title {
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

            .simulation-title {
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
                <a href="#">Simulations</a>
                <span>/</span>
                <span>Development Finance Policy Simulator</span>
            </nav>
            
            <h1 class="simulation-title">Development Finance Policy Simulator</h1>
            <p class="simulation-subtitle">Experience real-world development finance decision-making through interactive scenarios based on actual policy challenges and outcomes.</p>
            
            <div class="simulation-tags">
                <span class="tag">Simulation</span>
                <span class="tag">Interactive</span>
                <span class="tag">Real-World</span>
            </div>
            
            <div class="simulation-meta">
                <div class="meta-item">
                    <div class="meta-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <polyline points="12 6 12 12 16 14"/>
                        </svg>
                    </div>
                    <span>60-90 minutes</span>
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
                <div class="meta-item">
                    <div class="meta-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>
                    </div>
                    <span>Multiplayer</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <main class="main-content">
        <div class="content-column">
            <!-- Partnership Section -->
            <div class="content-section" style="padding: 1rem 2rem; margin-bottom: 1rem;">
                <div style="display: flex; align-items: center; gap: 1rem; font-size: 0.9rem; color: #666;">
                    <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/wm_full_horizontal.svg" alt="William & Mary Logo" style="height: 30px; width: auto;">
                    <span>Delivered in partnership with the W&M Studio for Teaching and Learning Innovation</span>
                </div>
            </div>
            
            <!-- Overview -->
            <div class="content-section">
                <h2 class="section-title">Simulation Overview</h2>
                <div class="section-content">
                    <p>Step into the shoes of policy makers, development finance institutions, and international organizations as you navigate complex decisions that shape global development outcomes. This immersive simulation uses real AidData research and historical cases to create authentic scenarios.</p>
                    
                    <p>Make strategic decisions, manage budgets, negotiate with stakeholders, and see the real-world impact of your choices through dynamic feedback and data visualization.</p>
                </div>
            </div>

            <!-- What You'll Experience -->
            <div class="content-section">
                <h2 class="section-title">What You'll Experience</h2>
                <div class="section-content">
                    <div class="learning-objectives-grid">
                        <div class="learning-card">
                            <div class="learning-icon">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                </svg>
                            </div>
                            <h4>Decision-Making Under Pressure</h4>
                            <p>Experience time-sensitive policy decisions with limited information, competing priorities, and real-world constraints.</p>
                        </div>
                        
                        <div class="learning-card">
                            <div class="learning-icon">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                    <circle cx="9" cy="7" r="4"/>
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                </svg>
                            </div>
                            <h4>Stakeholder Negotiations</h4>
                            <p>Navigate complex relationships between donors, recipients, civil society, and private sector actors in realistic scenarios.</p>
                        </div>
                        
                        <div class="learning-card">
                            <div class="learning-icon">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="3"/>
                                    <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/>
                                </svg>
                            </div>
                            <h4>Resource Management</h4>
                            <p>Balance competing demands for limited resources while maximizing development impact and political feasibility.</p>
                        </div>
                        
                        <div class="learning-card">
                            <div class="learning-icon">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="18" y1="20" x2="18" y2="10"/>
                                    <line x1="12" y1="20" x2="12" y2="4"/>
                                    <line x1="6" y1="20" x2="6" y2="14"/>
                                </svg>
                            </div>
                            <h4>Impact Assessment</h4>
                            <p>See the long-term consequences of your decisions through data-driven feedback and outcome visualization.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Simulation Scenarios -->
            <div class="content-section">
                <h2 class="section-title">Simulation Scenarios</h2>
                <div class="section-content">
                    <div class="scenario-item">
                        <div class="scenario-header">
                            <div class="scenario-title">
                                <div class="scenario-number">1</div>
                                <span>Crisis Response: Natural Disaster Recovery</span>
                            </div>
                            <div class="difficulty-badge difficulty-beginner">Beginner</div>
                            <div class="scenario-toggle">▼</div>
                        </div>
                        <div class="scenario-content">
                            <div class="scenario-details">
                                <p>A major earthquake has devastated a developing nation. As the head of an international development agency, you must coordinate emergency response, allocate resources, and plan long-term reconstruction efforts while managing political pressures and competing stakeholder interests.</p>
                                
                                <div class="scenario-objectives">
                                    <h5>Key Learning Objectives:</h5>
                                    <ul>
                                        <li>Emergency response coordination and prioritization</li>
                                        <li>Multi-stakeholder collaboration under pressure</li>
                                        <li>Balancing immediate needs with long-term development goals</li>
                                        <li>Resource allocation and budget management</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="scenario-item">
                        <div class="scenario-header">
                            <div class="scenario-title">
                                <div class="scenario-number">2</div>
                                <span>Infrastructure Investment: Belt and Road Initiative</span>
                            </div>
                            <div class="difficulty-badge difficulty-intermediate">Intermediate</div>
                            <div class="scenario-toggle">▼</div>
                        </div>
                        <div class="scenario-content">
                            <div class="scenario-details">
                                <p>Navigate the complex geopolitics of large-scale infrastructure investment. Play as both Chinese development finance institutions and recipient country governments to understand different perspectives on debt sustainability, economic development, and strategic partnerships.</p>
                                
                                <div class="scenario-objectives">
                                    <h5>Key Learning Objectives:</h5>
                                    <ul>
                                        <li>Debt sustainability analysis and risk assessment</li>
                                        <li>Geopolitical considerations in development finance</li>
                                        <li>Infrastructure project evaluation and selection</li>
                                        <li>Negotiating terms and conditions for large investments</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="scenario-item">
                        <div class="scenario-header">
                            <div class="scenario-title">
                                <div class="scenario-number">3</div>
                                <span>Climate Finance: Green Transition Strategy</span>
                            </div>
                            <div class="difficulty-badge difficulty-advanced">Advanced</div>
                            <div class="scenario-toggle">▼</div>
                        </div>
                        <div class="scenario-content">
                            <div class="scenario-details">
                                <p>Design and implement a comprehensive climate finance strategy for a middle-income country seeking to transition to renewable energy while maintaining economic growth. Balance environmental goals, social equity, and economic competitiveness in a complex multi-year simulation.</p>
                                
                                <div class="scenario-objectives">
                                    <h5>Key Learning Objectives:</h5>
                                    <ul>
                                        <li>Climate finance mechanisms and innovative instruments</li>
                                        <li>Just transition planning and social impact assessment</li>
                                        <li>Private sector engagement and blended finance</li>
                                        <li>Long-term strategic planning and adaptive management</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="scenario-item">
                        <div class="scenario-header">
                            <div class="scenario-title">
                                <div class="scenario-number">4</div>
                                <span>Pandemic Response: Global Health Security</span>
                            </div>
                            <div class="difficulty-badge difficulty-intermediate">Intermediate</div>
                            <div class="scenario-toggle">▼</div>
                        </div>
                        <div class="scenario-content">
                            <div class="scenario-details">
                                <p>Coordinate international response to a global health emergency. Manage vaccine distribution, healthcare system strengthening, and economic recovery while addressing equity concerns and building resilient health systems for the future.</p>
                                
                                <div class="scenario-objectives">
                                    <h5>Key Learning Objectives:</h5>
                                    <ul>
                                        <li>Global health governance and coordination mechanisms</li>
                                        <li>Equity considerations in emergency response</li>
                                        <li>Health system strengthening and resilience building</li>
                                        <li>Economic recovery and social protection integration</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="cta-card">
                <div class="price">$75</div>
                <p class="price-note">Premium Experience</p>
               
                <button class="btn btn-simulation">Launch Simulation</button>
                <button class="btn btn-secondary">Watch Demo</button>
                
                <div class="whats-included">
                    <h4>Simulation Features:</h4>
                    <ul class="included-list">
                        <li>4 immersive scenarios ✓</li>
                        <li>Real-time decision feedback ✓</li>
                        <li>Multiplayer collaboration ✓</li>
                        <li>Data-driven outcomes ✓</li>
                        <li>Performance analytics ✓</li>
                        <li>Completion certificate ✓</li>
                    </ul>
                </div>
            </div>

            <div class="cta-card">
                <h2 class="section-title">Technical Requirements</h2>
                <div class="info-list">
                    <li>
                        <span class="info-label">Platform</span>
                        <span class="info-value">Web Browser</span>
                    </li>
                    <li>
                        <span class="info-label">Internet</span>
                        <span class="info-value">Required</span>
                    </li>
                    <li>
                        <span class="info-label">Devices</span>
                        <span class="info-value">Desktop/Tablet</span>
                    </li>
                    <li>
                        <span class="info-label">Multiplayer</span>
                        <span class="info-value">2-6 Players</span>
                    </li>
                </div>
            </div>

            <div class="cta-card">
                <h2 class="section-title">Simulation Director</h2>
                <div class="scenario-item">
                    <div class="scenario-header">
                        <span class="scenario-title">Dr. AidData Research Team</span>
                        <div class="scenario-toggle">▼</div>
                    </div>
                    <div class="scenario-content">
                        <div class="scenario-details">
                            <p><strong>Policy Simulation Specialists</strong></p>
                            <p>Designed by AidData researchers with extensive experience in development finance policy analysis, stakeholder engagement, and interactive learning design.</p>
                        </div>
                    </div>
                </div>
            </div>
        </aside>
    </main>

    <script>
        // Scenario accordion functionality
        const scenarioHeaders = document.querySelectorAll('.scenario-header');
        
        scenarioHeaders.forEach(header => {
            header.addEventListener('click', function() {
                const scenario = this.parentElement;
                const isActive = scenario.classList.contains('active');
                
                // Close all scenarios
                document.querySelectorAll('.scenario-item').forEach(s => {
                    s.classList.remove('active');
                });
                
                // Open clicked scenario if it wasn't active
                if (!isActive) {
                    scenario.classList.add('active');
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
        document.querySelector('.btn-simulation').addEventListener('click', function() {
            console.log('Launch Simulation clicked');
            alert('Launching simulation environment...');
        });

        const watchDemoBtn = document.querySelector('.btn-secondary');
        if (watchDemoBtn && watchDemoBtn.textContent.includes('Demo')) {
            watchDemoBtn.addEventListener('click', function() {
                console.log('Watch Demo clicked');
                alert('Opening simulation demo...');
            });
        }
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

<!-- Custom Video Player Scripts and Styles -->
<link rel="stylesheet" href="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/css/video-player.css">
<script src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/js/video-player.js"></script>
</body>
</html>

