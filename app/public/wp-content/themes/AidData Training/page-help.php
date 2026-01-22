<?php
/**
 * Template Name: Help Page
 * The template for displaying the help page.
 *
 * @package WordPress
 * @subpackage Twenty_Twenty_Four
 */

get_header();
?>

<main id="main" class="wp-block-group site-main" style="margin-top: 0; padding-top: 0;">
    <div class="wp-block-group__inner-container">
        
        <style>
            .help-container {
                max-width: 1200px;
                margin: 0 auto;
                padding: 40px 20px;
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            }
            
            .help-header {
                text-align: center;
                margin-bottom: 50px;
                margin-top: 50px;
            }
            
            .help-header h1 {
                color: #026447;
                font-size: 2.5rem;
                margin-bottom: 20px;
            }
            
            .help-header p {
                font-size: 1.2rem;
                color: #666;
                max-width: 600px;
                margin: 0 auto;
            }
            
            .help-sections {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
                gap: 30px;
                margin-top: 50px;
            }
            
            .help-section {
                background: white;
                padding: 30px;
                border-radius: 12px;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
                border: 1px solid rgba(2, 100, 71, 0.1);
            }
            
            .help-section h3 {
                color: #026447;
                font-size: 1.5rem;
                margin-bottom: 20px;
                display: flex;
                align-items: center;
                gap: 10px;
            }
            
            .help-section ul {
                list-style: none;
                padding: 0;
            }
            
            .help-section li {
                margin: 12px 0;
                padding-left: 20px;
                position: relative;
            }
            
            .help-section li:before {
                content: "→";
                position: absolute;
                left: 0;
                color: #026447;
                font-weight: bold;
            }
            
            .help-section a {
                color: #026447;
                text-decoration: none;
                transition: color 0.3s ease;
            }
            
            .help-section a:hover {
                color: #014333;
                text-decoration: underline;
            }
            
            .contact-section {
                background: linear-gradient(135deg, #026447, #038559);
                color: white;
                text-align: center;
                margin-top: 50px;
            }
            
            .contact-section h3 {
                color: white;
            }
            
            .contact-section a {
                color: white;
                background: rgba(255, 255, 255, 0.2);
                padding: 12px 24px;
                border-radius: 6px;
                text-decoration: none;
                display: inline-block;
                margin-top: 20px;
                transition: background 0.3s ease;
            }
            
            .contact-section a:hover {
                background: rgba(255, 255, 255, 0.3);
            }
            
            .back-link {
                display: inline-block;
                margin-bottom: 30px;
                color: #026447;
                text-decoration: none;
                font-weight: 500;
                margin-top: 20px;
            }
            
            .back-link:hover {
                text-decoration: underline;
            }
            
            .icon {
                width: 24px;
                height: 24px;
                display: inline-block;
            }
        </style>
        
        <div class="help-container">
            <a href="<?php echo esc_url(home_url()); ?>" class="back-link">← Back to Training Hub</a>
            
            <div class="help-header">
                <h1>Help & Support</h1>
                <p>Find answers to common questions and get the support you need to make the most of your AidData training experience.</p>
            </div>
            
            <div class="help-sections">
                <div class="help-section">
                    <h3>
                        <span class="icon">📚</span>
                        Getting Started
                    </h3>
                    <ul>
                        <li><a href="#account">Creating your account</a></li>
                        <li><a href="#enrollment">How to enroll in courses</a></li>
                        <li><a href="#navigation">Navigating the platform</a></li>
                        <li><a href="#progress">Tracking your progress</a></li>
                    </ul>
                </div>
                
                <div class="help-section">
                    <h3>
                        <span class="icon">🎓</span>
                        Course Information
                    </h3>
                    <ul>
                        <li><a href="#requirements">Technical requirements</a></li>
                        <li><a href="#certificates">Digital badges and certificates</a></li>
                        <li><a href="#scholarships">Scholarship opportunities</a></li>
                        <li><a href="#deadlines">Assignment deadlines</a></li>
                    </ul>
                </div>
                
                <div class="help-section">
                    <h3>
                        <span class="icon">💰</span>
                        Payment & Pricing
                    </h3>
                    <ul>
                        <li><a href="#pricing">Course pricing information</a></li>
                        <li><a href="#payment">Payment methods accepted</a></li>
                        <li><a href="#refunds">Refund policy</a></li>
                        <li><a href="#financial-aid">Financial assistance</a></li>
                    </ul>
                </div>
                
                <div class="help-section">
                    <h3>
                        <span class="icon">🔧</span>
                        Technical Support
                    </h3>
                    <ul>
                        <li><a href="#login">Login issues</a></li>
                        <li><a href="#video">Video playback problems</a></li>
                        <li><a href="#browser">Browser compatibility</a></li>
                        <li><a href="#mobile">Mobile device access</a></li>
                    </ul>
                </div>
                
                <div class="help-section">
                    <h3>
                        <span class="icon">📊</span>
                        Data & Tools
                    </h3>
                    <ul>
                        <li><a href="#datasets">Accessing course datasets</a></li>
                        <li><a href="#software">Required software</a></li>
                        <li><a href="#downloads">Downloading resources</a></li>
                        <li><a href="#citations">How to cite AidData</a></li>
                    </ul>
                </div>
                
                <div class="help-section">
                    <h3>
                        <span class="icon">🌍</span>
                        About AidData
                    </h3>
                    <ul>
                        <li><a href="https://www.aiddata.org/about" target="_blank">About our organization</a></li>
                        <li><a href="https://www.aiddata.org/research" target="_blank">Our research</a></li>
                        <li><a href="https://www.aiddata.org/data" target="_blank">AidData datasets</a></li>
                        <li><a href="https://www.aiddata.org/publications" target="_blank">Publications</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="help-section contact-section">
                <h3>
                    <span class="icon">📧</span>
                    Still Need Help?
                </h3>
                <p>Our training team is here to support your learning journey. Don't hesitate to reach out with any questions or concerns.</p>
                <a href="mailto:training@aiddata.org?subject=Training Hub Support Request">Contact Support Team</a>
                <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid rgba(255, 255, 255, 0.2);">
                    <p><strong>Sethu Nguna</strong><br>
                    Manager, Training & Instructional Design<br>
                    <a href="mailto:snguna@aiddata.org?subject=Training Support" style="background: none; padding: 0; margin-top: 5px;">snguna@aiddata.org</a></p>
                </div>
            </div>
        </div>
        
    </div> <!-- .wp-block-group__inner-container -->
</main>

<script>
    // Smooth scrolling for anchor links
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
</script>

<?php
get_footer();
?> 