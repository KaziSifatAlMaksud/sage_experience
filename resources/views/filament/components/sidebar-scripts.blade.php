<!-- Load scripts for sidebar customization -->
<script src="{{ asset('js/sidebar.js') }}"></script>

<style>
    /* Sage Green theme for sidebar - deeper green to match Sage logo */
    .fi-sidebar,
    .fi-sidebar.fi-sidebar-open,
    .fi-sidebar.fi-collapsed {
        background-color: #3A5531 !important;
        border-right: none !important;
    }

    /* Style for sidebar items (non-active) */
    .fi-sidebar-item a,
    .fi-sidebar-item a:not(.fi-active),
    .fi-sidebar-item a.dashboard-link {
        color: #374237 !important; /* Dark sage for non-active links */
        font-weight: 600 !important;
        padding: 0.75rem 1rem !important;
    }

    /* Style for Review Skill Practices link */
    .fi-sidebar-item a[title="Review Skill Practices"] {
        color: #ADD8E6 !important; /* Light Blue for better contrast */
    }

    /* Override Dashboard link to use sidebar color */
    .fi-sidebar-item a.dashboard-link {
        color: #3A5531 !important;
    }

    /* Active item styling (neutral) */
    .fi-sidebar-item a.fi-active,
    .fi-sidebar-item a.fi-active:hover {
        border-left: none !important;
        background-color: transparent !important;
        color: #374237 !important;
        padding-left: 1rem !important;
    }

    /* Override any default white background */

    /* Hover effect */
    .fi-sidebar-item a:hover:not(.fi-active) {
        background-color: #5A7D4F !important;
        color: white !important;
    }

    /* Group headers */
    .fi-sidebar-group-title {
        color: #FFFFFF !important;
        font-size: 0.75rem !important;
        text-transform: uppercase !important;
        letter-spacing: 0.05em !important;
        padding: 0.5rem 1rem !important;
    }

    /* Collapse button */
    .fi-sidebar-collapse-button {
        background-color: #121212 !important;
        color: #FFFFFF !important;
        border: none !important;
    }

    .fi-sidebar-collapse-button:hover {
        background-color: #1a1a1a !important;
        color: #FFFFFF !important;
    }

    /* Icons in sidebar */
    .fi-sidebar-item svg {
        color: #FFFFFF !important;
    }

    .fi-sidebar-item a.fi-active svg {
        color: #3b82f6 !important;
    }

    /* Ensure sidebar has clean separation */
    .fi-main {
        background-color: #e7f0e6 !important;
    }

    /* Brand text styling */
    .fi-sidebar-brand {
        color: #FFFFFF !important; /* Default to white */
    }

    /* Dark mode support for brand text */
    body.dark .fi-sidebar-brand {
        color: #FFFFFF !important; /* Keep white in dark mode */
    }

    /* Fix any spacing issues */
    .fi-sidebar-nav {
        padding-top: 1rem !important;
    }

    /* Neutralize all active sidebar backgrounds and text */
    .fi-sidebar .fi-sidebar-item-active,
    .fi-sidebar .fi-sidebar-item.active,
    .fi-sidebar-item.fi-active,
    .fi-sidebar-item a.fi-active,
    .fi-sidebar-item a.fi-active:hover {
        background-color: transparent !important;
        border-left: none !important;
        color: #374237 !important; /* Same as non-active links */
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Apply custom sidebar enhancements
        const applyCustomSidebar = () => {
            const sidebar = document.querySelector('.fi-sidebar');
            if (sidebar) {
                sidebar.style.backgroundColor = '#42713b';
            }

            const sidebarItems = document.querySelectorAll('.fi-sidebar-item a');
            sidebarItems.forEach(item => {
                item.style.color = 'white';
                // Enhance active item indicators
                if (item.classList.contains('fi-active')) {
                    item.style.borderLeft = '3px solid #42713b';
                    item.style.color = 'white';
                }
            });

            // Ensure sidebar collapse functionality is preserved
            const collapseButton = document.querySelector('.fi-sidebar-collapse-button');
            if (collapseButton) {
                // Store sidebar state in localStorage
                collapseButton.addEventListener('click', () => {
                    const isCollapsed = sidebar && sidebar.classList.contains('fi-collapsed');
                    localStorage.setItem('sidebarCollapsed', isCollapsed);
                });

                // Apply saved state
                const sidebarCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
                if (sidebarCollapsed) {
                    if (sidebar && !sidebar.classList.contains('fi-collapsed')) {
                        collapseButton.click();
                    }
                }
            }
        };

        // Apply immediately and set up observer for dynamic content
        applyCustomSidebar();

        // Reapply when content changes
        const observer = new MutationObserver(() => {
            applyCustomSidebar();
        });

        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    });
</script>
