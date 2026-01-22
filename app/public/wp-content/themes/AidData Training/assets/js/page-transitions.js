/**
 * Page Transitions Enhancement
 * Provides smooth transitions between pages for better UX
 */

if (!window.aiddataPageTransitionsInitialized) {
    window.aiddataPageTransitionsInitialized = true;

    class PageTransitions {
        constructor() {
            this.isTransitioning = false;
            this.transitionDuration = 500;

            this.init();
        }

        init() {
            this.createTransitionOverlay();
            this.bindEvents();
            this.handlePageLoad();
        }

        getLogoUrl() {
            if (window.aiddataPageTransitions && window.aiddataPageTransitions.logoUrl) {
                return window.aiddataPageTransitions.logoUrl;
            }

            const logo = document.querySelector('.logo');
            if (logo && logo.getAttribute('src')) {
                return logo.getAttribute('src');
            }

            const fallback = document.querySelector('img[alt*="AidData"]');
            return fallback && fallback.getAttribute('src') ? fallback.getAttribute('src') : '';
        }

        createTransitionOverlay() {
            if (document.getElementById('pageTransitionOverlay')) {
                this.overlay = document.getElementById('pageTransitionOverlay');
                return;
            }

            const logoUrl = this.getLogoUrl();
            const logoMarkup = logoUrl
                ? `<img src="${logoUrl}" alt="AidData Logo" class="loading-logo">`
                : '';

            const overlayHTML = `
                <div class="page-transition-overlay" id="pageTransitionOverlay" aria-hidden="true">
                    <div class="loading-triangles-container">
                        <div class="loading-triangle triangle-move-1"></div>
                        <div class="loading-triangle triangle-move-2"></div>
                        <div class="loading-triangle triangle-move-3"></div>
                    </div>
                    <div class="loading-content">
                        ${logoMarkup}
                        <div class="loading-spinner">
                            <div class="spinner-ring"></div>
                        </div>
                        <p class="loading-text">Loading</p>
                    </div>
                </div>
            `;

            document.body.insertAdjacentHTML('beforeend', overlayHTML);
            this.overlay = document.getElementById('pageTransitionOverlay');
        }

        bindEvents() {
            document.addEventListener('click', (e) => {
                if (e.defaultPrevented || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) {
                    return;
                }

                const link = e.target.closest('a[href]');
                if (link && this.shouldTransition(link)) {
                    e.preventDefault();
                    this.transitionToPage(link.href);
                }
            });

            window.addEventListener('beforeunload', () => {
                if (!this.isTransitioning) {
                    this.showTransition();
                }
            });

            document.addEventListener('visibilitychange', () => {
                if (document.visibilityState === 'visible' && this.isTransitioning) {
                    this.hideTransition();
                }
            });

            window.addEventListener('pageshow', (event) => {
                if (event.persisted) {
                    this.hideTransition();
                }
            });
        }

        shouldTransition(link) {
            const href = link.getAttribute('href');

            if (!href ||
                href.startsWith('#') ||
                href.startsWith('mailto:') ||
                href.startsWith('tel:') ||
                href.startsWith('javascript:') ||
                link.target === '_blank' ||
                link.hasAttribute('download') ||
                link.hasAttribute('data-no-transition') ||
                link.closest('[data-no-transition]') ||
                link.closest('#profile-nav') ||
                link.closest('#profile-sidebar') ||
                link.closest('.lp-profile-nav-tabs') ||
                href.includes('wp-admin') ||
                href.includes('wp-login')) {
                return false;
            }

            const url = new URL(href, window.location.origin);
            if (url.origin !== window.location.origin) {
                return false;
            }

            if (url.pathname === window.location.pathname && url.hash) {
                return false;
            }

            if (link.classList.contains('button-continue-course') ||
                link.classList.contains('button-enroll-course') ||
                link.id === 'aiddata-continue-btn' ||
                href.includes('/lessons/') ||
                href.includes('/quiz/')) {
                return false;
            }

            return true;
        }

    showTransition() {
        if (this.isTransitioning || !this.overlay) return;

            this.isTransitioning = true;
            this.overlay.classList.remove('fade-out');
            this.overlay.classList.add('active');
            this.overlay.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
        }

        hideTransition() {
            if (!this.isTransitioning || !this.overlay) return;

            this.overlay.classList.add('fade-out');
            this.overlay.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';

            setTimeout(() => {
                this.overlay.classList.remove('active', 'fade-out');
                this.isTransitioning = false;
            }, this.transitionDuration);
        }

    transitionToPage(url) {
        this.setSkipLoadingScreenFlag();
        this.showTransition();

        setTimeout(() => {
            window.location.href = url;
        }, 200);
    }

    setSkipLoadingScreenFlag() {
        try {
            sessionStorage.setItem('aiddataSkipLoadingScreen', '1');
        } catch (error) {
            // Ignore storage errors in restricted environments.
        }
    }

        handlePageLoad() {
            if (this.shouldShowOnLoad()) {
                this.showTransition();
            }

            window.addEventListener('load', () => {
                this.hideTransition();
            });

            setTimeout(() => {
                this.hideTransition();
            }, 2000);
        }

        shouldShowOnLoad() {
            if (document.querySelector('.loading-screen')) {
                return false;
            }

            if (!document.referrer) {
                return false;
            }

            try {
                const referrerUrl = new URL(document.referrer);
                return referrerUrl.origin === window.location.origin;
            } catch (error) {
                return false;
            }
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        new PageTransitions();
    });
}
