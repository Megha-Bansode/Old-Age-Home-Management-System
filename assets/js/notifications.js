/**
 * SevaNest – Notifications JavaScript
 * File     : assets/js/notifications.js
 * Version  : 1.0
 * Author   : SevaNest Dev Team
 *
 * Features
 * --------
 * 1. Filter notifications by status (All vs. Unread).
 * 2. Dynamic filter badge count calculation.
 * 3. Client-side empty state toggle.
 * 4. Soft entrance transitions.
 */

'use strict';

document.addEventListener('DOMContentLoaded', function () {
    initNotificationsFilter();
});

function initNotificationsFilter() {
    const filterAllBtn = document.getElementById('nt-filter-all');
    const filterUnreadBtn = document.getElementById('nt-filter-unread');
    const feedContainer = document.getElementById('nt-feed-container');
    const emptyState = document.getElementById('nt-empty-state');
    
    if (!feedContainer || !emptyState) return;
    
    const bubbles = feedContainer.querySelectorAll('.nt-bubble');
    
    // ─── 1. Calculate & Update Badges Dynamic Counts ─────────────────────
    function updateFilterCounts() {
        const totalCount = bubbles.length;
        let unreadCount = 0;
        
        bubbles.forEach(function (bubble) {
            if (bubble.dataset.status === 'unread') {
                unreadCount++;
            }
        });
        
        const allBadge = document.getElementById('nt-count-all');
        const unreadBadge = document.getElementById('nt-count-unread');
        
        if (allBadge) allBadge.textContent = totalCount;
        if (unreadBadge) unreadBadge.textContent = unreadCount;
    }
    
    // ─── 2. Filter Handler ────────────────────────────────────────────────
    function filterFeed(filterType) {
        let visibleCount = 0;
        
        bubbles.forEach(function (bubble) {
            const status = bubble.dataset.status;
            
            if (filterType === 'all') {
                bubble.style.display = 'flex';
                visibleCount++;
            } else if (filterType === 'unread') {
                if (status === 'unread') {
                    bubble.style.display = 'flex';
                    visibleCount++;
                } else {
                    bubble.style.display = 'none';
                }
            }
        });
        
        // ─── 3. Toggle Empty State ────────────────────────────────────────
        if (visibleCount === 0) {
            feedContainer.style.display = 'none';
            emptyState.style.display = 'flex';
        } else {
            feedContainer.style.display = 'block';
            emptyState.style.display = 'none';
        }
    }
    
    // ─── 4. Event Listeners ──────────────────────────────────────────────
    if (filterAllBtn) {
        filterAllBtn.addEventListener('click', function (e) {
            e.preventDefault();
            filterAllBtn.classList.add('active');
            if (filterUnreadBtn) filterUnreadBtn.classList.remove('active');
            filterFeed('all');
        });
    }
    
    if (filterUnreadBtn) {
        filterUnreadBtn.addEventListener('click', function (e) {
            e.preventDefault();
            filterUnreadBtn.classList.add('active');
            if (filterAllBtn) filterAllBtn.classList.remove('active');
            filterFeed('unread');
        });
    }
    
    // Run initial counts & initial layout alignment
    updateFilterCounts();
    filterFeed('all');
}
