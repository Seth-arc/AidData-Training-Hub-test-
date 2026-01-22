document.addEventListener('DOMContentLoaded', function() {
    // Scroll to top on page load
    window.onbeforeunload = function () {
        window.scrollTo(0, 0);
    }

    // Also ensure we're at the top when the content is loaded
    window.scrollTo(0, 0);

    // Initialize features that don't depend on full page load
    initScrollReveal();
    initMobileMenu();
    initNotifications();
    initParallaxEffects();
    initCourseInteractions();
    initSmoothScroll();
    initHeaderScroll();
    initFilterFunctionality();
    initDrawer();
    initMenuDropdown();


});





// Enhanced scroll reveal with stagger effect
function initScrollReveal() {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry, index) => {
            if (entry.isIntersecting) {
                // Add stagger delay based on index
                setTimeout(() => {
                    entry.target.classList.add('visible');
                }, index * 100);
            }
        });
    }, {
        threshold: 0.2,
        rootMargin: '50px'
    });

    document.querySelectorAll('.course-card, .path-card').forEach(el => {
        el.classList.add('scroll-reveal');
        observer.observe(el);
    });
}

// Parallax effects for welcome section
function initParallaxEffects() {
    const welcomeSection = document.querySelector('.welcome-section');
    if (!welcomeSection) return;

    window.addEventListener('scroll', () => {
        const scrolled = window.pageYOffset;
        const rate = scrolled * 0.5;
        
        welcomeSection.style.backgroundPosition = `center ${-rate}px`;
        
        // Fade out effect
        const opacity = 1 - (scrolled / 500);
        welcomeSection.style.opacity = Math.max(opacity, 0.1);
    });
}

// Enhanced course interactions
function initCourseInteractions() {
    document.querySelectorAll('.featured-course').forEach(card => {
        card.addEventListener('mouseenter', (e) => {
            const button = card.querySelector('.primary-button');
            if (button && !button.disabled) {
                button.classList.add('hover');
            }
        });

        card.addEventListener('mouseleave', (e) => {
            const button = card.querySelector('.primary-button');
            if (button) {
                button.classList.remove('hover');
            }
        });
    });
}

// Smooth scroll functionality
function initSmoothScroll() {
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
}

// Enhanced notification system with reduced complexity
function initNotifications() {
    const elements = {
        button: document.getElementById('notificationsButton'),
        modal: document.getElementById('notificationModal'),
        closeButton: document.querySelector('.close-notifications'),
        markAllReadButton: document.querySelector('.mark-all-read'),
        items: document.querySelectorAll('.notification-item')
    };

    // Verify that all required elements exist
    if (!elements.button || !elements.modal) {
        console.warn('Notification elements not found. Make sure notificationsButton and notificationModal exist in the DOM.');
        return;
    }

    const handlers = {
        toggleModal: (show) => {
            elements.modal.classList.toggle('active', show);
            document.body.style.overflow = show ? 'hidden' : '';
            if (show) updateNotificationBadge(elements);
        },

        markAllRead: () => {
            elements.items.forEach(item => item.classList.remove('unread'));
            updateNotificationBadge(elements);
        },

        handleItemClick: (item) => {
            item.classList.remove('unread');
            updateNotificationBadge(elements);
        }
    };

    // Event Listeners
    elements.button.addEventListener('click', (e) => {
        e.stopPropagation();
        handlers.toggleModal(true);
    });

    // Check if closeButton exists before adding event listener
    if (elements.closeButton) {
        elements.closeButton.addEventListener('click', () => handlers.toggleModal(false));
    } else {
        console.warn('Close notifications button not found.');
    }

    elements.modal.addEventListener('click', (e) => {
        if (e.target === elements.modal) handlers.toggleModal(false);
    });

    // Check if markAllReadButton exists before adding event listener
    if (elements.markAllReadButton) {
        elements.markAllReadButton.addEventListener('click', handlers.markAllRead);
    } else {
        console.warn('Mark all read button not found.');
    }

    // Check if notification items exist before adding event listeners
    if (elements.items.length > 0) {
        elements.items.forEach(item => {
            item.addEventListener('click', () => handlers.handleItemClick(item));
        });
    } else {
        console.warn('No notification items found.');
    }

    // Escape key handler
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && elements.modal.classList.contains('active')) {
            handlers.toggleModal(false);
        }
    });

    // Initial badge update
    updateNotificationBadge(elements);
}

