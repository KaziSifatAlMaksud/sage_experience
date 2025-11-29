<!-- Load scripts for horizontal scrollable sidebar -->
<script src="{{ asset('js/horizontal-sidebar-fix.js') }}"></script>

<style>
    /* Additional inline styles for horizontal sidebar */
    .fi-topbar nav {
        overflow-x: auto !important;
        white-space: nowrap !important;
        display: flex !important;
        flex-wrap: nowrap !important;
    }

    /* Fix profile positioning */
    .fi-topbar {
        justify-content: space-between !important;
    }

    /* Fix the profile avatar position */
    .fi-user-menu {
        margin-left: auto !important;
    }
</style>
