// Toast Manager Class
class ToastManager {
    constructor() {
        this.createToastContainer();
    }

    createToastContainer() {
        if (!document.querySelector('.toast-container')) {
            const container = document.createElement('div');
            container.className = 'toast-container';
            document.body.appendChild(container);
        }
    }

    show(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `toast ${type}`;
        
        const icon = this.getIconForType(type);
        
        toast.innerHTML = `
            ${icon}
            <div class="toast-content">
                <p>${message}</p>
            </div>
            <button class="toast-close">&times;</button>
        `;
        
        const container = document.querySelector('.toast-container');
        container.appendChild(toast);
        
        // Add click event for close button
        const closeButton = toast.querySelector('.toast-close');
        closeButton.addEventListener('click', () => this.removeToast(toast));
        
        // Auto-remove after 5 seconds
        setTimeout(() => this.removeToast(toast), 5000);
    }

    removeToast(toast) {
        if (toast.parentNode) {
            toast.classList.add('fade-out');
            setTimeout(() => toast.remove(), 300);
        }
    }

    getIconForType(type) {
        const icons = {
            success: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="toast-icon"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>',
            error: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="toast-icon"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>',
            warning: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="toast-icon"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>',
            info: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="toast-icon"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>'
        };
        return icons[type] || icons.info;
    }
}

// Video Modal Manager Class
class VideoModalManager {
    constructor() {
        this.modal = document.getElementById('courseTrailer');
        this.iframe = this.modal?.querySelector('iframe');
        this.setupEventListeners();
    }

    setupEventListeners() {
        if (!this.modal) return;

        // Handle modal close
        const closeBtn = this.modal.querySelector('.close-modal');
        if (closeBtn) {
            closeBtn.addEventListener('click', () => this.closeVideo());
        }

        // Handle trailer buttons
        document.querySelectorAll('.trailer-button').forEach(btn => {
            btn.addEventListener('click', (e) => {
                if (btn.hasAttribute('disabled')) return;
                
                const videoSrc = btn.getAttribute('data-video');
                if (videoSrc) {
                    this.openVideo(videoSrc);
                }
            });
        });

        // Handle modal backdrop click
        this.modal.addEventListener('click', (e) => {
            if (e.target === this.modal) {
                this.closeVideo();
            }
        });
        
        // Handle Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.modal.classList.contains('active')) {
                this.closeVideo();
            }
        });
    }

    openVideo(videoSrc) {
        if (!this.modal || !this.iframe) return;

        this.iframe.src = videoSrc;
        this.modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    closeVideo() {
        if (!this.modal || !this.iframe) return;

        // Immediately stop video playback
        this.iframe.src = 'about:blank';
        
        // Remove any content or cached data
        try {
            this.iframe.contentWindow.document.write('');
            this.iframe.contentWindow.document.close();
        } catch (e) {
            // Ignore cross-origin errors
        }
        
        this.modal.classList.remove('active');
        document.body.style.overflow = '';
    }
}

// Modal Manager Class
class ModalManager {
    constructor() {
        this.activeModals = new Set();
        this.initializeModals();
        this.setupEventListeners();
    }

    initializeModals() {
        // Auth Modal
        this.authModal = document.getElementById('authModal');
        this.loginModal = document.getElementById('loginModal');
        this.signupModal = document.getElementById('signupModal');
        this.forgotPasswordModal = document.getElementById('forgotPasswordModal');
        
        // Info Modals
        this.infoModals = document.querySelectorAll('.info-modal');
        
        // Video Modal
        this.videoModal = document.getElementById('courseTrailer');
        this.videoIframe = this.videoModal?.querySelector('iframe');

        // Store all modals for easier management
        this.allModals = [
            this.authModal,
            this.loginModal,
            this.signupModal,
            this.forgotPasswordModal,
            ...Array.from(this.infoModals),
            this.videoModal
        ].filter(Boolean);

        // Initialize info modals
        this.infoModals.forEach(modal => {
            modal.setAttribute('aria-hidden', 'true');
        });
    }

