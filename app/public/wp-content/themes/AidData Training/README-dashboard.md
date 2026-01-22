# LifterLMS Dashboard Page Template

This document explains the custom LifterLMS dashboard page template that aligns with the course page design.

## Overview

The custom dashboard template (`page-myaccount.php`) provides a unified design experience that matches the course pages, featuring:
- Consistent header and footer structure
- Authentication styling integration
- Modern, responsive design
- Enhanced user experience

## Template Structure

### Files Created/Modified:
1. `page-myaccount.php` - Main dashboard page template
2. `assets/css/llms-dashboard.css` - Dashboard-specific styles
3. `functions.php` - Custom LifterLMS integration functions

## Features

### Header & Footer Alignment
- Uses the same `get_header()` and `get_footer()` structure as course templates
- Includes authentication-specific styles (`auth-styles.css`)
- Includes loading screen styles (`loading-screen.css`)

### Design Elements
- **Color Scheme**: Primary green (#026447) matching course pages
- **Typography**: Inter font family for consistency
- **Layout**: Clean, card-based design with proper spacing
- **Responsive**: Mobile-first design with tablet and desktop optimizations

### Enhanced Dashboard Components

#### Navigation
- Modern button-style navigation links
- Active state indicators
- Mobile-responsive layout

#### Course Cards
- Grid-based layout
- Hover effects
- Progress indicators
- Consistent with course archive styling

#### Tables & Forms
- Styled tables for grades, orders, notifications
- Enhanced form fields with focus states
- Improved accessibility

#### Progress Bars
- Animated progress indicators
- Visual feedback for course completion
- Gradient styling

## How to Use

### For a New Site:
1. Create a new page in WordPress admin
2. Set the page template to "LifterLMS My Account Dashboard"
3. Add the `[lifterlms_my_account]` shortcode to the page content
4. Configure this page as your LifterLMS dashboard page in LifterLMS settings

### For Existing Sites:
1. Go to your existing My Account page
2. Change the page template to "LifterLMS My Account Dashboard"
3. The shortcode should already be present

## Customization

### Styling
- Main styles are in `page-myaccount.php` (inline for immediate application)
- Additional styles in `assets/css/llms-dashboard.css`
- Uses CSS custom properties for easy color customization

### Functionality
- Custom wrapper functions in `functions.php`
- Hooks and filters for LifterLMS integration
- Authentication handling

## Accessibility Features

- Focus states for keyboard navigation
- High contrast mode support
- Reduced motion support for users with vestibular disorders
- Proper ARIA labels and semantic HTML structure

## Browser Support

- Modern browsers (Chrome 88+, Firefox 85+, Safari 14+, Edge 88+)
- Progressive enhancement for older browsers
- Responsive design for all screen sizes

## Notes

- The template automatically detects user login status
- Displays appropriate content for logged-in vs. logged-out users
- Maintains LifterLMS functionality while improving visual design
- Compatible with LifterLMS add-ons and extensions

## Troubleshooting

### Common Issues:
1. **Template not appearing**: Ensure you're editing a page (not post) and the template file is in the active theme directory
2. **Styling conflicts**: Check for theme conflicts and ensure CSS specificity is appropriate
3. **LifterLMS not loading**: Verify the shortcode is present and LifterLMS is active

### Support:
Refer to LifterLMS documentation for functionality questions and WordPress Codex for template development guidance. 