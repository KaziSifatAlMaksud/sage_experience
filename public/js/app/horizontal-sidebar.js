/**
 * Horizontal Sidebar Scrollability Fix
 * This script specifically targets the main topbar navigation
 */

(function() {
    // Run immediately when script loads
    fixHorizontalSidebars();

    // Also run when DOM is fully loaded
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', fixHorizontalSidebars);
    } else {
        fixHorizontalSidebars();
    }

    // Run when page is fully loaded with all resources
    window.addEventListener('load', fixHorizontalSidebars);

    // Run several times with delays to catch any dynamic content
    setTimeout(fixHorizontalSidebars, 100);
    setTimeout(fixHorizontalSidebars, 500);
    setTimeout(fixHorizontalSidebars, 1000);

    // Function to fix horizontal navigation
    function fixHorizontalSidebars() {
        console.log('Fixing horizontal navigation...');

        // Target only the main topbar navigation
        const selectors = [
            '.fi-topbar nav',
            '.fi-main-topbar nav',
            '.fi-topbar-content nav'
        ];

        // Apply to all matching selectors
        selectors.forEach(selector => {
            try {
                const elements = document.querySelectorAll(selector);
                console.log(`Found ${elements.length} elements matching ${selector}`);

                elements.forEach(element => {
                    // Prevent applying to elements that are children of already scrollable elements
                    if (!hasScrollableParent(element)) {
                        applyScrollingStyles(element);
                    }
                });
            } catch (e) {
                console.error(`Error applying styles to ${selector}:`, e);
            }
        });

        // Fix profile positioning
        try {
            const topbar = document.querySelector('.fi-topbar');
            if (topbar) {
                topbar.style.justifyContent = 'space-between !important';
                topbar.style.position ='fixed !important';
            }
            if (window.innerWidth <= 768) {
    const topbar = document.querySelector('.fi-topbar');
    if (topbar) {
        topbar.style.setProperty('position', 'fixed', 'important');
        topbar.style.setProperty('top', '0', 'important');
        topbar.style.setProperty('left', '0', 'important');
        topbar.style.setProperty('right', '0', 'important');
        topbar.style.setProperty('z-index', '9999', 'important');
        topbar.style.setProperty('background-color', '#fff', 'important'); // Optional: set background to prevent content overlap
    }
}

            const userMenu = document.querySelector('.fi-user-menu');
            if (userMenu) {
                userMenu.style.marginLeft = 'auto !important';
            }
        } catch (e) {
            console.error('Error fixing profile positioning:', e);
        }
    }

    // Check if element has a parent that already has scrolling applied
    function hasScrollableParent(element) {
        let parent = element.parentElement;
        while (parent) {
            if (parent.dataset.scrollFixed === 'true') {
                return true;
            }
            parent = parent.parentElement;
        }
        return false;
    }

    // Apply scrolling styles to an element
    function applyScrollingStyles(element) {
        if (!element || element.dataset.scrollFixed === 'true') return;

        try {
            // Apply inline styles with !important
            element.style.cssText += `
                overflow-x: auto !important;
                overflow-y: hidden !important;
                white-space: nowrap !important;
                display: flex !important;
                flex-wrap: nowrap !important;
                width: 100% !important;
                max-width: 100% !important;
                scrollbar-width: thin !important;
                -webkit-overflow-scrolling: touch !important;
                scroll-behavior: smooth !important;
            `;

            // Apply styles to direct children only
            if (element.children && element.children.length > 0) {
                Array.from(element.children).forEach(child => {
                    if (child.tagName === 'UL') {
                        child.style.cssText += `
                            display: flex !important;
                            flex-wrap: nowrap !important;
                            width: max-content !important;
                        `;

                        // Apply to list items within the menu
                        Array.from(child.children).forEach(li => {
                            li.style.cssText += `
                                flex-shrink: 0 !important;
                                white-space: nowrap !important;
                            `;
                        });
                    }
                });
            }

            // Mark as fixed to avoid duplicate processing
            element.dataset.scrollFixed = 'true';

            console.log('Fixed navigation element:', element);
        } catch (e) {
            console.error('Error applying styles to element:', e);
        }
    }

    // Watch for DOM changes to fix dynamically added elements
    try {
        const observer = new MutationObserver(mutations => {
            let needsFixing = false;

            mutations.forEach(mutation => {
                if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
                    needsFixing = true;
                }
            });

            if (needsFixing) {
                fixHorizontalSidebars();
            }
        });

        observer.observe(document.body, {
            childList: true,
            subtree: true,
            attributes: true,
            attributeFilter: ['class', 'style']
        });

        console.log('MutationObserver started');
    } catch (e) {
        console.error('Error setting up MutationObserver:', e);
    }
})();