// Separate pure function for badge updates
function updateNotificationBadge(elements) {
    if (!elements.button) return;
    
    const unreadCount = document.querySelectorAll('.notification-item.unread').length;
    const badge = elements.button.querySelector('.notification-badge');
    
    if (badge) {
        badge.style.display = unreadCount > 0 ? 'block' : 'none';
        // Add has-notifications class for animation effect
        if (unreadCount > 0) {
            badge.classList.add('has-notifications');
        } else {
            badge.classList.remove('has-notifications');
        }
    }
}

// Initialize mobile menu
function initMobileMenu() {
    const mobileMenuButton = document.querySelector('.mobile-menu-button');
    const nav = document.querySelector('.header-nav');
    
    if (mobileMenuButton && nav) {
        mobileMenuButton.addEventListener('click', () => {
            nav.classList.toggle('active');
            mobileMenuButton.classList.toggle('active');
        });
    }
}

// Header scroll behavior
function initHeaderScroll() {
    const header = document.querySelector('.lms-header');
    
    // Check if header exists before adding scroll listeners
    if (!header) {
        return;
    }
    
    let lastScroll = 0;
    let scrollTimeout;

    window.addEventListener('scroll', () => {
        clearTimeout(scrollTimeout);

        const currentScroll = window.pageYOffset;
        
        // Show/hide header based on scroll direction
        if (currentScroll > lastScroll && currentScroll > 100) {
            // Scrolling down & past threshold
            header.classList.add('header-scrolled');
            header.classList.remove('header-visible');
        } else {
            // Scrolling up or at top
            header.classList.remove('header-scrolled');
            header.classList.add('header-visible');
        }

        lastScroll = currentScroll;

        // Remove classes after scroll stops
        scrollTimeout = setTimeout(() => {
            if (currentScroll < 100) {
                header.classList.remove('header-scrolled', 'header-visible');
            }
        }, 150);
    });
}

