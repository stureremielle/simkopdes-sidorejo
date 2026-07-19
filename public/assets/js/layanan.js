/*
========================================================================
   SIMKOPDES LAYANAN PAGE JAVASCRIPT
   Description: Client-side category filtering, live search bar, 
                URL parameters pre-filter, and action button modals
========================================================================
*/

document.addEventListener('DOMContentLoaded', () => {
    // === 1. DOM Elements ===
    const filterTabs = document.querySelectorAll('.filter-tab');
    const searchInput = document.getElementById('searchInput');
    const productCards = document.querySelectorAll('.product-card');
    const emptyState = document.getElementById('emptyState');

    // Filter states
    let activeCategory = 'Semua';
    let searchQuery = '';

    // === 2. Core Filtering Function ===
    const applyFilters = () => {
        let visibleCount = 0;

        productCards.forEach(card => {
            const category = card.getAttribute('data-category');
            const name = card.getAttribute('data-name').toLowerCase();
            
            // Check category match
            const matchesCategory = (activeCategory === 'Semua' || category === activeCategory);
            
            // Check search query match
            const matchesSearch = name.includes(searchQuery);

            if (matchesCategory && matchesSearch) {
                card.classList.remove('hidden');
                visibleCount++;
            } else {
                card.classList.add('hidden');
            }
        });

        // Show/Hide empty state
        if (visibleCount === 0) {
            emptyState.classList.add('active');
        } else {
            emptyState.classList.remove('active');
        }
    };

    // === 3. Category Tab Events ===
    filterTabs.forEach(tab => {
        tab.addEventListener('click', () => {
            // Remove active state from all tabs
            filterTabs.forEach(t => t.classList.remove('active'));
            
            // Add active state to clicked tab
            tab.classList.add('active');
            
            // Update filter state and run
            activeCategory = tab.getAttribute('data-category');
            applyFilters();
        });
    });

    // === 4. Search Input Events ===
    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            searchQuery = e.target.value.toLowerCase().trim();
            applyFilters();
        });
    }

    // === 5. Read URL Query Parameters (For pre-filtering) ===
    const parseUrlParams = () => {
        const urlParams = new URLSearchParams(window.location.search);
        const categoryParam = urlParams.get('category');

        if (categoryParam) {
            // Find tab button that matches url parameter
            const targetTab = Array.from(filterTabs).find(
                tab => tab.getAttribute('data-category').toLowerCase() === categoryParam.toLowerCase()
            );

            if (targetTab) {
                // Simulate click on the target tab
                targetTab.click();
            }
        } else {
            // Run default (show all)
            applyFilters();
        }
    };

    // Initialize URL checks
    parseUrlParams();
});
