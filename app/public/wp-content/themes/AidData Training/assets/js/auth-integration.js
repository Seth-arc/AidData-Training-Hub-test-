/**
 * WordPress Authentication Integration
 * Connects custom UI with WordPress authentication system
 */

document.addEventListener('DOMContentLoaded', function() {
    initAuthenticationSystem();
    updateAuthUI();
});

function resetFormMessage(form) {
    const messageEl = form ? form.querySelector('.form-message') : null;

    if (!messageEl) {
        return null;
    }

    messageEl.textContent = '';
    messageEl.style.display = 'none';
    messageEl.classList.remove('error', 'success');

    return messageEl;
}

function showFormMessage(messageEl, message, type) {
    if (!messageEl) {
        return;
    }

    messageEl.textContent = message;
    messageEl.style.display = 'block';
    messageEl.classList.remove('error', 'success');

    if (type) {
        messageEl.classList.add(type);
    }
}

function setSubmitButtonState(form, isBusy, busyLabel) {
    const submitButton = form ? form.querySelector('button[type="submit"]') : null;

    if (!submitButton) {
        return;
    }

    if (!submitButton.dataset.defaultLabel) {
        submitButton.dataset.defaultLabel = submitButton.textContent.trim();
    }

    submitButton.disabled = isBusy;
    submitButton.setAttribute('aria-busy', isBusy ? 'true' : 'false');
    submitButton.textContent = isBusy ? busyLabel : submitButton.dataset.defaultLabel;
}

function getAuthRedirectUrl() {
    const params = new URLSearchParams(window.location.search);
    return params.get('redirect_to') || params.get('redirect');
}

function handleAuthSuccess(form, responseData, options = {}) {
    const { messageType = 'success', closeModalId = null, reload = false, resetForm = false } = options;
    const messageEl = resetFormMessage(form);
    const successMessage = responseData.message || 'Success.';
    const redirectUrl = responseData.redirectUrl || getAuthRedirectUrl();

    showFormMessage(messageEl, successMessage, messageType);

    if (window.toastManager && typeof window.toastManager.show === 'function') {
        window.toastManager.show(successMessage, messageType);
    }

    if (resetForm && form) {
        form.reset();
    }

    if (closeModalId && window.modalManager && typeof window.modalManager.closeModal === 'function') {
        const modal = document.getElementById(closeModalId);
        if (modal) {
            setTimeout(() => window.modalManager.closeModal(modal), 500);
        }
    }

    if (redirectUrl) {
        setTimeout(() => {
            window.location.href = redirectUrl;
        }, 1200);
        return;
    }

    if (reload) {
        setTimeout(() => {
            window.location.reload();
        }, 1200);
    }
}

/**
 * Initialize all authentication-related event handlers
 */
