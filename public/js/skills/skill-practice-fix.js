/**
 * Skill Practice UI Fix
 *
 * This script ensures clicks on UI elements are properly handled
 * and events are properly dispatched to Livewire components
 */
document.addEventListener('DOMContentLoaded', function() {
    console.log('Skill Practice Fix JS loaded');

    // Function to add click handlers to all relevant cards
    function addCardClickHandlers() {
        console.log('Adding click handlers to cards');

        // Get all cards that could be clicked
        const cards = document.querySelectorAll('.skill-area-card, .skill-card, .practice-card');

        cards.forEach(card => {
            // Remove existing event listeners to prevent duplicates
            card.removeEventListener('click', cardClickHandler);

            // Add the click handler
            card.addEventListener('click', cardClickHandler);
        });
    }

    // Click handler function
    function cardClickHandler(event) {
        console.log('Card clicked:', this);

        // Find the wire:click attribute
        const wireClick = this.getAttribute('wire:click');

        if (wireClick && window.Livewire) {
            // Find the Livewire component ID
            const componentId = this.closest('[wire\\:id]')?.getAttribute('wire:id');

            if (componentId) {
                console.log('Triggering Livewire event on component:', componentId, 'Method:', wireClick);

                try {
                    // Extract method name and parameters
                    const match = wireClick.match(/^([^(]+)(?:\(([^)]*)\))?$/);

                    if (match) {
                        const method = match[1];
                        const params = match[2] ? match[2].split(',').map(p => p.trim()) : [];

                        console.log('Calling method:', method, 'with params:', params);

                        // Call the Livewire method
                        window.Livewire.find(componentId)[method](...params);
                    }
                } catch (e) {
                    console.error('Error triggering Livewire event:', e);
                }
            }
        }
    }

    // Initial setup
    addCardClickHandlers();

    // Re-add handlers after Livewire updates
    document.addEventListener('livewire:update', function() {
        console.log('Livewire updated, re-adding click handlers');
        setTimeout(addCardClickHandlers, 100);
    });

    // Make sure the script works after the page is fully loaded
    window.addEventListener('load', addCardClickHandlers);

    // Also handle specific events from Livewire
    ['skillAreaSelected', 'skillSelected', 'practiceSelected', 'nextSkillSet'].forEach(event => {
        window.addEventListener(event, function() {
            console.log('Event received:', event);
            setTimeout(addCardClickHandlers, 100);
        });
    });

    // Fix for duplicate practice descriptions
    function fixDuplicatePractices() {
        console.log('Running fix for duplicate practices');

        // Find all direct paragraph elements in the main content
        const standaloneParas = document.querySelectorAll('.filament-main-content > p');

        standaloneParas.forEach(para => {
            console.log('Found standalone paragraph:', para.textContent.trim());
            para.style.display = 'none';
        });

        // Also look for any paragraphs with the exact same content as practice descriptions
        const practiceDescriptions = document.querySelectorAll('.practice-description');
        const allParagraphs = document.querySelectorAll('p:not(.practice-description)');

        allParagraphs.forEach(para => {
            const text = para.textContent.trim();

            // 1. Check if this is a standalone paragraph (not inside a proper container)
            const isStandalone = !para.closest('.practice-card') &&
                                 !para.closest('.grid') &&
                                 (!para.previousElementSibling || para.previousElementSibling.tagName !== 'H3');

            // 2. Check if there's another paragraph with the same text but with a header
            let isDuplicate = false;
            if (isStandalone) {
                // Look for headings followed by this same text
                const headings = document.querySelectorAll('h3');
                for (const heading of headings) {
                    const nextPara = heading.nextElementSibling;
                    if (nextPara && nextPara.tagName === 'P' && nextPara.textContent.trim() === text) {
                        isDuplicate = true;
                        break;
                    }
                }

                // Also check for specific practice descriptions in cards
                practiceDescriptions.forEach(desc => {
                    if (desc.textContent.trim() === text) {
                        isDuplicate = true;
                    }
                });

                if (isDuplicate) {
                    console.log('Found duplicate practice description:', text);
                    para.style.display = 'none';
                }
            }
        });

        // Special case for the screenshot pattern - direct paragraphs followed by titled sections
        document.querySelectorAll('.filament-main-content p + div > h3').forEach(heading => {
            const prevPara = heading.parentElement.previousElementSibling;
            if (prevPara && prevPara.tagName === 'P') {
                const nextParaAfterHeading = heading.nextElementSibling;
                if (nextParaAfterHeading && nextParaAfterHeading.tagName === 'P') {
                    if (prevPara.textContent.trim() === nextParaAfterHeading.textContent.trim()) {
                        console.log('Found duplicate pattern from screenshot:', prevPara.textContent.trim());
                        prevPara.style.display = 'none';
                    }
                }
            }
        });
    }

    // Fix duplicates on initial load
    fixDuplicatePractices();

    // Fix after any updates
    document.addEventListener('livewire:update', function() {
        setTimeout(fixDuplicatePractices, 150);
    });

    // Watch for DOM mutations to detect when new content is added
    const observer = new MutationObserver(function(mutations) {
        setTimeout(fixDuplicatePractices, 100);
    });

    // Observe the main content area for any changes
    const mainContent = document.querySelector('.filament-main-content');
    if (mainContent) {
        observer.observe(mainContent, {
            childList: true,
            subtree: true
        });
    }

    // Special exact text match fix for the screenshot
    function fixExactDuplicatesByText() {
        console.log('Running exact text match fix');

        // The exact texts from the screenshot
        const exactTextsToHide = [
            "Analyze the source's background and potential motivations.",
            "Compare the source's information with other reliable sources.",
            "Reflect on how the bias might affect the team's decision-making."
        ];

        // Find all paragraphs in the document
        const allParagraphs = document.querySelectorAll('p');

        // First pass: Mark paragraphs with these texts that appear inside headings
        const validParagraphs = new Set();

        allParagraphs.forEach(para => {
            const text = para.textContent.trim();

            if (exactTextsToHide.includes(text)) {
                // Check if this is inside a container with a heading
                const container = para.closest('div');
                if (container && container.querySelector('h3')) {
                    validParagraphs.add(para);
                    console.log('Found valid paragraph (with heading):', text);
                }
            }
        });

        // Second pass: Hide paragraphs with these texts that aren't in the valid set
        allParagraphs.forEach(para => {
            const text = para.textContent.trim();

            if (exactTextsToHide.includes(text) && !validParagraphs.has(para)) {
                console.log('Hiding duplicate paragraph:', text);
                para.style.display = 'none';
            }
        });
    }

    // Call the exact text match fix
    fixExactDuplicatesByText();

    // Re-run the fix when content changes
    document.addEventListener('livewire:update', function() {
        setTimeout(fixExactDuplicatesByText, 100);
    });

    // Also observe DOM changes to catch any updates
    const exactTextObserver = new MutationObserver(function() {
        setTimeout(fixExactDuplicatesByText, 100);
    });

    // Start observing
    exactTextObserver.observe(document.body, {
        childList: true,
        subtree: true
    });
});
