<?php
/**
 * Template Name: AidData Training Hub
 * Template Post Type: page
 *
 * A custom front page template for the AidData Training Hub
 *
 * @package WordPress
 * @subpackage Twenty_Twenty_Five
 * @since Twenty Twenty-Five 1.0
 */

// Enqueue authentication-specific styles
wp_enqueue_style('auth-styles', get_template_directory_uri() . '/assets/css/auth-styles.css', array(), '1.0.0');

// Enqueue loading screen styles
wp_enqueue_style('loading-screen', get_template_directory_uri() . '/assets/css/loading-screen.css', array(), '1.0.0');

get_header('empty');
?>

<!-- Scrollbar Styling -->
<style>
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
</style>

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
    <section class="welcome-section">
        <div class="welcome-content">
            <h2>Training Hub</h2>
            <p>Aligned with AidData's commitment to actionable research and evidence-based insights, this platform provides flexible learning pathways for data-driven professionals.</p>
            <button class="learn-more-btn">
                <span>Learn More</span>
                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
        </div>
        <div class="welcome-shapes">
            <div class="shape shape-1"></div>
            <div class="shape shape-2"></div>
            <div class="shape shape-3"></div>
        </div>
    </section>

    <!-- Info Drawer -->
    <div class="info-drawer">
        <div class="drawer-content">
            <button class="close-drawer" aria-label="Close drawer">
                <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M18 6L6 18M6 6l12 12"/>
                </svg>
            </button>
            <div class="drawer-header">
                <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/logodark.png" alt="AidData Logo" class="drawer-logo">
                <h2 class="drawer-heading">Training and Professional Development</h2>
            </div>
            <div class="drawer-grid">
                <div class="drawer-left">
                    <div class="video-section">
                        <div style="position: relative; width: 100%; height: 0; padding-bottom: 56.25%">
                            <iframe src="https://wmedu.hosted.panopto.com/Panopto/Pages/Embed.aspx?id=34384276-23d0-4431-aa29-b3d801014488&autoplay=false&offerviewer=false&showtitle=false&showbrand=false&captions=false&interactivity=none" style="border: 1px solid #464646; position: absolute; top: 0; left: 0; width: 100%; height: 100%; box-sizing: border-box;" allowfullscreen allow="autoplay" aria-label="Panopto Embedded Video Player" aria-description="AidData Training Overview Video"></iframe>
                        </div>
                    </div>

                    <div class="opportunities-section">
                        <h4>Current Opportunities</h4>
                        <div class="opportunities-list">
                            <div class="opportunity-item">
                                <div class="opportunity-badge">New</div>
                                <h5>Pakistan Data Journalism Training</h5>
                                <p>Join our upcoming data journalism training progam focused on development finance reporting. Perfect for journalists and media professionals looking to enhance their data analysis skills.</p>
                                <div class="opportunity-meta">
                                    <span class="opportunity-date">Starting July 2025</span>
                                    <span class="opportunity-format">Hybrid Format</span>
                                </div>
                                <a href="#" class="opportunity-link">Learn More →</a>
                            </div>
                        </div>
                    </div>

                    <div class="contact-section">
                        <h4>Contact Information</h4>
                        <div class="contact-person">
                            <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/sethu_nguna.png" alt="Sethu Nguna" class="contact-avatar">
                            <div class="contact-details">
                                <p>Questions about the courses? Interested in partnering?</p>
                                <p><strong>Sethu Nguna</strong>
                                Manager, Training & Instructional Design<br>
                                <a href="mailto:snguna@aiddata.org">snguna@aiddata.org</a></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="drawer-right">
                    <div class="info-block">
                        <h4>Overview</h4>
                        <p>Our training employs a hybrid (blended virtual and in-person) model that combines foundational online courses delivered through William & Mary's Studio for Teaching & Learning Innovation with in-person workshops and policy dialogues. In-person sessions are country- and region-specific for targeted professional development. The curriculum blends academic research with practical methodologies, providing a comprehensive skillset for meaningful engagement with data and development finance.</p>
                    </div>

                    <div class="info-block">
                        <h4>Expertise that empowers</h4>
                        <div class="expertise-grid">
                            <div class="expertise-tile">
                                <div class="expertise-image">
                                    <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/data_journalism_pakistan.jpg" alt="Data journalism course in Pakistan">
                                </div>
                                <div class="expertise-content">
                                    <h5>Data-to-policy curriculum</h5>
                                    <p>AidData data and tools are incorporated to empower policymakers, analysts, and journalists with the skills to transform data into insights and actionable decisions in the Global South and Global North.</p>
                                    <span class="expertise-caption">Journalists take a course on data journalism led by AidData and the Center for Excellence in Journalism in Pakistan.</span>
                                </div>
                            </div>

                            <div class="expertise-tile">
                                <div class="expertise-image">
                                    <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/seth_goodman_workshop.jpg" alt="Dr. Seth Goodman leading a workshop">
                                </div>
                                <div class="expertise-content">
                                    <h5>Experienced subject matter experts</h5>
                                    <p>AidData researchers and educators bring intensive teaching experience at William & Mary, along with extensive real world experience applying learning in policy and civil society contexts.</p>
                                    <span class="expertise-caption">AidData Research Scientist Dr. Seth Goodman leads a workshop on remote sensing and machine learning at the Green Climate Fund.</span>
                                </div>
                            </div>

                            <div class="expertise-tile">
                                <div class="expertise-image">
                                    <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/sethu_nguna_training.jpeg" alt="Sethu Nguna leading a training session">
                                </div>
                                <div class="expertise-content">
                                    <h5>Innovative strategies</h5>
                                    <p>Hybrid and technology-based delivery is used to ensure learners can effectively interpret data and implement solutions in real-world scenarios.</p>
                                    <span class="expertise-caption">Sethu Nguna, AidData's Manager, Training & Instructional Design at AidData, has developed institutional digital transformation strategies, courseware design and infrastructure, and academic curricula.</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <section class="featured-content">
        <div class="filter-section">
            <div class="filter-container">
                <div class="filter-buttons">
                    <button class="filter-btn active" data-filter="all">
                        All
                    </button>
                    <button class="filter-btn" data-filter="course">
                        Courses
                    </button>
                    <button class="filter-btn" data-filter="tutorial">
                        Tutorial
                    </button>
                    <button class="filter-btn" data-filter="interview">
                        Interviews
                    </button>
                    <button class="filter-btn" data-filter="tools">
                        Tools
                    </button>
                </div>
            </div>
        </div>

        <div class="featured-grid">
            <!-- Empty State Template -->
            <div class="empty-state">
                <div class="empty-state-animation">
                    <div class="thinking-animation">
                        <div class="bubble"></div>
                        <div class="bubble"></div>
                        <div class="bubble"></div>
                    </div>
                    <svg class="hero-character" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="8" r="7"/>
                        <path d="M12 15v6"/>
                        <path d="M8 21h8"/>
                        <circle cx="9" cy="7" r="1" fill="currentColor"/>
                        <circle cx="15" cy="7" r="1" fill="currentColor"/>
                        <path d="M8 12s2 1 4 1 4-1 4-1"/>
                    </svg>
                </div>
                <h3 class="empty-message">Hmm... This section is as empty as a developer's coffee cup at 9 AM!</h3>
                <p class="empty-description">Don't worry, we're brewing up some amazing content for this category.</p>
            </div>

            <!-- Course 1: Navigating Global Development Finance -->
            <div class="featured-course" data-type="course">
                <div class="course-preview">
                    <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/global_finance.png" alt="Global Development Finance" class="preview-image">
                    <div class="preview-overlay">
                        <div class="preview-content">
                            <h3>Navigating Global Development Finance</h3>
                            <div class="course-categories">
                                <span class="category-tag tutorial">Course</span>
                                <span class="category-tag badge">Digital Badge</span>
                                <span class="category-tag multimodal">Multimodal</span>
                            </div>
                            <p>Learn the fundamentals of development finance, from funding sources to implementation strategies</p>
                            <div class="course-stats">
                                <span class="stat">
                                    <svg viewBox="0 0 24 24" width="16" height="16">
                                        <circle cx="12" cy="12" r="10" fill="none" stroke="currentColor" stroke-width="2"/>
                                        <polyline points="12 6 12 12 16 14" fill="none" stroke="currentColor" stroke-width="2"/>
                                    </svg>
                                    12-16 hours
                                </span>
                                <span class="stat">
                                    <svg viewBox="0 0 24 24" width="16" height="16">
                                        <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" fill="none" stroke="currentColor" stroke-width="2"/>
                                    </svg>
                                    Introductory
                                </span>
                                <span class="stat">
                                    <svg viewBox="0 0 24 24" width="16" height="16">
                                        <polygon points="12,2 22,20 2,20" fill="none" stroke="white" stroke-width="2"/>
                                    </svg>
                                    Data Journalism
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="course-actions">
                    <button class="primary-button start-learning" data-course="<?php echo home_url('/t-h/navigating-global-development-finance/'); ?>">Start Learning</button>
                    <button class="trailer-button" data-video="https://wmedu.hosted.panopto.com/Panopto/Pages/Embed.aspx?id=ce80f637-c233-465b-a586-b3d801016b15&autoplay=false&offerviewer=false&showtitle=false&showbrand=false&captions=false&interactivity=none">
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <polygon points="10 8 16 12 10 16" fill="currentColor"/>
                        </svg>
                        Watch Trailer
                    </button>
                    <button class="secondary-button" data-modal="navigatingGlobalDevelopmentFinanceInfo">Info</button>
                </div>
            </div>

            <!-- Course 2: Global Chinese Development Finance -->
            <div class="featured-course" data-type="tutorial">
                <div class="course-preview">
                    <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/china_dashboard.png" alt="China Dashboard Tutorial" class="preview-image">
                    <div class="preview-overlay">
                        <div class="preview-content">
                            <h3>Global Chinese Development Finance</h3>
                            <div class="course-categories">
                                <span class="category-tag tutorial">Tutorial</span>
                                <span class="category-tag">Sovereign Finance</span>
                                <span class="category-tag">Certificate</span>
                            </div>
                            <p>Learn how to effectively use the China.AidData.org dashboard to explore and analyze Chinese development finance data, track projects, and generate insights.</p>
                            <div class="course-stats">
                                <span class="stat">
                                    <svg viewBox="0 0 24 24" width="16" height="16">
                                        <circle cx="12" cy="12" r="10" fill="none" stroke="currentColor" stroke-width="2"/>
                                        <polyline points="12 6 12 12 16 14" fill="none" stroke="currentColor" stroke-width="2"/>
                                    </svg>
                                    30-45 mins
                                </span>
                                <span class="stat">
                                    <svg viewBox="0 0 24 24" width="16" height="16">
                                        <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" fill="none" stroke="currentColor" stroke-width="2"/>
                                    </svg>
                                    All Levels
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="course-actions">
                    <button class="primary-button start-learning" data-course="<?php echo home_url('/courses/global-chinese-development-finance/'); ?>">Start Tutorial</button>
                    <button class="trailer-button" data-video="https://wmedu.hosted.panopto.com/Panopto/Pages/Embed.aspx?id=db8251fc-f910-4fc0-a5d5-b379004d81da&autoplay=false&offerviewer=false&showtitle=false&showbrand=false&captions=true&interactivity=none">
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <polygon points="10 8 16 12 10 16" fill="currentColor"/>
                        </svg>
                        Watch Preview
                    </button>
                    <button class="secondary-button" data-modal="chinaDashboardInfo">Info</button>
                </div>
            </div>

            

            <!-- Course 7: Geospatial Impact Evaluations Tutorial -->
            <div class="featured-course" data-type="tutorial">
                <div class="course-preview">
                    <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/GIE_coursethumbnail.jpg" alt="Geospatial Impact Evaluations Tutorial" class="preview-image">
                    <div class="preview-overlay">
                        <div class="preview-content">
                            <h3>Geospatial Impact Evaluations</h3>
                            <div class="course-categories">
                                <span class="category-tag tutorial">Tutorial</span>
                                <span class="category-tag">Geospatial Data</span>
                                <span class="category-tag">Certificate</span>
                            </div>
                            <p>An introduction to AidData's geospatial impact evaluation (GIE) methodology for evaluating development interventions using satellite data.</p>
                            <div class="course-stats">
                                <span class="stat">
                                    <svg viewBox="0 0 24 24" width="16" height="16">
                                        <circle cx="12" cy="12" r="10" fill="none" stroke="currentColor" stroke-width="2"/>
                                        <polyline points="12 6 12 12 16 14" fill="none" stroke="currentColor" stroke-width="2"/>
                                    </svg>
                                    45-60 mins
                                </span>
                                <span class="stat">
                                    <svg viewBox="0 0 24 24" width="16" height="16">
                                        <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" fill="none" stroke="currentColor" stroke-width="2"/>
                                    </svg>
                                    Intermediate
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="course-actions">
                    <button class="primary-button start-learning" data-course="<?php echo home_url('/courses/geospatial-impact-evaluations/'); ?>">Start Tutorial</button>
                    <button class="trailer-button" data-video="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/videos/tut1.mp4">
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <polygon points="10 8 16 12 10 16" fill="currentColor"/>
                        </svg>
                        Watch Preview
                    </button>
                    <button class="secondary-button" data-modal="geospatialImpactEvaluationsInfo">Info</button>
                </div>
            </div>

            <!-- Course 6: How China Lends Interview -->
            <div class="featured-course" data-type="interview">
                <div class="course-preview">
                    <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/How_China_Lends&_Collateralizes_interview.png" alt="How China Lends Interview" class="preview-image">
                    <div class="preview-overlay">
                        <div class="preview-content">
                            <h3>Decoding China's Debt Contracts</h3>
                            <div class="course-categories">
                                <span class="category-tag interview">Interview</span>
                                <span class="category-tag">Sovereign Finance</span>
                            </div>
                            <p>Investigating Chinese loan contract terms, conditions, and patterns <br>across countries and sectors with Ameya Joshi</p><br>
                        </div>
                    </div>
                </div>
                <div class="course-actions">
                    <button class="primary-button start-learning" data-course="<?php echo home_url('/t-h/decoding-china-debt-contracts/'); ?>">Watch Interview</button>
                </div>
            </div>

            <!-- Tool: Credit Evaluation Tool -->
            <div class="featured-course" data-type="tools">
                <div class="course-preview">
                    <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/credit_shopper.png" alt="Credit Evaluation Tool" class="preview-image">
                    <div class="preview-overlay">
                        <div class="preview-content">
                            <h3>Credit Evaluation Tool</h3>
                            <div class="course-categories">
                                <span class="category-tag">Tool</span>
                                <span class="category-tag">Sovereign Finance</span>
                                <span class="category-tag">Decision Support</span>
                            </div>
                            <p>Compare lender terms, stress-test repayment scenarios, and benchmark financing packages.</p>
                            <div class="course-stats">
                                <span class="stat">
                                    <svg viewBox="0 0 24 24" width="16" height="16">
                                        <circle cx="12" cy="12" r="10" fill="none" stroke="currentColor" stroke-width="2"/>
                                        <polyline points="12 6 12 12 16 14" fill="none" stroke="currentColor" stroke-width="2"/>
                                    </svg>
                                    Interactive
                                </span>
                                <span class="stat">
                                    <svg viewBox="0 0 24 24" width="16" height="16">
                                        <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" fill="none" stroke="currentColor" stroke-width="2"/>
                                    </svg>
                                    All Levels
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="course-actions">
                    <button class="secondary-button full-width-button" data-modal="creditEvaluationToolInfo">Learn More</button>
                </div>
            </div>

            <!-- Interview 1: Inside the Belt & Road Reboot - HIDDEN -->
            <?php /* <div class="featured-course" data-type="interview">
                <div class="course-preview">
                    <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/inside_the_belt_and_road_initiative_reboot_interview.png?v=<?php echo time(); ?>" alt="Inside the Belt & Road Reboot" style="width: 100%; height: 100%; object-fit: cover;">
                    <div class="preview-overlay">
                        <div class="preview-content">
                            <h3>Inside the Belt & Road Reboot</h3>
                            <div class="course-categories">
                                <span class="category-tag interview">Interview</span>
                            </div>
                            <p>Exploring the major shifts in China's flagship infrastructure initiative</p>
                            <div class="course-stats">
                                <span class="stat">
                                    <svg viewBox="0 0 24 24" width="16" height="16">
                                        <polygon points="12,2 22,20 2,20" fill="none" stroke="white" stroke-width="2"/>
                                    </svg>
                                    Development Finance
                                </span>
                                <span class="stat">
                                    <svg viewBox="0 0 24 24" width="16" height="16">
                                        <polygon points="12,2 22,20 2,20" fill="none" stroke="white" stroke-width="2"/>
                                    </svg>
                                    Underreported Financial Flows
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="course-actions">
                    <button class="primary-button start-learning" data-course="/interviews/">Watch Interview</button>
                    <button class="secondary-button" data-modal="beltRoadRebootInfo">Info</button>
                </div>
            </div> */ ?>

            <!-- Interview 2: Seeing Development from Above - HIDDEN -->
            <?php /* <div class="featured-course" data-type="interview">
                <div class="course-preview">
                    <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/seeing_development_from_above_interview.png?v=<?php echo time(); ?>" alt="Seeing Development from Above" style="width: 100%; height: 100%; object-fit: cover;">
                    <div class="preview-overlay">
                        <div class="preview-content">
                            <h3>Seeing Development from Above</h3>
                            <div class="course-categories">
                                <span class="category-tag interview">Interview</span>
                            </div>
                            <p>Using Geospatial Data to Measure What Works in Development</p>
                            <div class="course-stats">
                                <span class="stat">
                                    <svg viewBox="0 0 24 24" width="16" height="16">
                                        <polygon points="12,2 22,20 2,20" fill="none" stroke="white" stroke-width="2"/>
                                    </svg>
                                    Geospatial Data
                                </span>
                                <span class="stat">
                                    <svg viewBox="0 0 24 24" width="16" height="16">
                                        <polygon points="12,2 22,20 2,20" fill="none" stroke="white" stroke-width="2"/>
                                    </svg>
                                    Development
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="course-actions">
                    <button class="primary-button start-learning" data-course="/interviews/">Watch Interview</button>
                    <button class="secondary-button" data-modal="seeingDevelopmentInfo">Info</button>
                </div>
            </div> */ ?>

            <!-- Interview 3: Decoding China's Debt Contracts - HIDDEN -->
            <?php /* <div class="featured-course" data-type="interview">
                <div class="course-preview">
                    <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/decoding_chinas_debt_contracts_interview.png?v=<?php echo time(); ?>" alt="Decoding China's Debt Contracts" style="width: 100%; height: 100%; object-fit: cover;">
                    <div class="preview-overlay">
                        <div class="preview-content">
                            <h3>Decoding China's Debt Contracts</h3>
                            <div class="course-categories">
                                <span class="category-tag interview">Interview</span>
                            </div>
                            <p>The Mechanics of China's Unique Approach to International Finance</p>
                            <div class="course-stats">
                                <span class="stat">
                                    <svg viewBox="0 0 24 24" width="16" height="16">
                                        <polygon points="12,2 22,20 2,20" fill="none" stroke="white" stroke-width="2"/>
                                    </svg>
                                    Development Finance
                                </span>
                                <span class="stat">
                                    <svg viewBox="0 0 24 24" width="16" height="16">
                                        <polygon points="12,2 22,20 2,20" fill="none" stroke="white" stroke-width="2"/>
                                    </svg>
                                    Underreported Financial Flows
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="course-actions">
                    <button class="primary-button start-learning" data-course="/interviews/">Watch Interview</button>
                    <button class="secondary-button" data-modal="decodingChinaDebtInfo">Info</button>
                </div>
            </div> */ ?>

            <!-- Interview 4: Harboring Global Ambitions -->
            <div class="featured-course" data-type="interview">
                <div class="course-preview">
                    <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/harboring_global_ambitions_interview.png?v=<?php echo time(); ?>" alt="Harboring Global Ambitions" style="width: 100%; height: 100%; object-fit: cover;">
                    <div class="preview-overlay">
                        <div class="preview-content">
                            <h3>Harboring Global Ambitions</h3>
                            <div class="course-categories">
                                <span class="category-tag interview">Interview</span>
                                <span class="category-tag">National Security</span>
                            </div>
                            <p>Using Development Finance Data to Analyze Military Expansion<br>with Alex Wooley</p><br>
                        </div>
                    </div>
                </div>
                <div class="course-actions">
                    <button class="primary-button start-learning" data-course="<?php echo home_url('/t-h/harboring-global-ambitions/'); ?>">Watch Interview</button>
                </div>
            </div>

        </div>
    </section>

    <!-- Info Modal for China.AidData.org Tutorial -->
    <div class="info-modal" id="chinaDashboardInfo">
        <div class="info-container">
            <button class="close-info" aria-label="Close">&times;</button>
            <div class="info-content">
                <div class="title-section">
                    <div style="width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                        <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/certificate.png" alt="Tutorial Certificate" style="width: 35px; height: 35px;">
                    </div>
                    <h3>Global Chinese Development Finance</h3>
                </div>

                <div class="info-description">
                    <h4>About this Tutorial</h4>
                    <p>Get hands-on experience with AidData's premier tool for tracking Chinese development finance. This quick tutorial will show you how to navigate the dashboard, understand its features, and extract valuable insights about Chinese overseas development projects.</p>
                    
                    <h4>What You'll Learn</h4>
                    <ul class="learning-objectives">
                        <li>How to access and interpret project-level data on Chinese development finance</li>
                        <li>Techniques for filtering and searching across 13,000+ projects</li>
                        <li>Ways to analyze geographical distribution of Chinese development finance</li>
                        <li>Methods for comparing projects across sectors and regions</li>
                        <li>Steps to download and cite data for research</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Info Modal for How China Lends Dashboard Tutorial -->
    <div class="info-modal" id="howchinalendsInfo">
        <div class="info-container">
            <button class="close-info" aria-label="Close">&times;</button>
            <div class="info-content">
                <div class="title-section">
                    <div style="width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                        <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/certificate.png" alt="Tutorial Certificate" style="width: 35px; height: 35px;">
                    </div>
                    <h3>How China Lends and Collateralizes</h3>
                </div>
                <div class="info-description">
                    <h4>About this Interview</h4>
                    <p>Join expert researchers as they discuss the groundbreaking dataset on Chinese loan contracts and what it reveals about international lending practices. This interview provides unique insights into contract terms, conditions, and patterns across countries and sectors, with expert analysis of their implications for global development finance.</p>

                    <h4>What You'll Discover</h4>
                    <ul class="learning-objectives">
                        <li>Key findings from analyzing thousands of Chinese loan contracts</li>
                        <li>How contract terms vary across different countries and sectors</li>
                        <li>The unique features that distinguish Chinese development finance</li>
                        <li>Patterns in collateralization and lending conditions</li>
                        <li>Implications for borrowing countries and global finance</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Info Modal for Balancing the Scales -->
    <div class="info-modal" id="balancingScalesInfo">
        <div class="info-container">
            <button class="close-info" aria-label="Close">&times;</button>
            <div class="info-content">
                <div class="title-section">
                    <div style="width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                        <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/game_controller.png" alt="Game Controller Icon" style="width: 35px; height: 35px;">
                    </div>
                    <h3>Balancing the Scales</h3>
                </div>
                
                <div class="course-instructors">
                    <h4>Learning Designer</h4>
                    <div class="instructor-avatars">
                        <div class="instructor">
                            <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/sethu_nguna.png" alt="Sethu Nguna" class="instructor-avatar">
                            <div class="instructor-info">
                                <h5>Sethu Nguna</h5>
                                <p>Manager, Training & Instructional Design</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="inline-partnership">
                    <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/wm_logo_white.png" alt="William & Mary Logo">
                    <p>Developed by the AidData Research Lab at William & Mary</p>
                </div>

                <div class="info-description">
                    <h4>About this Game</h4>
                    <p>Balancing Scales is an immersive strategy game that puts you in the role of a global development policymaker. Armed with real-world geospatial data and development indicators, you'll face the complex challenge of allocating limited foreign aid resources across regions grappling with climate change, migration crises, and persistent poverty. As you navigate this intricate landscape, you'll need to balance competing priorities in health, infrastructure, and education while managing donor constraints and political pressures. Through this hands-on experience, the game illuminates the real-world complexities of development finance and underscores the critical importance of evidence-based decision-making in creating meaningful impact.</p>

                    <div class="system-requirements" style="margin-top: 20px; padding: 15px; background: rgba(255,255,255,0.1); border-radius: 8px;">
                        <h4 style="margin-top: 0;">System Requirements</h4>
                        <ul style="margin-bottom: 0;">
                            <li>Modern web browser (Chrome, Firefox, Safari, or Edge)</li>
                            <li>Stable internet connection for real-time data updates</li>
                            <li>Minimum screen resolution: 1024x768</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Info Modal for Credit Shopper Tool -->
    <div class="info-modal" id="creditShopperInfo">
        <div class="info-container">
            <button class="close-info" aria-label="Close">&times;</button>
            <div class="info-content">
                <div class="title-section">
                    <div style="width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                        <svg viewBox="0 0 24 24" width="35" height="35" fill="none" stroke="#115740" stroke-width="2">
                            <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>
                        </svg>
                    </div>
                    <h3>Credit Shopper Tool</h3>
                </div>
                
                <div class="course-instructors">
                    <h4>Tool Developers</h4>
                    <div class="instructor-avatars">
                        <div class="instructor">
                            <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/john_custer.svg" alt="John Custer" class="instructor-avatar">
                            <div class="instructor-info">
                                <h5>John Custer</h5>
                                <p>Deputy Director, Communications & Data Analytics</p>
                            </div>
                        </div>
                        <div class="instructor">
                            <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/asad_sami.jpg" alt="Asad Sami" class="instructor-avatar">
                            <div class="instructor-info">
                                <h5>Asad Sami</h5>
                                <p>Senior Program Manager</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="inline-partnership">
                    <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/wm_logo_white.png" alt="William & Mary Logo">
                    <p>Developed by the AidData Research Lab at William & Mary</p>
                </div>

                <div class="info-description">
                    <h4>About this Tool</h4>
                    <p>The Credit Shopper Tool is a powerful resource that leverages AidData's comprehensive development finance datasets to enable sophisticated benchmarking and comparison of financing options. Development professionals can analyze and evaluate credit terms from various development finance institutions, comparing them against historical data and market trends.</p>
                    
                    <h4>Key Features</h4>
                    <ul class="learning-objectives">
                        <li>Real-time comparison of credit options from multiple development finance institutions</li>
                        <li>Interactive calculators for loan terms, interest rates, and repayment schedules</li>
                        <li>Customizable filters for sector, region, and project type</li>
                        <li>Detailed analysis of terms and conditions</li>
                        <li>Export functionality for reports and comparisons</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Info Modal for Credit Evaluation Tool -->
    <div class="info-modal" id="creditEvaluationToolInfo">
        <div class="info-container">
            <button class="close-info" aria-label="Close">&times;</button>
            <div class="info-content">
                <div class="title-section">
                    <div style="width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                        <svg viewBox="0 0 24 24" width="35" height="35" fill="none" stroke="#115740" stroke-width="2">
                            <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>
                        </svg>
                    </div>
                    <h3>Credit Evaluation Tool</h3>
                </div>
                
                <div class="course-instructors">
                    <h4>Tool Developers</h4>
                    <div class="instructor-avatars">
                        <div class="instructor">
                            <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/john_custer.svg" alt="John Custer" class="instructor-avatar">
                            <div class="instructor-info">
                                <h5>John Custer</h5>
                                <p>Deputy Director, Communications & Data Analytics</p>
                            </div>
                        </div>
                        <div class="instructor">
                            <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/asad_sami.jpg" alt="Asad Sami" class="instructor-avatar">
                            <div class="instructor-info">
                                <h5>Asad Sami</h5>
                                <p>Senior Program Manager</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="inline-partnership">
                    <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/wm_logo_white.png" alt="William & Mary Logo">
                    <p>Developed by the AidData Research Lab at William & Mary</p>
                </div>

                <div class="info-description">
                    <h4>About this Tool</h4>
                    <p>The Credit Evaluation Tool helps teams compare financing offers, model repayment trajectories, and stress-test loan packages against key macroeconomic assumptions. Use it to benchmark terms across lenders and document trade-offs for decision makers.</p>
                    
                    <h4>Key Features</h4>
                    <ul class="learning-objectives">
                        <li>Side-by-side comparison of credit terms across lenders</li>
                        <li>Scenario modeling for interest rates, grace periods, and maturities</li>
                        <li>Risk flags for collateralization, fees, and covenant structures</li>
                        <li>Benchmarking against historical deals and peer countries</li>
                        <li>Exportable summaries for approvals and negotiations</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Info Modal for Expert Insights Series -->
    <div class="info-modal" id="expertInsightsInfo">
        <div class="info-container">
            <button class="close-info" aria-label="Close">&times;</button>
            <div class="info-content">
                <div class="title-section">
                    <div style="width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                        <svg viewBox="0 0 24 24" width="35" height="35" fill="none" stroke="#115740" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <polygon points="10 8 16 12 10 16" fill="#115740"/>
                        </svg>
                    </div>
                    <h3>Expert Insights: Development Finance Leaders</h3>
                </div>
                
                <div class="inline-partnership">
                    <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/wm_logo_white.png" alt="William & Mary Logo">
                    <p>Developed by the AidData Research Lab at William & Mary</p>
                </div>

                <div class="info-description">
                    <h4>About this Series</h4>
                    <p>Coming soon.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Info Modal for Securing Development Funding -->
    <div class="info-modal" id="securingdevelopmentfundingInfo">
        <div class="info-container">
            <button class="close-info" aria-label="Close">&times;</button>
            <div class="info-content">
                <div class="title-section">
                    <div style="width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                        <img src="wp-content/themes/twentytwentyfour/assets/images/certificate.png" alt="Simulation Badge" style="width: 35px; height: 35px;">
                    </div>
                    <h3>Securing Development Funding</h3>
                </div>
                
                <div class="course-instructors">
                    <h4>Course Instructors</h4>
                    <div class="instructor-avatars">
                        <div class="instructor">
                            <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/brooke_e.png" alt="Brooke Escobar" class="instructor-avatar">
                            <div class="instructor-info">
                                <h5>Brooke Escobar</h5>
                                <p>Interim Director, Chinese Development Finance Program</p>
                            </div>
                        </div>
                        <div class="instructor">
                            <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/asad_sami.jpg" alt="Asad Sami" class="instructor-avatar">
                            <div class="instructor-info">
                                <h5>Asad Sami</h5>
                                <p>Senior Program Manager</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="inline-partnership">
                    <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/wm_logo_white.png" alt="William & Mary Logo">
                    <p>Developed by the AidData Research Lab at William & Mary</p>
                </div>

                <div class="info-description">
                    <h4>About this Simulation</h4>
                    <p>Master the art of analyzing and selecting optimal financing packages for development projects. This interactive simulation puts you in the role of a development finance professional making critical funding decisions.</p>
                    
                    <h4>Who is this Course for?</h4>
                    <ul class="learning-objectives">
                        <li>Development finance professionals involved in project funding decisions</li>
                        <li>Government officials responsible for securing development financing</li>
                        <li>International development organizations seeking funding strategies</li>
                        <li>Financial analysts working in development finance institutions</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Info Modal for Navigating Debt Distress -->
    <div class="info-modal" id="navigatingdebtdistressInfo">
        <div class="info-container">
            <button class="close-info" aria-label="Close">&times;</button>
            <div class="info-content">
                <div class="title-section">
                    <div style="width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                        <img src="wp-content/themes/twentytwentyfour/assets/images/certificate.png" alt="Simulation Badge" style="width: 35px; height: 35px;">
                    </div>
                    <h3>Navigating Debt Distress</h3>
                </div>
                
                <div class="course-instructors">
                    <h4>Course Instructors</h4>
                    <div class="instructor-avatars">
                        <div class="instructor">
                            <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/brooke_e.png" alt="Brooke Escobar" class="instructor-avatar">
                            <div class="instructor-info">
                                <h5>Brooke Escobar</h5>
                                <p>Interim Director, Chinese Development Finance Program</p>
                            </div>
                        </div>
                        <div class="instructor">
                            <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/asad_sami.jpg" alt="Asad Sami" class="instructor-avatar">
                            <div class="instructor-info">
                                <h5>Asad Sami</h5>
                                <p>Senior Program Manager</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="inline-partnership">
                    <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/wm_logo_white.png" alt="William & Mary Logo">
                    <p>Developed by the AidData Research Lab at William & Mary</p>
                </div>

                <div class="info-description">
                    <h4>About this Simulation</h4>
                    <p>Learn strategies for managing and resolving sovereign debt challenges in developing economies. This simulation provides hands-on experience in dealing with complex debt distress scenarios.</p>
                    
                    <h4>Who is this Course for?</h4>
                    <ul class="learning-objectives">
                        <li>Government officials managing sovereign debt portfolios</li>
                        <li>Development finance professionals working on debt sustainability</li>
                        <li>Financial analysts in international financial institutions</li>
                        <li>Policy advisors focused on debt management strategies</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Info Modal for Navigating Global Development Finance -->
    <div class="info-modal" id="navigatingGlobalDevelopmentFinanceInfo">
        <div class="info-container">
            <button class="close-info" aria-label="Close">&times;</button>
            <div class="info-content">
                <div class="title-section">
                <div style="width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                        <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/data_journalism_badge.png" alt="Course Badge" style="width: 35px; height: 35px;">
                    </div>
                    <h3>Navigating Global Development Finance</h3>
                </div>
                
                <div class="course-instructors">
                    <h4>Course Instructors</h4>
                    <div class="instructor-avatars">
                        <div class="instructor">
                            <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/alex_wooley.jpg" alt="Alex Wooley" class="instructor-avatar">
                            <div class="instructor-info">
                                <h5>Alex Wooley</h5>
                                <p>Director, Partnerships & Communications</p>
                            </div>
                        </div>
                        <div class="instructor">
                            <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/john_custer.svg" alt="John Custer" class="instructor-avatar">
                            <div class="instructor-info">
                                <h5>John Custer</h5>
                                <p>Deputy Director, Communications & Data Analytics</p>
                            </div>
                        </div>
                        <div class="instructor">
                            <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/sethu_nguna.png" alt="Sethu Nguna" class="instructor-avatar">
                            <div class="instructor-info">
                                <h5>Sethu Nguna</h5>
                                <p>Manager, Training & Instructional Design</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="inline-partnership">
                    <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/wm_logo_white.png" alt="William & Mary Logo">
                    <p>Delivered in partnership with the W&M Studio for Teaching and Learning Innovation</p>
                </div>

                <div class="info-description">
                    <h4>About this Course</h4>
                    <p>Learn the fundamentals of development finance, from funding sources to implementation strategies. This comprehensive course provides a solid foundation in understanding global development finance mechanisms and practices.</p>
                    
                    <h4>Who is this Course for?</h4>
                    <ul class="learning-objectives">
                        <li>Development finance professionals</li>
                        <li>Government officials working in finance and development</li>
                        <li>NGO staff involved in project funding</li>
                        <li>Students pursuing careers in international development</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Info Modal for Critical Data Analysis -->
    <div class="info-modal" id="criticalDataAnalysisInfo">
        <div class="info-container">
            <button class="close-info" aria-label="Close">&times;</button>
            <div class="info-content">
                <div class="title-section">
                    <div style="width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                        <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/data_analysis_badge.png" alt="Course Badge" style="width: 35px; height: 35px;">
                    </div>
                    <h3>Critical Data Analysis and Visualization</h3>
                </div>
                
                <div class="course-instructors">
                    <h4>Course Instructors</h4>
                    <div class="instructor-avatars">
                        <div class="instructor">
                            <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/john_custer.svg" alt="John Custer" class="instructor-avatar">
                            <div class="instructor-info">
                                <h5>John Custer</h5>
                                <p>Deputy Director, Communications & Data Analytics</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="inline-partnership">
                    <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/wm_logo_white.png" alt="William & Mary Logo">
                    <p>Delivered in partnership with the W&M Studio for Teaching and Learning Innovation</p>
                </div>

                <div class="info-description">
                    <h4>About this Course</h4>
                    <p>Master data analysis techniques and create compelling visualizations for development finance insights. This course combines theoretical knowledge with practical skills in data analysis and visualization.</p>
                    
                    <h4>Who is this Course for?</h4>
                    <ul class="learning-objectives">
                        <li>Data analysts in development organizations</li>
                        <li>Researchers working with development data</li>
                        <li>Policy analysts and decision-makers</li>
                        <li>Development professionals seeking to enhance their data skills</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Info Modal for Geospatial Impact Evaluations -->
    <div class="info-modal" id="geospatialImpactEvaluationsInfo">
        <div class="info-container">
            <button class="close-info" aria-label="Close">&times;</button>
            <div class="info-content">
                <div class="title-section">
                    <div style="width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                        <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/certificate.png" alt="Tutorial Certificate" style="width: 35px; height: 35px;">
                    </div>
                    <h3>Geospatial Impact Evaluations</h3>
                </div>
                
                <div class="course-instructors">
                    <h4>Tutorial Instructor</h4>
                    <div class="instructor-avatars">
                        <div class="instructor">
                            <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/seth_goodman.png" alt="Dr. Seth Goodman" class="instructor-avatar">
                            <div class="instructor-info">
                                <h5>Dr. Seth Goodman</h5>
                                <p>Research Scientist, Geospatial Impact Evaluations</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="inline-partnership">
                    <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/wm_logo_white.png" alt="William & Mary Logo">
                    <p>Developed by the AidData Research Lab at William & Mary</p>
                </div>

                <div class="info-description">
                    <h4>About this Tutorial</h4>
                    <p>Discover AidData's pioneering geospatial impact evaluation (GIE) methodology that enables rigorous evaluation of development interventions using satellite observations and spatial analysis. Learn how GIEs can measure intended and unintended impacts at a fraction of the time and cost of traditional randomized controlled trials.</p>
                    
                    <h4>What You'll Learn</h4>
                    <ul class="learning-objectives">
                        <li>Understanding the fundamentals of geospatial impact evaluation methodology</li>
                        <li>How to leverage satellite data and spatial analysis for impact measurement</li>
                        <li>Techniques for establishing reliable counterfactuals using geographic data</li>
                        <li>Methods for measuring development outcomes remotely and retrospectively</li>
                        <li>Best practices for implementing GIEs in various development contexts</li>
                        <li>Real-world case studies demonstrating GIE applications in agriculture, health, and infrastructure</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Info Modal for Interview 1: Inside the Belt & Road Reboot - HIDDEN -->
    <?php /* <div class="info-modal" id="beltRoadRebootInfo">
        <div class="info-container">
            <button class="close-info" aria-label="Close">&times;</button>
            <div class="info-content">
                <div class="title-section">
                    <div style="width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                        <svg viewBox="0 0 24 24" width="35" height="35" fill="none" stroke="#115740" stroke-width="2">
                            <path d="M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z"></path>
                            <path d="M19 10v2a7 7 0 0 1-14 0v-2"></path>
                            <line x1="12" y1="19" x2="12" y2="23"></line>
                            <line x1="8" y1="23" x2="16" y2="23"></line>
                        </svg>
                    </div>
                    <h3>Inside the Belt & Road Reboot</h3>
                </div>

                <div class="inline-partnership">
                    <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/wm_logo_white.png" alt="William & Mary Logo">
                    <p>Developed by the AidData Research Lab at William & Mary</p>
                </div>

                <div class="info-description">
                    <h4>About this Interview</h4>
                    <p>Explore the major shifts in China's Belt and Road Initiative (BRI), examining how this flagship infrastructure program has evolved in response to global economic changes, geopolitical pressures, and debt sustainability concerns. This interview provides expert insights into the transformation of one of the world's most ambitious development finance initiatives.</p>
                    
                    <h4>Topics Covered</h4>
                    <ul class="learning-objectives">
                        <li>Evolution of the Belt and Road Initiative since its inception</li>
                        <li>Changes in China's financing approaches and project selection</li>
                        <li>Impact of debt sustainability concerns on BRI projects</li>
                        <li>Shifts in regional focus and sectoral priorities</li>
                        <li>Future outlook for Chinese infrastructure financing</li>
                    </ul>
                </div>
            </div>
        </div>
    </div> */ ?>

    <!-- Info Modal for Interview 2: Seeing Development from Above - HIDDEN -->
    <?php /* <div class="info-modal" id="seeingDevelopmentInfo">
        <div class="info-container">
            <button class="close-info" aria-label="Close">&times;</button>
            <div class="info-content">
                <div class="title-section">
                    <div style="width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                        <svg viewBox="0 0 24 24" width="35" height="35" fill="none" stroke="#115740" stroke-width="2">
                            <path d="M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z"></path>
                            <path d="M19 10v2a7 7 0 0 1-14 0v-2"></path>
                            <line x1="12" y1="19" x2="12" y2="23"></line>
                            <line x1="8" y1="23" x2="16" y2="23"></line>
                        </svg>
                    </div>
                    <h3>Seeing Development from Above</h3>
                </div>

                <div class="inline-partnership">
                    <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/wm_logo_white.png" alt="William & Mary Logo">
                    <p>Developed by the AidData Research Lab at William & Mary</p>
                </div>

                <div class="info-description">
                    <h4>About this Interview</h4>
                    <p>Discover how geospatial data and satellite technology are revolutionizing our understanding of what works in international development. This interview explores cutting-edge methodologies that use remote sensing and spatial analysis to measure development outcomes, evaluate interventions, and generate evidence at unprecedented scale and speed.</p>
                    
                    <h4>Topics Covered</h4>
                    <ul class="learning-objectives">
                        <li>Introduction to geospatial data applications in development</li>
                        <li>How satellite imagery can measure development outcomes</li>
                        <li>Case studies of geospatial impact evaluations</li>
                        <li>Advantages and limitations of remote sensing approaches</li>
                        <li>Future directions in geospatial development research</li>
                    </ul>
                </div>
            </div>
        </div>
    </div> */ ?>

    <!-- Info Modal for Interview 3: Decoding China's Debt Contracts - HIDDEN -->
    <?php /* <div class="info-modal" id="decodingChinaDebtInfo">
        <div class="info-container">
            <button class="close-info" aria-label="Close">&times;</button>
            <div class="info-content">
                <div class="title-section">
                    <div style="width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                        <svg viewBox="0 0 24 24" width="35" height="35" fill="none" stroke="#115740" stroke-width="2">
                            <path d="M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z"></path>
                            <path d="M19 10v2a7 7 0 0 1-14 0v-2"></path>
                            <line x1="12" y1="19" x2="12" y2="23"></line>
                            <line x1="8" y1="23" x2="16" y2="23"></line>
                        </svg>
                    </div>
                    <h3>Decoding China's Debt Contracts</h3>
                </div>

                <div class="inline-partnership">
                    <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/wm_logo_white.png" alt="William & Mary Logo">
                    <p>Developed by the AidData Research Lab at William & Mary</p>
                </div>

                <div class="info-description">
                    <h4>About this Interview</h4>
                    <p>Gain insider knowledge on the unique mechanics of Chinese international lending through an in-depth examination of loan contracts and financing terms. This interview unveils the distinctive features of Chinese development finance, including collateral arrangements, confidentiality clauses, and repayment structures that set it apart from traditional multilateral lending.</p>
                    
                    <h4>Topics Covered</h4>
                    <ul class="learning-objectives">
                        <li>Unique features of Chinese loan contracts</li>
                        <li>Collateralization practices in Chinese lending</li>
                        <li>Confidentiality and non-disclosure provisions</li>
                        <li>Comparison with traditional multilateral lending</li>
                        <li>Implications for debt sustainability and sovereignty</li>
                        <li>Recent changes in Chinese lending practices</li>
                    </ul>
                </div>
            </div>
        </div>
    </div> */ ?>

    <!-- Info Modal for Interview 4: Harboring Global Ambitions (Interview) -->
    <div class="info-modal" id="harboringGlobalAmbitionsInterviewInfo">
        <div class="info-container">
            <button class="close-info" aria-label="Close">&times;</button>
            <div class="info-content">
                <div class="title-section">
                    <div style="width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                        <svg viewBox="0 0 24 24" width="35" height="35" fill="none" stroke="#115740" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polygon points="23 7 16 12 23 17 23 7"></polygon>
                            <rect x="1" y="5" width="15" height="14" rx="2" ry="2"></rect>
                        </svg>
                    </div>
                    <h3>Harboring Global Ambitions</h3>
                </div>
                
                <p style="font-size: 1rem; margin: -0.5rem 0 1rem 0; color: #666; text-align: center;">A conversation with Alex Wooley, AidData's Director of Partnerships and Communications</p>

                <div class="inline-partnership">
                    <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/wm_logo_white.png" alt="William & Mary Logo">
                    <p>Developed by the AidData Research Lab at William & Mary</p>
                </div>

                <div class="info-description">
                    <h4>About this Interview</h4>
                    <p>Explore the intersection of development finance and national security through an analysis of Chinese port investments worldwide. This interview examines how development finance data can reveal strategic patterns and predict potential military expansion, offering critical insights for policymakers and analysts working at the nexus of economics and security.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Auth Modal -->
    <div class="auth-modal" id="authModal" role="dialog" aria-hidden="true" aria-labelledby="authModalTitle">
        <div class="auth-container">
            <button class="close-auth" aria-label="Close">×</button>
            <div class="auth-header">
                <h3 id="authModalTitle">Start Your Learning Journey</h3>
                <p>Please log in or create an account to access this course</p>
            </div>
            <div class="auth-options">
                <button class="auth-button login-button">Log In</button>
                <button class="auth-button signup-button">Sign Up</button>
            </div>
        </div>
    </div>

    <!-- Sign Up Modal -->
    <div class="signup-modal" id="signupModal" role="dialog" aria-hidden="true" aria-labelledby="signupModalTitle">
        <div class="signup-container">
            <button class="close-signup" aria-label="Close">×</button>
            <div class="signup-header">
                <h3 id="signupModalTitle">Create Your Account</h3>
                <p>Join AidData Training Hub to access all courses and track your progress</p>
            </div>
            <form class="signup-form" id="signupForm">
                <div class="form-group">
                    <label for="fullName">Full Name</label>
                    <input type="text" id="fullName" name="fullName" required aria-required="true">
                </div>
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" required aria-required="true">
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required aria-required="true" 
                           aria-describedby="passwordRequirements">
                    <p id="passwordRequirements" class="password-requirements">Password must be at least 8 characters long and include a number and special character</p>
                </div>
                <div class="form-group">
                    <label for="organization">Organization (Optional)</label>
                    <input type="text" id="organization" name="organization" aria-required="false">
                </div>
                <div class="checkbox-group">
                    <input type="checkbox" id="newsletter" name="newsletter">
                    <label for="newsletter">Subscribe to the AidData newsletter to receive updates about new courses, research insights, and development finance news</label>
                </div>
                <div class="form-message" style="display: none; margin: 10px 0; padding: 10px; border-radius: 4px; color: #fff; background-color: #d32f2f;"></div>
                <div class="form-footer">
                    <button type="submit" class="create-account-button">Create Account</button>
                    <p>Already have an account? <a href="#" class="login-link">Log in</a></p>
                </div>
            </form>
        </div>
    </div>

    <!-- Login Modal -->
    <div class="login-modal" id="loginModal" role="dialog" aria-hidden="true" aria-labelledby="loginModalTitle">
        <div class="login-container">
            <button class="close-login" aria-label="Close">&times;</button>
            <div class="login-header">
                <h3 id="loginModalTitle">Welcome Back</h3>
                <p>Please log in to continue</p>
            </div>
            <form class="login-form" id="loginForm">
                <div class="form-group">
                    <label for="loginEmail">Email</label>
                    <input type="email" id="loginEmail" name="email" required aria-required="true">
                </div>
                <div class="form-group">
                    <label for="loginPassword">Password</label>
                    <input type="password" id="loginPassword" name="password" required aria-required="true">
                </div>
                <div class="forgot-password">
                    <a href="#" class="forgot-password-link">Forgot Password?</a>
                </div>
                <div class="form-message" style="display: none; margin: 10px 0; padding: 10px; border-radius: 4px; color: #fff; background-color: #d32f2f;"></div>
                <button type="submit" class="login-button-submit">Log In</button>
                <div class="form-footer">
                    <p>Need an account? <a href="#" class="signup-link">Sign up</a></p>
                </div>
            </form>
        </div>
    </div>

    <!-- Forgot Password Modal -->
    <div class="forgot-password-modal" id="forgotPasswordModal" role="dialog" aria-hidden="true" aria-labelledby="forgotPasswordModalTitle">
        <div class="forgot-password-container">
            <button class="close-forgot-password" aria-label="Close">&times;</button>
            <div class="forgot-password-header">
                <h3 id="forgotPasswordModalTitle">Reset Your Password</h3>
                <p>Enter your email address and we'll send you instructions to reset your password</p>
            </div>
            <form class="forgot-password-form" id="forgotPasswordForm">
                <div class="form-group">
                    <label for="resetEmail">Email Address</label>
                    <input type="email" id="resetEmail" name="email" required aria-required="true">
                </div>
                <div class="form-message" style="display: none; margin: 10px 0; padding: 10px; border-radius: 4px;"></div>
                <button type="submit" class="reset-password-button">Send Reset Link</button>
                <div class="form-footer">
                    <p>Remembered your password? <a href="#" class="login-link">Back to login</a></p>
                </div>
            </form>
        </div>
    </div>

    <!-- Video Modal -->
    <div class="video-modal" id="courseTrailer" role="dialog" aria-hidden="true" aria-labelledby="courseTrailerTitle">
        <button class="close-modal" aria-label="Close video">×</button>
        <h3 id="courseTrailerTitle" class="sr-only"></h3>
        <div class="video-container">
            <iframe 
                src="about:blank"
                allow="fullscreen; encrypted-media" 
                allowfullscreen 
                title=""
                style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;"
                frameborder="0">
            </iframe>
        </div>
    </div>

    <!-- Info Modal Template -->
    <div class="info-modal" id="courseInfo" role="dialog" aria-hidden="true" aria-labelledby="courseInfoTitle">
        <div class="info-container">
            <button class="close-info" aria-label="Close">&times;</button>
            <div class="info-content">
                <div class="title-section">
                    <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/certificate.png" alt="Tutorial Certificate" class="course-badge" style="width: 35px; height: 35px;">
                    <h3 id="courseInfoTitle">Course Information</h3>
                </div>
                <!-- Rest of info modal content will be dynamically populated -->
            </div>
        </div>
    </div>

    <!-- Notification Modal -->
    <div class="notification-modal" id="notificationModal">
        <div class="notification-container">
            <button class="close-notifications" aria-label="Close">&times;</button>
            <div class="notification-header">
                <h3>Notifications</h3>
            </div>
            <div class="notification-list">
                <div class="notification-item">
                    <div class="notification-content">
                        <p>Welcome to AidData Training Hub</p>
                        <span class="notification-time">Just now</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

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
            <button onclick="window.open('https://www.aiddata.org/newsletter', '_blank')" class="newsletter-button" aria-label="Get our Newsletter" style="display: block; background-color: #004E38; color: white; padding: 10px 18px; border-radius: 6px; border: none; cursor: pointer; margin-top: 20px; font-weight: 600; transition: all 0.3s ease; font-family: inherit; font-size: 13px; width: fit-content; letter-spacing: 0.3px; text-transform: uppercase;">
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
<!-- Loading Screen Control -->
<script>
// Hide loading screen smoothly when page is ready
(function() {
    function hideLoadingScreen() {
        const loadingScreen = document.querySelector('.loading-screen');
        if (loadingScreen) {
            // Add fade-out class
            loadingScreen.classList.add('fade-out');
            
            // Add loaded class to body to show main content
            document.body.classList.add('loaded');
            
            // Remove loading screen from DOM after transition
            setTimeout(function() {
                loadingScreen.remove();
            }, 500);
        }
    }
    
    // Hide when page is fully loaded
    if (document.readyState === 'complete') {
        hideLoadingScreen();
    } else {
        window.addEventListener('load', hideLoadingScreen);
    }
    
    // Fallback: hide after 3 seconds maximum
    setTimeout(hideLoadingScreen, 3000);
})();
</script>