// Enhanced filter functionality with FLIP animations and improved UX
function initFilterFunctionality() {
    const elements = {
        filterBtns: document.querySelectorAll('.filter-btn'),
        cards: document.querySelectorAll('.featured-course'),
        emptyState: document.querySelector('.empty-state'),
        featuredGrid: document.querySelector('.featured-grid'),
        filterSection: document.querySelector('.filter-section')
    };

    if (!elements.filterBtns || !elements.cards || !elements.emptyState || !elements.featuredGrid) return;

    // Create accessibility announcement element
    const announcement = document.createElement('div');
    announcement.className = 'filter-announcement';
    announcement.setAttribute('aria-live', 'polite');
    announcement.setAttribute('aria-atomic', 'true');
    elements.filterSection.appendChild(announcement);

    // Function to check if we're on mobile
    const isMobile = () => window.innerWidth <= 768;

    // Check if user prefers reduced motion
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    // Get card positions for FLIP animations
    const getCardPositions = () => {
        const positions = new Map();
        elements.cards.forEach(card => {
            const rect = card.getBoundingClientRect();
            positions.set(card, {
                x: rect.left,
                y: rect.top,
                width: rect.width,
                height: rect.height
            });
        });
        return positions;
    };

    // Animate cards using FLIP pattern
    const animateCards = async (beforePositions, afterPositions, visibleCards) => {
        if (prefersReducedMotion) return;

        const animations = [];
        
        visibleCards.forEach(card => {
            const before = beforePositions.get(card);
            const after = afterPositions.get(card);
            
            if (before && after) {
                const deltaX = before.x - after.x;
                const deltaY = before.y - after.y;
                
                if (deltaX !== 0 || deltaY !== 0) {
                    // Set initial position
                    card.style.transform = `translate(${deltaX}px, ${deltaY}px)`;
                    card.style.transition = 'none';
                    
                    // Force reflow
                    card.offsetHeight;
                    
                    // Animate to final position
                    card.style.transition = 'transform 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94)';
                    card.style.transform = 'translate(0, 0)';
                    
                    animations.push(new Promise(resolve => {
                        card.addEventListener('transitionend', resolve, { once: true });
                    }));
                }
            }
        });

        await Promise.all(animations);
        
        // Clean up
        visibleCards.forEach(card => {
            card.style.transform = '';
            card.style.transition = '';
        });
    };

    // Consistent grid layout update function
    const updateGridLayout = (filter, elements) => {
        // Define grid configurations for different filter types
        const gridConfigs = {
            'all': {
                columns: 'repeat(auto-fit, minmax(500px, 1fr))',
                specialOrder: false
            },
            'course': {
                columns: 'repeat(auto-fit, minmax(500px, 1fr))',
                specialOrder: true,
                targetType: 'course'
            },
            'simulation': {
                columns: 'repeat(auto-fit, minmax(500px, 1fr))',
                specialOrder: true,
                targetType: 'simulation'
            },
            'tutorial': {
                columns: isMobile() ? '1fr' : 'repeat(2, 1fr)',
                specialOrder: true,
                targetType: 'tutorial'
            },
            'interview': {
                columns: 'repeat(auto-fit, minmax(500px, 1fr))',
                specialOrder: true,
                targetType: 'interview'
            },
            'game': {
                columns: 'repeat(auto-fit, minmax(500px, 1fr))',
                specialOrder: true,
                targetType: 'game'
            },
            'tools': {
                columns: 'repeat(auto-fit, minmax(500px, 1fr))',
                specialOrder: true,
                targetType: 'tools'
            }
        };

        const config = gridConfigs[filter] || gridConfigs['all'];
        
        // Update grid columns
        elements.featuredGrid.style.gridTemplateColumns = config.columns;
        
        // Handle ordering to move filtered cards to the top
        if (config.specialOrder && config.targetType && filter !== 'all') {
            let visibleCount = 0;
            elements.cards.forEach(card => {
                if (card.dataset.type === config.targetType) {
                    card.style.order = visibleCount;
                    visibleCount++;
                } else {
                    card.style.order = '999'; // Push non-matching cards to the end
                }
            });
        } else {
            // Reset card ordering for 'all' filter
            elements.cards.forEach(card => {
                card.style.order = '';
            });
        }
    };

    // Enhanced filter function with smooth animations
    const filterCards = async (filter) => {
        const isVisible = card => filter === 'all' || card.dataset.type === filter;
        const cardsToHide = Array.from(elements.cards).filter(card => !isVisible(card) && !card.classList.contains('hidden'));
        const cardsToShow = Array.from(elements.cards).filter(card => isVisible(card) && card.classList.contains('hidden'));
        const visibleCards = Array.from(elements.cards).filter(isVisible);
        const hasVisibleCards = visibleCards.length > 0;

        // Add filtering state
        elements.featuredGrid.classList.add('filtering');
        elements.filterSection.classList.add('filter-loading');

        try {
            // Step 1: Get initial positions for FLIP animation
            const beforePositions = getCardPositions();

            // Step 2: Hide cards with staggered animation
            if (cardsToHide.length > 0) {
                cardsToHide.forEach((card, index) => {
                    setTimeout(() => {
                        card.classList.add('filtering-out');
                    }, index * 50);
                });

                // Wait for hide animations to complete
                await new Promise(resolve => setTimeout(resolve, cardsToHide.length * 50 + 300));

                // Actually hide the cards
                cardsToHide.forEach(card => {
                    card.classList.remove('filtering-out');
                    card.classList.add('hidden');
                });
            }

            // Step 3: Update grid layout based on filter with consistent behavior
            updateGridLayout(filter, elements);

            // Step 4: Show cards with staggered animation
            if (cardsToShow.length > 0) {
                // Prepare cards for animation
                cardsToShow.forEach(card => {
                    card.classList.remove('hidden');
                    card.classList.add('filtering-in');
                });

                // Force reflow
                elements.featuredGrid.offsetHeight;

                // Get positions after layout change
                const afterPositions = getCardPositions();

                // Animate existing cards to new positions
                await animateCards(beforePositions, afterPositions, visibleCards.filter(card => !cardsToShow.includes(card)));

                // Show new cards with stagger
                cardsToShow.forEach((card, index) => {
                    setTimeout(() => {
                        card.classList.remove('filtering-in');
                    }, index * 80);
                });

                // Wait for show animations to complete
                await new Promise(resolve => setTimeout(resolve, cardsToShow.length * 80 + 300));
            } else if (visibleCards.length > 0) {
                // Just animate existing cards to new positions
                const afterPositions = getCardPositions();
                await animateCards(beforePositions, afterPositions, visibleCards);
            }

            // Step 5: Update empty state
        elements.emptyState.classList.toggle('visible', !hasVisibleCards);

            // Step 6: Announce changes to screen readers
            const filterNames = {
                all: 'all content',
                course: 'courses',
                tutorial: 'tutorials',
                interview: 'interviews',
                simulation: 'simulations',
                tools: 'tools',
                game: 'games'
            };
            const filterName = filterNames[filter] || `${filter}s`;
            const resultCount = hasVisibleCards ? visibleCards.length : 0;
            announcement.textContent = `Showing ${resultCount} ${filterName}. ${resultCount === 0 ? 'No items found.' : ''}`;

        } finally {
            // Remove filtering states
            setTimeout(() => {
                elements.featuredGrid.classList.remove('filtering');
                elements.filterSection.classList.remove('filter-loading');
            }, 100);
        }
    };

    // Debounced filter function to prevent rapid successive calls
    let filterTimeout;
    const debouncedFilter = (filter) => {
        clearTimeout(filterTimeout);
        filterTimeout = setTimeout(() => filterCards(filter), 50);
    };

    // Add window resize event listener to update layout when screen size changes
    window.addEventListener('resize', () => {
        const activeFilter = Array.from(elements.filterBtns).find(btn => btn.classList.contains('active'));
        if (activeFilter) {
            updateGridLayout(activeFilter.dataset.filter, elements);
        }
    });

    // Add click handlers to filter buttons
    elements.filterBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            // Prevent multiple rapid clicks
            if (elements.featuredGrid.classList.contains('filtering')) return;

            // Update active state
            elements.filterBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            
            // Apply filter
            debouncedFilter(btn.dataset.filter);
        });
    });

    // Initialize with 'all' filter
    const allButton = Array.from(elements.filterBtns).find(btn => btn.dataset.filter === 'all');
    if (allButton && !Array.from(elements.filterBtns).some(btn => btn.classList.contains('active'))) {
        allButton.classList.add('active');
    }

    // Performance optimization: Preload will-change property
    elements.cards.forEach(card => {
        card.style.willChange = 'transform, opacity';
    });

    // Cleanup function for memory management
    const cleanup = () => {
        clearTimeout(filterTimeout);
        elements.cards.forEach(card => {
            card.style.willChange = 'auto';
        });
    };

    // Add cleanup on page unload
    window.addEventListener('beforeunload', cleanup);

    return cleanup; // Return cleanup function for manual cleanup if needed
}

