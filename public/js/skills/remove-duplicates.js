/**
 * Aggressive duplicate remover for skill practice
 * This script will completely remove duplicate practice texts
 */
document.addEventListener('DOMContentLoaded', function() {
    console.log('Duplicate remover loaded');

    // The exact content we want to remove duplicates of
    const targetTexts = [
        "Analyze the source's background and potential motivations.",
        "Compare the source's information with other reliable sources.",
        "Reflect on how the bias might affect the team's decision-making."
    ];

    function removeDuplicates() {
        // First find top-level paragraphs
        const topLevelParagraphs = document.querySelectorAll('.filament-main-content > p');
        console.log(`Found ${topLevelParagraphs.length} top-level paragraphs`);

        // Remove them all - they shouldn't be there
        topLevelParagraphs.forEach(p => {
            console.log(`Removing top-level paragraph: ${p.textContent.trim()}`);
            p.parentNode.removeChild(p);
        });

        // Now find any standalone paragraphs in the document
        const allParagraphs = document.querySelectorAll('p');
        const validParagraphs = new Set();

        // First identify paragraphs that should be kept (those in containers with headings)
        allParagraphs.forEach(p => {
            const text = p.textContent.trim();

            // Only process paragraphs with our target texts
            if (targetTexts.includes(text)) {
                // Find if this paragraph is properly contained in a div with a heading
                const container = p.closest('div');
                if (container && container.querySelector('h3')) {
                    // This is a paragraph we want to keep
                    validParagraphs.add(p);
                    console.log(`Keeping paragraph in container: ${text}`);
                }
            }
        });

        // Now remove any paragraphs with our target texts that aren't in the valid set
        allParagraphs.forEach(p => {
            const text = p.textContent.trim();

            if (targetTexts.includes(text) && !validParagraphs.has(p)) {
                console.log(`Removing duplicate paragraph: ${text}`);
                if (p.parentNode) {
                    p.parentNode.removeChild(p);
                }
            }
        });
    }

    // Run the remover
    removeDuplicates();

    // Also run after a short delay to catch any lazy-loaded content
    setTimeout(removeDuplicates, 200);
    setTimeout(removeDuplicates, 500);
    setTimeout(removeDuplicates, 1000);

    // Watch for DOM mutations to remove duplicates when new content is added
    const observer = new MutationObserver(function(mutations) {
        removeDuplicates();
    });

    // Start observing
    observer.observe(document.body, {
        childList: true,
        subtree: true
    });

    // Also watch for Livewire updates
    if (window.Livewire) {
        document.addEventListener('livewire:load', function() {
            window.Livewire.hook('message.processed', function() {
                removeDuplicates();
            });
        });
    }

    // And after page interactions
    document.body.addEventListener('click', function() {
        setTimeout(removeDuplicates, 100);
    });
});