    setupEventListeners() {
        // Auth Modal Buttons
        this.bindButton('.guest-only .login-button', () => this.openModal(this.loginModal));
        this.bindButton('.guest-only .signup-button', () => this.openModal(this.signupModal));
        this.bindButton('.auth-modal .login-button', () => {
            this.closeModal(this.authModal);
            this.openModal(this.loginModal);
        });
        this.bindButton('.auth-modal .signup-button', () => {
            this.closeModal(this.authModal);
            this.openModal(this.signupModal);
        });

        // Close buttons - Using querySelectorAll to ensure we catch all close buttons
        document.querySelectorAll('.close-auth, .close-login, .close-signup, .close-info, .close-forgot-password').forEach(button => {
            button.addEventListener('click', () => {
                const modal = button.closest('.auth-modal, .login-modal, .signup-modal, .info-modal, .forgot-password-modal');
                if (modal) {
                    this.closeModal(modal);
                }
            });
        });

        // Switch between login and signup
        this.bindButton('.signup-modal .login-link', (e) => {
            e.preventDefault();
            this.closeModal(this.signupModal);
            this.openModal(this.loginModal);
        });
        this.bindButton('.login-modal .signup-link', (e) => {
            e.preventDefault();
            this.closeModal(this.loginModal);
            this.openModal(this.signupModal);
        });
        
        // Forgot Password Functionality
        this.forgotPasswordModal = document.getElementById('forgotPasswordModal');
        this.bindButton('.forgot-password-link', (e) => {
            e.preventDefault();
            this.closeModal(this.loginModal);
            this.openModal(this.forgotPasswordModal);
        });
        
        // Add a global event listener to handle "Back to login" link clicks
        document.addEventListener('click', (e) => {
            // Check if the clicked element is the "Back to login" link in the forgot password modal
            if (e.target.closest('#forgotPasswordForm .form-footer .login-link')) {
                e.preventDefault();
                this.closeModal(this.forgotPasswordModal);
                setTimeout(() => {
                    this.openModal(this.loginModal);
                }, 100); // Small delay to ensure proper transition
            }
        });
        
        // Handle forgot password form submission
        const forgotPasswordForm = document.getElementById('forgotPasswordForm');
        if (forgotPasswordForm) {
            forgotPasswordForm.addEventListener('submit', (e) => {
                e.preventDefault();
                const email = forgotPasswordForm.querySelector('input[name="email"]').value;
                
                if (email) {
                    // Here you would typically call your password reset API
                    // For demo purposes, we'll just show a success message
                    this.closeModal(this.forgotPasswordModal);
                    
                    // Show success toast message
                    const toastManager = new ToastManager();
                    toastManager.show('Password reset link sent to your email', 'success');
                    
                    // Clear the form
                    forgotPasswordForm.reset();
                }
            });
        }

        // Info Modal Buttons
        document.querySelectorAll('.secondary-button').forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                const courseCard = button.closest('.featured-course');
                if (!courseCard) return;
                
                const courseTitle = courseCard.querySelector('.preview-content h3')?.textContent.trim();
                if (!courseTitle) return;
                
                let modalId;
                switch (courseTitle) {
                    case 'China.AidData.org Dashboard Tutorial':
                        modalId = 'chinaDashboardInfo';
                        break;
                    case 'Global Chinese Development Finance':
                        modalId = 'chinaDashboardInfo';
                        break;
                    case 'How China Lends and Collateralizes':
                        modalId = 'howchinalendsInfo';
                        break;
                    case 'China\'s Global Diplomacy':
                        modalId = 'chinasGlobalDiplomacyInfo';
                        break;
                    case 'Balancing the Scales':
                        modalId = 'balancingScalesInfo';
                        break;
                    case 'Credit Shopper Tool':
                        modalId = 'creditShopperInfo';
                        break;
                    case 'Credit Evaluation Tool':
                        modalId = 'creditEvaluationToolInfo';
                        break;
                    case 'Expert Insights: Development Finance Leaders':
                        modalId = 'expertInsightsInfo';
                        break;
                    case 'Navigating Global Development Finance':
                        modalId = 'navigatingGlobalDevelopmentFinanceInfo';
                        break;
                    case 'Critical Data Analysis and Visualization':
                        modalId = 'criticalDataAnalysisInfo';
                        break;
                    case 'Securing Development Funding':
                        modalId = 'securingdevelopmentfundingInfo';
                        break;
                    case 'Navigating Debt Distress':
                        modalId = 'navigatingdebtdistressInfo';
                        break;
                    case 'Harboring Global Ambitions':
                        modalId = 'harboringGlobalAmbitionsInterviewInfo';
                        break;
                    case 'Listening to Leaders':
                        modalId = 'listeningToLeadersInfo';
                        break;
                    default:
                        modalId = courseTitle.toLowerCase().replace(/[^\w]+/g, '') + 'Info';
                }
                
                const modal = document.getElementById(modalId);
                if (modal) {
                    this.openModal(modal);
                } else {
                    // If specific info modal not found, use the general one
                    const courseInfo = document.getElementById('courseInfo');
                    if (courseInfo) {
                        // Populate the generic info modal with course details
                        this.populateInfoModal(courseInfo, courseCard);
                        this.openModal(courseInfo);
                    }
                }
            });
        });

        // Start learning buttons - Authentication check
        document.querySelectorAll('.start-learning').forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                const courseUrl = button.getAttribute('data-course');
                
                // Check if user is logged in (demo logic)
                const isLoggedIn = document.querySelector('.auth-only').style.display !== 'none';
                
                if (isLoggedIn) {
                    // If logged in, redirect to course
                    if (courseUrl) {
                        window.location.href = courseUrl;
                    }
                } else {
                    // If not logged in, show auth modal
                    this.openModal(this.authModal);
                }
            });
        });

        // Handle form submissions for login and signup
        const loginForm = document.getElementById('loginForm');
        const signupForm = document.getElementById('signupForm');

        if (loginForm) {
            loginForm.addEventListener('submit', (e) => {
                e.preventDefault();
                
                // For demo purposes, show success toast
                const toastManager = new ToastManager();
                toastManager.show('Login successful', 'success');
                
                // Close the modal
                this.closeModal(this.loginModal);
                
                // Clear the form
                loginForm.reset();
                
                // Update UI state to show logged in state
                this.updateAuthState(true);
                
                // Simulate successful auth
                document.querySelector('.guest-only').style.display = 'none';
                document.querySelector('.auth-only').style.display = 'flex';
                
                // Update user info (for demo)
                document.querySelector('.user-name').textContent = 'Demo User';
                document.querySelector('.user-email').textContent = loginForm.querySelector('input[name="email"]').value || 'demo@example.com';
            });
        }

        if (signupForm) {
            signupForm.addEventListener('submit', (e) => {
                e.preventDefault();
                
                // Validate password (simple check for demo)
                const password = signupForm.querySelector('input[name="password"]').value;
                if (password.length < 8) {
                    const toastManager = new ToastManager();
                    toastManager.show('Password must be at least 8 characters long', 'error');
                    return;
                }
                
                // For demo purposes, show success toast
                const toastManager = new ToastManager();
                toastManager.show('Account created successfully', 'success');
                
                // Close the modal
                this.closeModal(this.signupModal);
                
                // Update UI state to show logged in state
                this.updateAuthState(true);
                
                // Set user info (for demo)
                const fullName = signupForm.querySelector('input[name="fullName"]').value;
                const email = signupForm.querySelector('input[name="email"]').value;
                
                document.querySelector('.user-name').textContent = fullName || 'New User';
                document.querySelector('.user-email').textContent = email || 'user@example.com';
                
                // Clear the form
                signupForm.reset();
                
                // Simulate successful auth
                document.querySelector('.guest-only').style.display = 'none';
                document.querySelector('.auth-only').style.display = 'flex';
            });
        }

        // Logout functionality
        document.querySelectorAll('.logout-button').forEach(button => {
            button.addEventListener('click', () => {
                // Update UI state to show logged out state
                this.updateAuthState(false);
                
                // Show toast
                const toastManager = new ToastManager();
                toastManager.show('Logged out successfully', 'info');
                
                // Close any open dropdown
                document.querySelector('.profile-dropdown')?.classList.remove('active');
                
                // Update UI elements
                document.querySelector('.guest-only').style.display = 'flex';
                document.querySelector('.auth-only').style.display = 'none';
            });
        });

        // Global event listeners
        this.setupGlobalEventListeners();
    }

    // Helper method to populate the generic info modal
    populateInfoModal(modal, courseCard) {
        if (!modal || !courseCard) return;
        
        const courseTitle = courseCard.querySelector('.preview-content h3')?.textContent.trim();
        const courseDesc = courseCard.querySelector('.preview-content p')?.textContent.trim();
        const courseType = courseCard.getAttribute('data-type');
        const courseLevel = courseCard.querySelector('.stat:nth-child(2)')?.textContent.trim();
        const courseDuration = courseCard.querySelector('.stat:nth-child(1)')?.textContent.trim();
        
        // Set the title
        const titleElement = modal.querySelector('#courseInfoTitle');
        if (titleElement) titleElement.textContent = courseTitle || 'Course Information';
        
        // Create content if it doesn't exist yet
        const contentContainer = modal.querySelector('.info-content');
        if (!contentContainer) return;
        
        // Clear existing content except the title section
        const titleSection = contentContainer.querySelector('.title-section');
        contentContainer.innerHTML = '';
        contentContainer.appendChild(titleSection);
        
        // Add course description
        const descSection = document.createElement('div');
        descSection.className = 'info-description';
        descSection.innerHTML = `
            <h4>About this ${courseType || 'course'}</h4>
            <p>${courseDesc || 'No description available.'}</p>
            
            <h4>What You'll Learn</h4>
            <ul class="learning-objectives">
                <li>Understand key concepts and frameworks in ${courseTitle}</li>
                <li>Develop practical skills for analyzing and interpreting data</li>
                <li>Apply learned concepts to real-world development challenges</li>
                <li>Connect with a community of development professionals</li>
            </ul>
        `;
        
        contentContainer.appendChild(descSection);
    }

    // Helper method to update auth state UI
    updateAuthState(isLoggedIn) {
        const guestElements = document.querySelectorAll('.guest-only');
        const authElements = document.querySelectorAll('.auth-only');
        
        guestElements.forEach(el => {
            el.style.display = isLoggedIn ? 'none' : 'flex';
        });
        
        authElements.forEach(el => {
            el.style.display = isLoggedIn ? 'flex' : 'none';
        });
    }

    bindButton(selector, handler) {
        const buttons = document.querySelectorAll(selector);
        buttons.forEach(button => {
            if (button) button.addEventListener('click', handler);
        });
    }

    setupGlobalEventListeners() {
        // Close modals when clicking outside
        document.addEventListener('click', (e) => {
            if (this.activeModals.size === 0) return;
            
            const clickedModal = e.target.closest('.modal, .info-modal, .auth-modal, .login-modal, .signup-modal, .forgot-password-modal, .video-modal');
            if (!clickedModal && !e.target.closest('button')) {
                this.closeAllModals();
            }
        });

        // Close modals with Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.activeModals.size > 0) {
                this.closeAllModals();
            }
        });

        // Trap focus within modal
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Tab' && this.activeModals.size > 0) {
                this.handleTabKey(e);
            }
        });
    }

    openModal(modal) {
        if (!modal) return;
        
        // Close any other open modals
        this.closeAllModals();
        
        // Show the modal
        modal.classList.add('active');
        
        // Prevent body scroll
        document.body.style.overflow = 'hidden';
        
        // Add to active modals
        this.activeModals.add(modal);
        
        // Set ARIA attributes
        modal.setAttribute('aria-hidden', 'false');
        
        // Focus first focusable element
        const focusable = modal.querySelectorAll(
            'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
        );
        if (focusable.length) focusable[0].focus();
    }

    closeModal(modal) {
        if (!modal) return;
        
        // Hide the modal
        modal.classList.remove('active');
        
        // Check for and stop any videos in the modal
        const iframes = modal.querySelectorAll('iframe');
        iframes.forEach(iframe => {
            this.stopVideoPlayback(iframe);
        });
        
        // Remove from active modals
        this.activeModals.delete(modal);
        
        // Restore body scroll if no modals are active
        if (this.activeModals.size === 0) {
            document.body.style.overflow = '';
        }
        
        // Reset ARIA attributes
        modal.setAttribute('aria-hidden', 'true');
    }

    closeAllModals() {
        this.allModals.forEach(modal => this.closeModal(modal));
    }

    // Helper method to completely stop video playback in any iframe
    stopVideoPlayback(iframe) {
        if (!iframe) return;
        
        // First set source to blank
        iframe.src = 'about:blank';
        
        // Then try to clear content and cached data
        try {
            iframe.contentWindow.document.write('');
            iframe.contentWindow.document.close();
        } catch (e) {
            // Ignore potential cross-origin errors
        }
    }

    handleTabKey(e) {
        const modal = Array.from(this.activeModals)[0];
        if (!modal) return;

        const focusable = modal.querySelectorAll(
            'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
        );
        
        if (focusable.length === 0) return;
        
        const firstFocusable = focusable[0];
        const lastFocusable = focusable[focusable.length - 1];

        if (e.shiftKey) {
            if (document.activeElement === firstFocusable) {
                e.preventDefault();
                lastFocusable.focus();
            }
        } else {
            if (document.activeElement === lastFocusable) {
                e.preventDefault();
                firstFocusable.focus();
            }
        }
    }
}

