/**
 * SevaNest – Health Updates JavaScript
 * File     : assets/js/health-updates.js
 * Version  : 1.0
 * Author   : SevaNest Dev Team
 *
 * Features
 * --------
 * 1. Medication table – live search filter by name.
 * 2. Medication table – status filter (taken / pending / upcoming).
 * 3. Medical Reports table – live search filter by report name.
 * 4. Pagination button UI (visual only; wire to server for DB pagination).
 * 5. Bootstrap Tooltip initialisation.
 * 6. Vital row hover micro-interaction.
 */

'use strict';


/* ─────────────────────────────────────────────────────────────────────────
 * 1 & 2. Medication Tracker – search + status filter
 * ───────────────────────────────────────────────────────────────────────── */

function initMedicationFilter() {
    const searchInput = document.getElementById('hu-med-search');
    const statusFilter = document.getElementById('hu-med-filter');
    const tbody = document.getElementById('hu-med-tbody');

    if (!searchInput || !statusFilter || !tbody) return;

    function applyMedFilter() {
        const query      = searchInput.value.trim().toLowerCase();
        const statusVal  = statusFilter.value.toLowerCase();
        const rows       = tbody.querySelectorAll('tr');

        rows.forEach(function (row) {
            const nameCell   = row.querySelector('td:first-child');
            const rowStatus  = (row.dataset.status || '').toLowerCase();
            const nameText   = nameCell ? nameCell.textContent.trim().toLowerCase() : '';

            const matchQuery  = !query      || nameText.includes(query);
            const matchStatus = !statusVal  || rowStatus === statusVal;

            row.style.display = (matchQuery && matchStatus) ? '' : 'none';
        });
    }

    searchInput.addEventListener('input', applyMedFilter);
    statusFilter.addEventListener('change', applyMedFilter);
}


/* ─────────────────────────────────────────────────────────────────────────
 * 3. Medical Reports – live search filter
 * ───────────────────────────────────────────────────────────────────────── */

function initReportSearch() {
    const searchInput = document.getElementById('hu-report-search');
    const tbody       = document.getElementById('hu-reports-tbody');

    if (!searchInput || !tbody) return;

    searchInput.addEventListener('input', function () {
        const query = this.value.trim().toLowerCase();
        const rows  = tbody.querySelectorAll('tr');

        rows.forEach(function (row) {
            const nameCell = row.querySelector('td:first-child');
            const nameText = nameCell ? nameCell.textContent.trim().toLowerCase() : '';
            row.style.display = !query || nameText.includes(query) ? '' : 'none';
        });
    });
}


/* ─────────────────────────────────────────────────────────────────────────
 * 4. Pagination – visual active state
 *    In a DB-driven setup, page buttons should submit a GET param.
 * ───────────────────────────────────────────────────────────────────────── */

function initPagination() {
    const container = document.getElementById('hu-reports-pagination');
    if (!container) return;

    const pageButtons = container.querySelectorAll('.hu-page-btn[id^="hu-page-"]');
    const prevBtn     = document.getElementById('hu-page-prev');
    const nextBtn     = document.getElementById('hu-page-next');

    // Collect numbered buttons (not prev/next)
    const numberedBtns = Array.from(pageButtons).filter(function (btn) {
        return btn.id !== 'hu-page-prev' && btn.id !== 'hu-page-next';
    });

    let currentPage = 0; // 0-indexed

    function updatePageState(index) {
        numberedBtns.forEach(function (btn, i) {
            btn.classList.toggle('active', i === index);
            btn.setAttribute('aria-current', i === index ? 'page' : 'false');
        });

        if (prevBtn) prevBtn.disabled = (index === 0);
        if (nextBtn) nextBtn.disabled = (index === numberedBtns.length - 1);

        currentPage = index;
    }

    numberedBtns.forEach(function (btn, i) {
        btn.addEventListener('click', function () {
            updatePageState(i);
        });
    });

    if (prevBtn) {
        prevBtn.addEventListener('click', function () {
            if (currentPage > 0) updatePageState(currentPage - 1);
        });
    }

    if (nextBtn) {
        nextBtn.addEventListener('click', function () {
            if (currentPage < numberedBtns.length - 1) updatePageState(currentPage + 1);
        });
    }

    // Set initial state
    updatePageState(0);
}


/* ─────────────────────────────────────────────────────────────────────────
 * 5. Vital row hover micro-interaction
 * ───────────────────────────────────────────────────────────────────────── */

function initVitalHover() {
    document.querySelectorAll('.hu-vital-row').forEach(function (row) {
        row.addEventListener('mouseenter', function () {
            const icon = this.querySelector('.hu-vital-icon');
            if (icon) {
                icon.style.transform  = 'scale(1.12)';
                icon.style.transition = 'transform 160ms ease';
            }
        });
        row.addEventListener('mouseleave', function () {
            const icon = this.querySelector('.hu-vital-icon');
            if (icon) icon.style.transform = '';
        });
    });
}


/* ─────────────────────────────────────────────────────────────────────────
 * 6. Bootstrap Tooltip init
 * ───────────────────────────────────────────────────────────────────────── */

function initTooltips() {
    const els = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
        els.forEach(function (el) {
            new bootstrap.Tooltip(el, { trigger: 'hover focus' });
        });
    }
}


/* ─────────────────────────────────────────────────────────────────────────
 * Init – runs after DOM is ready
 * ───────────────────────────────────────────────────────────────────────── */

document.addEventListener('DOMContentLoaded', function () {
    initMedicationFilter();
    initReportSearch();
    initPagination();
    initVitalHover();
    initTooltips();
});
