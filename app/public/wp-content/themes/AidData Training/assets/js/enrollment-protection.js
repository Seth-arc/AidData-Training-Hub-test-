/**
 * Tutorial Enrollment Protection
 * Requires users to log in before enrolling in tutorials
 * 
 * @package AidData_LMS
 * @since 1.0.0
 */

(function() {
    'use strict';
    
    // Wait for DOM to be ready
    function ready(fn) {
        if (document.readyState !== 'loading') {
            fn();
        } else {
            document.addEventListener('DOMContentLoaded', fn);
        }
    }
    
    ready(function() {
        // Check if user is logged in (should be set by PHP)
        var isLoggedIn = typeof window.aidataUserLoggedIn !== 'undefined' ? window.aidataUserLoggedIn : false;
        
        // If already logged in, no need to protect
        if (isLoggedIn) {
            console.log('[Enrollment] User is logged in - enrollment available');
            return;
        }
        
        console.log('[Enrollment] User not logged in - protecting enrollment buttons');
        
        /**
         * Show authentication modal
         */
        function showAuthModal(event, message) {
            if (event) {
                event.preventDefault();
                event.stopPropagation();
            }
            
            // Try to find auth modal
            var authModal = document.getElementById('authModal');
            
            if (!authModal) {
                // Fallback: redirect to login page
                console.warn('[Enrollment] Auth modal not found, redirecting to login');
                window.location.href = '/login?redirect=' + encodeURIComponent(window.location.href);
                return;
            }
            
            // Update modal message if provided
            if (message) {
                var authMessage = authModal.querySelector('.auth-header p');
                if (authMessage) {
                    authMessage.textContent = message;
                }
            }
            
            // Show the modal
            authModal.classList.add('active');
            authModal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
            
            console.log('[Enrollment] Auth modal shown');
        }
        
        /**
         * Check if button/link is an enrollment action
         */
        function isEnrollmentButton(element) {
            var text = element.textContent.trim().toLowerCase();
            var href = element.getAttribute('href') || '';
            var dataAction = element.getAttribute('data-action') || '';
            
            // Check button text
            var enrollmentKeywords = [
                'start tutorial',
                'begin tutorial',
                'access tutorial',
                'enroll',
                'join course',
                'start learning',
                'get started'
            ];
            
            for (var i = 0; i < enrollmentKeywords.length; i++) {
                if (text.includes(enrollmentKeywords[i])) {
                    return true;
                }
            }
            
            // Check data attributes
            if (dataAction === 'enroll' || 
                element.getAttribute('data-requires-enrollment') === 'true' ||
                element.getAttribute('data-requires-auth') === 'true') {
                return true;
            }
            
            // Check classes
            if (element.classList.contains('enroll-button') || 
                element.classList.contains('enrollment-button') ||
                element.classList.contains('start-tutorial')) {
                return true;
            }
            
            return false;
        }
        
        /**
         * Protect an enrollment button
         */
        function protectButton(button) {
            // Mark as protected
            button.setAttribute('data-enrollment-protected', 'true');
            button.setAttribute('data-original-href', button.getAttribute('href') || '#');
            
            // Update href to prevent accidental navigation
            if (button.tagName === 'A') {
                button.setAttribute('href', '#enroll-protected');
            }
            
            // Add visual indicator (optional)
            button.style.cursor = 'pointer';
            
            // Add click handler
            button.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                showAuthModal(e, 'Please log in or create an account to enroll in this tutorial');
                return false;
            }, true); // Use capture phase to ensure it fires first
            
            console.log('[Enrollment] Protected button:', button.textContent.trim());
        }
        
        /**
         * Find and protect all enrollment buttons
         */
        function protectAllEnrollmentButtons() {
            // Find all potential buttons
            var selectors = [
                'a.btn',
                'a.btn-primary',
                'a.btn-secondary',
                'button.btn',
                'button.btn-primary',
                '.enroll-button',
                '.enrollment-button',
                '.start-tutorial',
                '[data-action="enroll"]',
                '[data-requires-enrollment="true"]',
                '[data-requires-auth="true"]'
            ];
            
            var elements = document.querySelectorAll(selectors.join(', '));
            var protectedCount = 0;
            
            elements.forEach(function(element) {
                // Skip if already protected
                if (element.getAttribute('data-enrollment-protected') === 'true') {
                    return;
                }
                
                // Check if this is an enrollment button
                if (isEnrollmentButton(element)) {
                    protectButton(element);
                    protectedCount++;
                }
            });
            
            console.log('[Enrollment] Protected ' + protectedCount + ' enrollment button(s)');
        }
        
        // Initial protection
        protectAllEnrollmentButtons();
        
        // Re-run protection after a delay (in case content loads dynamically)
        setTimeout(protectAllEnrollmentButtons, 1000);
        setTimeout(protectAllEnrollmentButtons, 2000);
        
        // Watch for dynamically added buttons (optional, for AJAX-loaded content)
        if (typeof MutationObserver !== 'undefined') {
            var observer = new MutationObserver(function(mutations) {
                var shouldCheck = false;
                mutations.forEach(function(mutation) {
                    if (mutation.addedNodes.length > 0) {
                        shouldCheck = true;
                    }
                });
                if (shouldCheck) {
                    protectAllEnrollmentButtons();
                }
            });
            
            observer.observe(document.body, {
                childList: true,
                subtree: true
            });
        }
        
        // Make showAuthModal available globally for other scripts
        window.aidataShowAuthModal = showAuthModal;
    });
})();