<!-- Tutorial Enrollment Protection -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Check if user is logged in (passed from PHP)
    var isLoggedIn = <?php echo is_user_logged_in() ? 'true' : 'false'; ?>;
    
    // Function to show auth modal
    function showAuthModal(event, message) {
        event.preventDefault();
        event.stopPropagation();
        
        // Update modal message if provided
        if (message) {
            var authModal = document.getElementById('authModal');
            var authMessage = authModal.querySelector('.auth-header p');
            if (authMessage) {
                authMessage.textContent = message;
            }
        }
        
        // Show the auth modal
        var authModal = document.getElementById('authModal');
        if (authModal) {
            authModal.classList.add('active');
            authModal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
        }
    }
    
    // Protect tutorial enrollment buttons
    if (!isLoggedIn) {
        // Find all buttons and links that might be enrollment buttons
        var allButtons = document.querySelectorAll('a.btn, a.btn-primary, button.btn, button.btn-primary');
        allButtons.forEach(function(button) {
            var buttonText = button.textContent.trim().toLowerCase();
            if (buttonText.includes('start tutorial') || 
                buttonText.includes('enroll') || 
                buttonText.includes('begin tutorial') ||
                buttonText.includes('access tutorial')) {
                
                // Mark as requiring auth
                button.setAttribute('data-requires-auth', 'true');
                button.setAttribute('data-auth-action', 'enroll');
                
                // Add click handler
                button.addEventListener('click', function(e) {
                    showAuthModal(e, 'Please log in or create an account to enroll in this tutorial');
                    return false;
                });
            }
        });
        
        // Also protect any links with class "enroll-button" or similar
        var enrollButtons = document.querySelectorAll(
            '.enroll-button, ' +
            '.enrollment-button, ' +
            '[data-action="enroll"], ' +
            '[data-requires-enrollment="true"]'
        );
        
        enrollButtons.forEach(function(button) {
            button.addEventListener('click', function(e) {
                showAuthModal(e, 'Please log in or create an account to enroll in this tutorial');
                return false;
            });
        });
        
        console.log('Tutorial enrollment protection active - user must log in to enroll');
    } else {
        console.log('User is logged in - enrollment protection disabled');
    }
});
</script>

<?php get_footer(); ?>