// Password strength checker functionality
document.addEventListener('DOMContentLoaded', () => {
    const passwordInput = document.getElementById('password');
    const strengthMeter = document.querySelector('.strength-meter');
    const strengthBar = document.querySelector('.strength-bar');
    const strengthText = document.querySelector('.strength-text');
    
    if (passwordInput && strengthBar && strengthText) {
        passwordInput.addEventListener('input', function() {
            const value = this.value;
            let strength = 0;
            
            if (value.length >= 8) strength += 25;
            if (value.match(/[A-Z]/)) strength += 25;
            if (value.match(/[0-9]/)) strength += 25;
            if (value.match(/[^A-Za-z0-9]/)) strength += 25;
            
            // Update strength bar
            strengthBar.style.width = strength + '%';
            
            // Update color based on strength
            if (strength <= 25) {
                strengthBar.style.background = '#dc3545';
                strengthText.textContent = 'Weak';
            } else if (strength <= 50) {
                strengthBar.style.background = '#06a181';
                strengthText.textContent = 'Fair';
            } else if (strength <= 75) {
                strengthBar.style.background = '#28a745';
                strengthText.textContent = 'Good';
            } else {
                strengthBar.style.background = '#20c997';
                strengthText.textContent = 'Strong';
            }
        });
    }
    
    // Toggle password visibility
    const toggleButtons = document.querySelectorAll('.toggle-password');
    toggleButtons.forEach(button => {
        if (button) {
            button.addEventListener('click', function() {
                const input = this.previousElementSibling;
                const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                input.setAttribute('type', type);
                
                // Update icon
                const icon = this.querySelector('svg');
                if (icon) {
                    if (type === 'text') {
                        icon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>';
                    } else {
                        icon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>';
                    }
                }
            });
        }
    });
    
    // Initialize managers
    window.modalManager = new ModalManager();
    window.toastManager = new ToastManager();
    // VideoModalManager disabled - using custom video player from video-player.js instead
    // window.videoModalManager = new VideoModalManager();
});