// Improved drawer functionality with immutable state
const DrawerState = {
    isOpen: false,
    scrollPosition: 0
};

function toggleDrawer() {
    const infoDrawer = document.querySelector('.info-drawer');
    if (!infoDrawer) return;

    const newState = {
        ...DrawerState,
        isOpen: !DrawerState.isOpen,
        scrollPosition: window.scrollY
    };

    updateDrawerState(infoDrawer, newState);
}

function updateDrawerState(drawer, newState) {
    drawer.classList.toggle('open', newState.isOpen);
    document.body.style.overflow = newState.isOpen ? 'hidden' : '';
    
    if (newState.isOpen) {
        setTimeout(() => drawer.scrollTo(0, 0), 100);
    }

    Object.assign(DrawerState, newState);
}

// Initialize drawer functionality with improved state management
function initDrawer() {
    const elements = {
        learnMoreBtn: document.querySelector('.learn-more-btn'),
        infoDrawer: document.querySelector('.info-drawer'),
        closeDrawerBtn: document.querySelector('.close-drawer')
    };

    if (!elements.learnMoreBtn || !elements.infoDrawer || !elements.closeDrawerBtn) return;

    // Event listeners
    elements.learnMoreBtn.addEventListener('click', toggleDrawer);
    elements.closeDrawerBtn.addEventListener('click', toggleDrawer);

    elements.infoDrawer.addEventListener('click', (e) => {
        if (e.target === elements.infoDrawer) toggleDrawer();
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && DrawerState.isOpen) toggleDrawer();
    });
}

// Make drawer functionality globally available
window.toggleDrawer = toggleDrawer;

// Initialize menu dropdown
function initMenuDropdown() {
    const menuButton = document.querySelector('.menu-button');
    const dropdown = document.querySelector('.profile-dropdown');

    if (menuButton && dropdown) {
        // Toggle dropdown on button click
        menuButton.addEventListener('click', (e) => {
            e.stopPropagation();
            dropdown.classList.toggle('active');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!dropdown.contains(e.target) && !menuButton.contains(e.target)) {
                dropdown.classList.remove('active');
            }
        });

        // Handle menu item clicks
        const logoutButton = dropdown.querySelector('.logout-button');
        if (logoutButton) {
            logoutButton.addEventListener('click', function() {
                dropdown.classList.remove('active');
                // Handle logout functionality
            });
        }
    }
}
