/**
 * Sidebar Enhancement Script
 * This script handles the collapsible sidebar functionality
 */

(function() {
    // Run when DOM is fully loaded
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', enhanceSidebar);
    } else {
        enhanceSidebar();
    }

    // Run when page is fully loaded with all resources
    window.addEventListener('load', enhanceSidebar);

    // Run several times with delays to catch any dynamic content
    setTimeout(enhanceSidebar, 100);
    setTimeout(enhanceSidebar, 500);
    setTimeout(enhanceSidebar, 1000);

    // Main function to enhance the sidebar
    function enhanceSidebar() {
        // console.log('Enhancing sidebar...');

        // Handle sidebar collapse state persistence
        setupCollapseStatePersistence();

        // Add accent icons to sidebar items
        addAccentIcons();

        // Apply additional styling based on user preferences
        applyAdditionalStyling();

        // Add hover effects and active state enhancements
        enhanceSidebarInteractions();



        setActiveItemColor();
    }

    // Enhance sidebar interactions with hover and active states
    function enhanceSidebarInteractions() {
        const sidebarItems = document.querySelectorAll('.fi-sidebar-item a');
        sidebarItems.forEach(item => {
            item.addEventListener('mouseenter', () => {
                item.style.backgroundColor = '#5A7D4F';
                item.style.transition = 'background-color 0.2s ease';
            });
            item.addEventListener('mouseleave', () => {
                if (!item.classList.contains('fi-active')) {
                    item.style.backgroundColor = '';
                }
            });
        });
    }

    // Setup persistence of collapse state using localStorage
    function setupCollapseStatePersistence() {
        try {
            // Get sidebar collapse button
            const collapseButton = document.querySelector('.fi-sidebar-collapse-button');
            if (!collapseButton) return;

            // Check if sidebar was collapsed in previous session
            const sidebarCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';

            // Apply collapse state if needed
            if (sidebarCollapsed) {
                const sidebar = document.querySelector('.fi-sidebar');
                if (sidebar && !sidebar.classList.contains('fi-collapsed')) {
                    // Trigger click on collapse button to collapse sidebar
                    collapseButton.click();
                }
            }

            // Add event listener to save state on collapse button click
            collapseButton.addEventListener('click', () => {
                const sidebar = document.querySelector('.fi-sidebar');
                const isCollapsed = sidebar && sidebar.classList.contains('fi-collapsed');
                localStorage.setItem('sidebarCollapsed', isCollapsed);
            });

            // console.log('Sidebar collapse state persistence setup completed');
        } catch (e) {
            // console.error('Error setting up sidebar collapse state persistence:', e);
        }
    }

    // Add accent icons to sidebar navigation items
    function addAccentIcons() {
        try {
            // Find all sidebar navigation items
            const sidebarItems = document.querySelectorAll('.fi-sidebar-item a');

            sidebarItems.forEach(item => {
                console.log(item)
                // Check if item already has an accent indicator
                if (item.querySelector('.accent-indicator')) return;

                // Create accent element
                const accentIndicator = document.createElement('div');
                accentIndicator.classList.add('accent-indicator');
                accentIndicator.style.cssText = `
                    position: absolute;
                    left: 0;
                    top: 0;
                    bottom: 0;
                    width: 3px;
                    background-color: transparent;
                    transition: background-color 0.2s ease-in-out;
                `;

                // Add hover effect
                item.addEventListener('mouseover', () => {
                    if (!item.classList.contains('fi-active')) {
                        accentIndicator.style.backgroundColor = 'rgba(74, 103, 65, 0.5)';
                        item.style.backgroundColor = 'rgba(74, 103, 65, 0.1)';
                    }
                });

                item.addEventListener('mouseout', () => {
                    if (!item.classList.contains('fi-active')) {
                        accentIndicator.style.backgroundColor = 'transparent';
                        item.style.backgroundColor = 'transparent';
                    }
                });

                // Add accent indicator to item
                item.style.position = 'relative';
                item.prepend(accentIndicator);

                // Set active state if needed
            
            });

            // console.log('Added accent indicators to sidebar items');
        } catch (e) {
            // console.error('Error adding accent indicators to sidebar items:', e);
        }
    }

    // Apply additional styling based on user preferences
    function applyAdditionalStyling() {
        try {
            // Apply deeper Sage green theme to sidebar
            const sidebar = document.querySelector('.fi-sidebar');
            if (sidebar) {
                sidebar.style.cssText += `
                    background-color: #42713b !important;
                    border-color: #42713b !important;
                    color: #fff !important;
                `;
                // Ensure all text in sidebar is bright white with !important flag to override any theme settings
                const sidebarLinks = sidebar.querySelectorAll('a, .fi-sidebar-group-title, .fi-sidebar-brand, .fi-sidebar-item, .fi-sidebar-item-label, .fi-sidebar-nav, .fi-sidebar-header');
                sidebarLinks.forEach(link => {
                    link.style.cssText += 'color: #fff !important;';
                });

                // Add a style tag to ensure text remains white even with CSS class changes
                const styleTag = document.createElement('style');
                styleTag.textContent = `
                    .fi-sidebar a,
                    .fi-sidebar-item,
                    .fi-sidebar-item-label,
                    .fi-sidebar-group-title,
                    .fi-sidebar-brand,
                    .fi-sidebar-nav,
                    .fi-sidebar-header,
                    .fi-sidebar * {
                        color: #fff !important;
                    }
                `;
                document.head.appendChild(styleTag);
            }

            // console.log('Applied additional styling to sidebar');
        } catch (e) {
            // console.error('Error applying additional styling to sidebar:', e);
        }
    }









    function setActiveItemColor() {
    const activeItems = document.querySelectorAll('.fi-sidebar-item.fi-active');

    activeItems.forEach(item => {
        const link = item.querySelector('a');
        if (!link) return;

        const svg = link.querySelector('svg');
        const span = link.querySelector('span');

        if (svg) {
            svg.style.color = 'black';
            svg.style.stroke = 'white'; // for stroke-based icons
            svg.style.fill = 'black'; // in case it's fill-based
        }

         if (span) {
           
            span.setAttribute('style', (span.getAttribute('style') || '') + '; color: black !important;');
        }
    });
}








    // Watch for DOM changes to fix dynamically added elements
    try {
        const observer = new MutationObserver(mutations => {
            let needsEnhancement = false;

            mutations.forEach(mutation => {
                if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
                    needsEnhancement = true;
                }
            });

            if (needsEnhancement) {
                enhanceSidebar();
            }
        });

        observer.observe(document.body, {
            childList: true,
            subtree: true,
            attributes: true,
            attributeFilter: ['class', 'style']
        });

        // console.log('MutationObserver started for sidebar enhancements');
    } catch (e) {
        // console.error('Error setting up MutationObserver for sidebar:', e);
    }
})();