function initAuthenticationSystem() {
    // Login form handler
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const messageEl = resetFormMessage(loginForm);
            setSubmitButtonState(loginForm, true, 'Logging In...');
            
            const data = new FormData();
            data.append('action', 'custom_ajax_login');
            data.append('username', document.getElementById('loginEmail').value);
            data.append('password', document.getElementById('loginPassword').value);
            data.append('security', auth_object.security);
            
            fetch(auth_object.ajaxurl, {
                method: 'POST',
                credentials: 'same-origin',
                body: data
            })
            .then(response => response.json())
            .then(response => {
                if(response.success) {
                    handleAuthSuccess(loginForm, response.data || {}, {
                        closeModalId: 'loginModal',
                        reload: true
                    });
                } else {
                    if (messageEl) {
                        showFormMessage(messageEl, response.data.message, 'error');
                    } else {
                        alert(response.data.message);
                    }
                }
            })
            .catch(error => {
                console.error('Login error:', error);
                if (messageEl) {
                    showFormMessage(messageEl, 'An error occurred. Please try again.', 'error');
                }
            })
            .finally(() => {
                setSubmitButtonState(loginForm, false, 'Logging In...');
            });
        });
    }
    
    // Registration form handler
    const signupForm = document.getElementById('signupForm');
    if (signupForm) {
        signupForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const messageEl = resetFormMessage(signupForm);
            setSubmitButtonState(signupForm, true, 'Creating Account...');
            
            const data = new FormData();
            data.append('action', 'custom_ajax_register');
            data.append('fullName', document.getElementById('fullName').value);
            data.append('email', document.getElementById('email').value);
            data.append('password', document.getElementById('password').value);
            
            const organization = document.getElementById('organization');
            if (organization) {
                data.append('organization', organization.value);
            }
            
            const newsletter = document.getElementById('newsletter');
            if (newsletter && newsletter.checked) {
                data.append('newsletter', 'on');
            }
            
            data.append('security', auth_object.security);
            
            fetch(auth_object.ajaxurl, {
                method: 'POST',
                credentials: 'same-origin',
                body: data
            })
            .then(response => response.json())
            .then(response => {
                if(response.success) {
                    handleAuthSuccess(signupForm, response.data || {}, {
                        closeModalId: 'signupModal',
                        reload: true,
                        resetForm: true
                    });
                } else {
                    if (messageEl) {
                        showFormMessage(messageEl, response.data.message, 'error');
                    } else {
                        alert(response.data.message);
                    }
                }
            })
            .catch(error => {
                console.error('Registration error:', error);
                if (messageEl) {
                    showFormMessage(messageEl, 'An error occurred. Please try again.', 'error');
                }
            })
            .finally(() => {
                setSubmitButtonState(signupForm, false, 'Creating Account...');
            });
        });
    }
    
    // Password reset form handler
    const forgotPasswordForm = document.getElementById('forgotPasswordForm');
    if (forgotPasswordForm) {
        forgotPasswordForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const messageEl = resetFormMessage(forgotPasswordForm);
            setSubmitButtonState(forgotPasswordForm, true, 'Sending Reset Link...');
            
            const data = new FormData();
            data.append('action', 'custom_ajax_reset_password');
            data.append('email', document.getElementById('resetEmail').value);
            data.append('security', auth_object.security);
            
            fetch(auth_object.ajaxurl, {
                method: 'POST',
                credentials: 'same-origin',
                body: data
            })
            .then(response => response.json())
            .then(response => {
                if(response.success) {
                    if (messageEl) {
                        showFormMessage(messageEl, response.data.message, 'success');
                    } else {
                        alert(response.data.message);
                    }
                } else {
                    if (messageEl) {
                        showFormMessage(messageEl, response.data.message, 'error');
                    } else {
                        alert(response.data.message);
                    }
                }
            })
            .catch(error => {
                console.error('Password reset error:', error);
                if (messageEl) {
                    showFormMessage(messageEl, 'An error occurred. Please try again.', 'error');
                }
            })
            .finally(() => {
                setSubmitButtonState(forgotPasswordForm, false, 'Sending Reset Link...');
            });
        });
    }
    
    // Logout handler - handles multiple logout buttons
    const logoutButtons = document.querySelectorAll('.logout-button');
    logoutButtons.forEach(logoutButton => {
        if (logoutButton && !logoutButton.hasAttribute('data-logout-initialized')) {
            logoutButton.addEventListener('click', function(e) {
                e.preventDefault();
                
                const data = new FormData();
                data.append('action', 'custom_ajax_logout');
                data.append('security', auth_object.security);
                
                fetch(auth_object.ajaxurl, {
                    method: 'POST',
                    credentials: 'same-origin',
                    body: data
                })
                .then(response => response.json())
                .then(response => {
                    if(response.success) {
                        // Redirect to frontpage
                        window.location.href = auth_object.home_url;
                    }
                })
                .catch(error => {
                    console.error('Logout error:', error);
                    // Fallback: still redirect to frontpage even if AJAX fails
                    window.location.href = auth_object.home_url;
                });
            });
            
            // Mark as initialized to prevent duplicate listeners
            logoutButton.setAttribute('data-logout-initialized', 'true');
        }
    });
    
    // Course start button handler
    document.querySelectorAll('.start-learning').forEach(button => {
        if (!button.getAttribute('data-auth-initialized')) {
            const originalClick = button.onclick;
            
            button.addEventListener('click', function(e) {
                e.preventDefault();
                
                const coursePath = this.getAttribute('data-course');
                if (!coursePath) return;
                
                fetch(auth_object.ajaxurl, {
                    method: 'POST',
                    credentials: 'same-origin',
                    body: new URLSearchParams({
                        'action': 'get_auth_status'
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.loggedIn) {
                        window.location.href = coursePath;
                    } else {
                        // Show auth modal
                        const authModal = document.getElementById('authModal');
                        if (authModal) {
                            authModal.style.display = 'flex';
                        }
                    }
                })
                .catch(error => {
                    console.error('Authentication status check error:', error);
                });
            });
            
            button.setAttribute('data-auth-initialized', 'true');
        }
    });
}

/**
 * Update UI based on authentication status
 */
function updateAuthUI() {
    const authOnly = document.querySelector('.auth-only');
    const guestOnly = document.querySelector('.guest-only');
    const adminLink = document.querySelector('.admin-link');
    
    if (!authOnly || !guestOnly) return;
    
    fetch(auth_object.ajaxurl, {
        method: 'POST',
        credentials: 'same-origin',
        body: new URLSearchParams({
            'action': 'get_auth_status'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.loggedIn) {
            authOnly.style.display = 'flex';
            guestOnly.style.display = 'none';
            
            // Update user info in dropdown
            const userName = document.querySelector('.user-name');
            const userEmail = document.querySelector('.user-email');
            
            if (userName) userName.textContent = data.userName;
            if (userEmail) userEmail.textContent = data.userEmail;
            
            // Show admin link if user is admin
            if (adminLink && data.isAdmin) {
                adminLink.style.display = 'block';
            }
            
            // Update notifications
            if (typeof updateNotificationBadge === 'function') {
                 const elements = {
                    button: document.getElementById('notificationsButton'),
                    modal: document.getElementById('notificationModal'),
                    closeButton: document.querySelector('.close-notifications'),
                    markAllReadButton: document.querySelector('.mark-all-read'),
                    items: document.querySelectorAll('.notification-item')
                };
                
                if (elements.button) {
                    updateNotificationBadge(elements);
                }
            }
        } else {
            authOnly.style.display = 'none';
            guestOnly.style.display = 'flex';
            
            if (adminLink) {
                adminLink.style.display = 'none';
            }
        }
    })
    .catch(error => {
        console.error('Error checking auth status:', error);
    });
} 
