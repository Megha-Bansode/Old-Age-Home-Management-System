/**
 * SevaNest – Resident Profile JavaScript
 * File     : assets/js/resident-profile.js
 * Version  : 1.0
 * Author   : SevaNest Dev Team
 *
 * Features
 * --------
 * 1. Photo upload preview – clicking the photo wrap triggers file input.
 * 2. Keyboard accessibility for photo wrap.
 * 3. Bootstrap tooltip initialisation.
 * 4. Smooth entrance animation trigger.
 */

'use strict';

/* ─────────────────────────────────────────────────────────────────────────
 * 1. Photo Upload Preview
 * ───────────────────────────────────────────────────────────────────────── */

function initPhotoUpload() {
    const photoWrap  = document.getElementById('rp-photo-wrap');
    const photoInput = document.getElementById('rp-photo-input');
    const placeholder = document.getElementById('rp-photo-placeholder');

    if (!photoWrap || !photoInput) return;

    // Open file picker on click
    photoWrap.addEventListener('click', function () {
        photoInput.click();
    });

    // Keyboard: Enter or Space triggers click
    photoWrap.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            photoInput.click();
        }
    });

    // Preview selected image
    photoInput.addEventListener('change', function () {
        const file = this.files && this.files[0];
        if (!file || !file.type.startsWith('image/')) return;

        const reader = new FileReader();
        reader.onload = function (evt) {
            // Remove placeholder if present
            if (placeholder) placeholder.style.display = 'none';

            // Update or create <img> inside wrap
            let img = document.getElementById('rp-resident-img');
            if (!img) {
                img = document.createElement('img');
                img.id  = 'rp-resident-img';
                img.alt = 'Resident photo';
                // Insert before the overlay
                const overlay = photoWrap.querySelector('.rp-photo-overlay');
                photoWrap.insertBefore(img, overlay);
            }
            img.src = evt.target.result;
        };
        reader.readAsDataURL(file);
    });
}


/* ─────────────────────────────────────────────────────────────────────────
 * 2. Field value hover pulse (micro-interaction)
 * ───────────────────────────────────────────────────────────────────────── */

function initFieldHover() {
    document.querySelectorAll('.rp-field-value').forEach(function (el) {
        el.addEventListener('mouseenter', function () {
            this.style.transform = 'translateX(2px)';
            this.style.transition = 'transform 150ms ease';
        });
        el.addEventListener('mouseleave', function () {
            this.style.transform = '';
        });
    });
}


/* ─────────────────────────────────────────────────────────────────────────
 * 3. Bootstrap Tooltip init
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
 * 4. Init
 * ───────────────────────────────────────────────────────────────────────── */

document.addEventListener('DOMContentLoaded', function () {
    initPhotoUpload();
    initFieldHover();
    initTooltips();
});
