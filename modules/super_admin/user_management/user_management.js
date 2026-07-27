/*=============================================================================
    OLD AGE HOME MANAGEMENT SYSTEM
    Module: Super Admin - User Management
    File: user_management.js
    Description: Live MySQL Database Interactivity, AJAX CRUD Operations, 
                 Validations & DOM Event Handlers.
=============================================================================*/

(function () {
    /* -------------------------------------------------------------------------
       1. STATE PARAMETERS
    ------------------------------------------------------------------------- */
    let currentPage = 1;
    const rowsPerPage = 5;
    let targetDeleteUserId = null;
    let searchDebounceTimer = null;

    /* -------------------------------------------------------------------------
       2. DOM ELEMENTS SELECTION
    ------------------------------------------------------------------------- */
    let userTableBody, emptyState, searchInput, btnClearSearch, roleFilter, statusFilter, btnResetFilters, btnEmptyReset;
    let pageStart, pageEnd, totalRecords, btnPrevPage, btnNextPage, pageNumbers;
    let statTotalUsers, statActiveUsers, statInactiveUsers, statTotalRoles;
    let btnAddUser, addUserModal, addUserForm, addProfileImg;
    let editUserModal, editUserForm, editProfileImg;
    let viewUserModal, deleteModal, deleteUserName, btnConfirmDelete;

    function queryElements() {
        userTableBody = document.getElementById('userTableBody');
        emptyState = document.getElementById('emptyState');
        searchInput = document.getElementById('searchInput');
        btnClearSearch = document.getElementById('btnClearSearch');
        roleFilter = document.getElementById('roleFilter');
        statusFilter = document.getElementById('statusFilter');
        btnResetFilters = document.getElementById('btnResetFilters');
        btnEmptyReset = document.getElementById('btnEmptyReset');

        pageStart = document.getElementById('pageStart');
        pageEnd = document.getElementById('pageEnd');
        totalRecords = document.getElementById('totalRecords');
        btnPrevPage = document.getElementById('btnPrevPage');
        btnNextPage = document.getElementById('btnNextPage');
        pageNumbers = document.getElementById('pageNumbers');

        statTotalUsers = document.getElementById('statTotalUsers');
        statActiveUsers = document.getElementById('statActiveUsers');
        statInactiveUsers = document.getElementById('statInactiveUsers');
        statTotalRoles = document.getElementById('statTotalRoles');

        btnAddUser = document.getElementById('btnAddUser');
        addUserModal = document.getElementById('addUserModal');
        addUserForm = document.getElementById('addUserForm');
        addProfileImg = document.getElementById('addProfileImg');

        editUserModal = document.getElementById('editUserModal');
        editUserForm = document.getElementById('editUserForm');
        editProfileImg = document.getElementById('editProfileImg');

        viewUserModal = document.getElementById('viewUserModal');
        deleteModal = document.getElementById('deleteModal');
        deleteUserName = document.getElementById('deleteUserName');
        btnConfirmDelete = document.getElementById('btnConfirmDelete');
    }

    /* -------------------------------------------------------------------------
       3. INITIALIZATION
    ------------------------------------------------------------------------- */
    function init() {
        queryElements();
        fetchUsers(1);
        setupEventListeners();
    }

    /* -------------------------------------------------------------------------
       4. LIVE MYSQL DATA FETCHING VIA AJAX (USER_API.PHP)
    ------------------------------------------------------------------------- */
    function fetchUsers(page = currentPage) {
        currentPage = page;
        const search = searchInput ? searchInput.value.trim() : '';
        const role = roleFilter ? roleFilter.value : 'All';
        const status = statusFilter ? statusFilter.value : 'All';

        if (btnClearSearch) {
            btnClearSearch.style.display = search.length > 0 ? 'block' : 'none';
        }

        const params = new URLSearchParams({
            action: 'list',
            search: search,
            role: role,
            status: status,
            page: currentPage,
            limit: rowsPerPage
        });

        fetch(`user_api.php?${params.toString()}`)
            .then(res => res.json())
            .then(res => {
                if (res && res.success) {
                    updateDashboardStats(res.stats);
                    renderTable(res.data, res.total, res.page, res.limit);
                } else {
                    showToast('Database Error', res ? res.message : 'Failed to fetch users from database.', 'error');
                }
            })
            .catch(err => {
                console.error('Fetch Users API Error:', err);
                showToast('Connection Error', 'Failed to communicate with MySQL backend.', 'error');
            });
    }

    /* -------------------------------------------------------------------------
       5. DASHBOARD STATS UPDATER
    ------------------------------------------------------------------------- */
    function updateDashboardStats(stats) {
        if (!stats) return;
        animateCounter(statTotalUsers, stats.total_users || 0);
        animateCounter(statActiveUsers, stats.active_users || 0);
        animateCounter(statInactiveUsers, stats.inactive_users || 0);
        animateCounter(statTotalRoles, stats.total_roles || 0);
    }

    function animateCounter(element, target) {
        if (!element) return;
        let current = 0;
        const duration = 250;
        const stepTime = 20;
        const increment = target / (duration / stepTime);

        if (target === 0) {
            element.textContent = "0";
            return;
        }

        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                element.textContent = target;
                clearInterval(timer);
            } else {
                element.textContent = Math.ceil(current);
            }
        }, stepTime);
    }

    /* -------------------------------------------------------------------------
       6. TABLE RENDERING & PAGINATION
    ------------------------------------------------------------------------- */
    function renderTable(users, totalCount, page, limit) {
        if (!userTableBody) return;
        userTableBody.innerHTML = '';

        if (!users || users.length === 0) {
            if (emptyState) emptyState.style.display = 'block';
            updatePaginationInfo(0, 0, 0);
            renderPaginationControls(0);
            return;
        }

        if (emptyState) emptyState.style.display = 'none';

        const startIndex = (page - 1) * limit + 1;
        const endIndex = Math.min(startIndex + users.length - 1, totalCount);

        users.forEach(user => {
            const tr = document.createElement('tr');
            
            const avatarSrc = user.photo && user.photo !== '' 
                ? user.photo 
                : `https://ui-avatars.com/api/?name=${encodeURIComponent(user.name)}&background=6B9080&color=fff`;

            const statusClass = (user.status || 'Active').toLowerCase();

            tr.innerHTML = `
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <img src="${avatarSrc}" alt="${escapeHtml(user.name)}" class="user-avatar" style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover;">
                        <div>
                            <div class="fw-bold text-dark" style="font-size: 13px;">${escapeHtml(user.name)}</div>
                            <small class="text-muted" style="font-size: 11px;">#${user.id} • ${escapeHtml(user.gender || 'Unspecified')}</small>
                        </div>
                    </div>
                </td>
                <td>
                    <div style="font-size: 12px; font-weight: 500;">${escapeHtml(user.email)}</div>
                    <small class="text-muted" style="font-size: 11px;">${escapeHtml(user.phone || 'N/A')}</small>
                </td>
                <td><span class="badge bg-light text-dark border" style="font-size: 11px; padding: 4px 8px;">${escapeHtml(user.role)}</span></td>
                <td>
                    <span class="badge-status ${statusClass}">${escapeHtml(user.status)}</span>
                </td>
                <td style="font-size: 12px; color: var(--text-muted);">${user.created_at || 'N/A'}</td>
                <td class="text-end">
                    <div class="d-inline-flex gap-1">
                        <button type="button" class="btn-action btn-view btn btn-sm btn-light border" data-id="${user.id}" title="View Profile">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                        </button>
                        <button type="button" class="btn-action btn-edit btn btn-sm btn-light border" data-id="${user.id}" title="Edit User">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 2 2h14a2 2 0 0 2 2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                        </button>
                        <button type="button" class="btn-action btn-toggle btn btn-sm btn-light border" data-id="${user.id}" title="${user.status === 'Active' ? 'Deactivate Account' : 'Activate Account'}">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="5" width="22" height="14" rx="7" ry="7"></rect><circle cx="${user.status === 'Active' ? '16' : '8'}" cy="12" r="3"></circle></svg>
                        </button>
                        <button type="button" class="btn-action btn-delete btn btn-sm btn-light border text-danger" data-id="${user.id}" title="Delete User">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                        </button>
                    </div>
                </td>
            `;
            userTableBody.appendChild(tr);
        });

        updatePaginationInfo(startIndex, endIndex, totalCount);
        renderPaginationControls(Math.ceil(totalCount / limit));
    }

    function updatePaginationInfo(start, end, total) {
        if (pageStart) pageStart.textContent = start;
        if (pageEnd) pageEnd.textContent = end;
        if (totalRecords) totalRecords.textContent = total;
    }

    function renderPaginationControls(totalPages) {
        if (!pageNumbers) return;
        pageNumbers.innerHTML = '';

        if (btnPrevPage) {
            btnPrevPage.disabled = currentPage <= 1;
        }

        if (btnNextPage) {
            btnNextPage.disabled = currentPage >= totalPages || totalPages === 0;
        }

        if (totalPages <= 1) return;

        for (let i = 1; i <= totalPages; i++) {
            const pageBtn = document.createElement('button');
            pageBtn.type = 'button';
            pageBtn.className = `page-btn ${i === currentPage ? 'active' : ''}`;
            pageBtn.textContent = i;
            pageBtn.addEventListener('click', () => {
                fetchUsers(i);
            });
            pageNumbers.appendChild(pageBtn);
        }
    }

    /* -------------------------------------------------------------------------
       7. MODAL CONTROLLERS & EXPOSED GLOBAL HELPERS
    ------------------------------------------------------------------------- */
    function openModal(modal) {
        if (!modal) return;
        modal.classList.add('active');
        modal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
    }

    function closeModal(modal) {
        if (!modal) return;
        modal.classList.remove('active');
        modal.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
    }

    window.openUserModal = function(modalId) {
        const targetModal = document.getElementById(modalId);
        if (targetModal) openModal(targetModal);
    };

    window.closeUserModal = function(modalId) {
        const targetModal = document.getElementById(modalId);
        if (targetModal) closeModal(targetModal);
    };

    window.resetUserFilters = function() {
        if (searchInput) searchInput.value = '';
        if (roleFilter) roleFilter.value = 'All';
        if (statusFilter) statusFilter.value = 'All';
        fetchUsers(1);
    };

    /* -------------------------------------------------------------------------
       8. FORM SUBMISSIONS & MYSQL DATABASE INTEGRATION
    ------------------------------------------------------------------------- */
    if (addUserForm) {
        addUserForm.addEventListener('submit', (e) => {
            e.preventDefault();
            
            const fullName = document.getElementById('addFullName').value.trim();
            const email    = document.getElementById('addEmail').value.trim();
            const phone    = document.getElementById('addPhone').value.trim();
            const role     = document.getElementById('addRole').value;
            const password = document.getElementById('addPassword') ? document.getElementById('addPassword').value : 'User@123';

            document.querySelectorAll('#addUserForm .field-error').forEach(el => el.style.display = 'none');

            let isValid = true;
            if (!fullName) {
                showFieldError('errAddFullName', 'Please enter a valid full name.');
                isValid = false;
            }
            if (!email || !validateEmail(email)) {
                showFieldError('errAddEmail', 'Please enter a valid email address.');
                isValid = false;
            }
            if (!phone || phone.length < 7) {
                showFieldError('errAddPhone', 'Enter a valid mobile number.');
                isValid = false;
            }
            if (!role) {
                showFieldError('errAddRole', 'Please assign a user role.');
                isValid = false;
            }

            if (!isValid) {
                showToast('Validation Error', 'Please check the highlighted form fields.', 'error');
                return;
            }

            const formData = new FormData();
            formData.append('action', 'add');
            formData.append('full_name', fullName);
            formData.append('email', email);
            formData.append('phone', phone);
            formData.append('gender', document.getElementById('addGender').value);
            formData.append('role', role);
            formData.append('status', document.getElementById('addStatus').value);
            formData.append('address', document.getElementById('addAddress').value.trim());
            formData.append('password', password);

            if (addProfileImg && addProfileImg.files[0]) {
                formData.append('photo_file', addProfileImg.files[0]);
            }

            fetch('user_api.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(res => {
                if (res && res.success) {
                    showToast('Success', 'User added successfully', 'success');
                    closeModal(addUserModal);
                    addUserForm.reset();
                    // Refreshes User Details Table and Stats live from MySQL Database
                    fetchUsers(1);
                } else {
                    showToast('Validation Error', res ? res.message : 'Error adding user to database', 'error');
                }
            })
            .catch(err => {
                console.error(err);
                showToast('Database Error', 'Failed to insert user into MySQL database.', 'error');
            });
        });
    }

    if (editUserForm) {
        editUserForm.addEventListener('submit', (e) => {
            e.preventDefault();
            
            const userId   = document.getElementById('editUserId').value;
            const fullName = document.getElementById('editFullName').value.trim();
            const email    = document.getElementById('editEmail').value.trim();
            const phone    = document.getElementById('editPhone').value.trim();
            const role     = document.getElementById('editRole').value;
            const password = document.getElementById('editPassword') ? document.getElementById('editPassword').value : '';

            document.querySelectorAll('#editUserForm .field-error').forEach(el => el.style.display = 'none');

            let isValid = true;
            if (!fullName) {
                showFieldError('errEditFullName', 'Please enter a valid full name.');
                isValid = false;
            }
            if (!email || !validateEmail(email)) {
                showFieldError('errEditEmail', 'Please enter a valid email address.');
                isValid = false;
            }
            if (!phone || phone.length < 7) {
                showFieldError('errEditPhone', 'Enter a valid mobile number.');
                isValid = false;
            }
            if (!role) {
                showFieldError('errEditRole', 'Please assign a user role.');
                isValid = false;
            }

            if (!isValid) {
                showToast('Validation Error', 'Please check the highlighted form fields.', 'error');
                return;
            }

            const formData = new FormData();
            formData.append('action', 'edit');
            formData.append('id', userId);
            formData.append('full_name', fullName);
            formData.append('email', email);
            formData.append('phone', phone);
            formData.append('gender', document.getElementById('editGender').value);
            formData.append('role', role);
            formData.append('status', document.getElementById('editStatus').value);
            formData.append('address', document.getElementById('editAddress').value.trim());

            if (password) {
                formData.append('password', password);
            }

            if (editProfileImg && editProfileImg.files[0]) {
                formData.append('photo_file', editProfileImg.files[0]);
            }

            fetch('user_api.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(res => {
                if (res && res.success) {
                    showToast('Success', 'User updated successfully', 'success');
                    closeModal(editUserModal);
                    // Refreshes User Details Table live from MySQL Database
                    fetchUsers(currentPage);
                } else {
                    showToast('Update Error', res ? res.message : 'Error updating user', 'error');
                }
            })
            .catch(err => {
                console.error(err);
                showToast('Database Error', 'Failed to update user profile in database.', 'error');
            });
        });
    }

    function showFieldError(errorId, msg) {
        const errEl = document.getElementById(errorId);
        if (errEl) {
            errEl.textContent = msg;
            errEl.style.display = 'block';
        }
    }

    function validateEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    /* -------------------------------------------------------------------------
       9. EVENT LISTENERS & ACTION DELEGATION
    ------------------------------------------------------------------------- */
    function setupEventListeners() {

        if (searchInput) {
            searchInput.addEventListener('input', () => {
                clearTimeout(searchDebounceTimer);
                searchDebounceTimer = setTimeout(() => {
                    fetchUsers(1);
                }, 200);
            });
        }

        if (btnClearSearch) {
            btnClearSearch.addEventListener('click', () => {
                searchInput.value = '';
                fetchUsers(1);
            });
        }

        if (roleFilter) roleFilter.addEventListener('change', () => fetchUsers(1));
        if (statusFilter) statusFilter.addEventListener('change', () => fetchUsers(1));

        if (btnResetFilters) {
            btnResetFilters.addEventListener('click', window.resetUserFilters);
        }

        if (btnEmptyReset) {
            btnEmptyReset.addEventListener('click', window.resetUserFilters);
        }

        if (btnPrevPage) {
            btnPrevPage.addEventListener('click', () => {
                if (currentPage > 1) fetchUsers(currentPage - 1);
            });
        }

        if (btnNextPage) {
            btnNextPage.addEventListener('click', () => {
                fetchUsers(currentPage + 1);
            });
        }

        if (btnAddUser) {
            btnAddUser.addEventListener('click', () => openModal(addUserModal));
        }

        if (userTableBody) {
            userTableBody.addEventListener('click', (e) => {
                const btn = e.target.closest('.btn-action');
                if (!btn) return;

                const userId = btn.getAttribute('data-id');
                if (!userId) return;

                // View Profile
                if (btn.classList.contains('btn-view')) {
                    fetch(`user_api.php?action=get&id=${userId}`)
                        .then(res => res.json())
                        .then(res => {
                            if (res && res.success && res.data) {
                                populateViewModal(res.data);
                            } else {
                                showToast('Error', 'User record not found in database.', 'error');
                            }
                        })
                        .catch(err => {
                            showToast('Database Error', 'Failed to fetch user profile.', 'error');
                        });

                // Edit User
                } else if (btn.classList.contains('btn-edit')) {
                    fetch(`user_api.php?action=get&id=${userId}`)
                        .then(res => res.json())
                        .then(res => {
                            if (res && res.success && res.data) {
                                populateEditModal(res.data);
                            } else {
                                showToast('Error', 'User details not found.', 'error');
                            }
                        })
                        .catch(err => {
                            showToast('Database Error', 'Failed to load user details.', 'error');
                        });

                // Toggle User Status
                } else if (btn.classList.contains('btn-toggle')) {
                    const formData = new FormData();
                    formData.append('action', 'toggle_status');
                    formData.append('id', userId);

                    fetch('user_api.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(res => res.json())
                    .then(res => {
                        if (res && res.success) {
                            showToast('Success', res.new_status === 'Active' ? 'User activated successfully' : 'User deactivated successfully', res.new_status === 'Active' ? 'success' : 'warning');
                            fetchUsers(currentPage);
                        } else {
                            showToast('Error', res ? res.message : 'Failed to update status', 'error');
                        }
                    })
                    .catch(err => {
                        showToast('Database Error', 'Failed to toggle account status.', 'error');
                    });

                // Delete User Trigger
                } else if (btn.classList.contains('btn-delete')) {
                    targetDeleteUserId = userId;
                    const rowName = btn.closest('tr')?.querySelector('.fw-bold')?.textContent || 'User';
                    if (deleteUserName) deleteUserName.textContent = rowName;
                    openModal(deleteModal);
                }
            });
        }

        if (btnConfirmDelete) {
            btnConfirmDelete.addEventListener('click', () => {
                if (!targetDeleteUserId) return;

                const formData = new FormData();
                formData.append('action', 'delete');
                formData.append('id', targetDeleteUserId);

                fetch('user_api.php', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(res => {
                    if (res && res.success) {
                        showToast('Success', 'User deleted successfully', 'success');
                        closeModal(deleteModal);
                        targetDeleteUserId = null;
                        fetchUsers(currentPage);
                    } else {
                        showToast('Error', res ? res.message : 'Failed to delete user.', 'error');
                    }
                })
                .catch(err => {
                    showToast('Database Error', 'Failed to delete user from database.', 'error');
                });
            });
        }

        document.querySelectorAll('[data-close]').forEach(btn => {
            btn.addEventListener('click', () => {
                const modalId = btn.getAttribute('data-close');
                const modal = document.getElementById(modalId);
                if (modal) closeModal(modal);
            });
        });

        document.querySelectorAll('.modal-overlay').forEach(modal => {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) closeModal(modal);
            });
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                const activeModal = document.querySelector('.modal-overlay.active');
                if (activeModal) closeModal(activeModal);
            }
        });
    }

    function populateViewModal(user) {
        document.getElementById('viewUserId').textContent = `#${user.id}`;
        document.getElementById('viewFullName').textContent = user.name;
        document.getElementById('viewEmail').textContent = user.email;
        document.getElementById('viewPhone').textContent = user.phone || 'N/A';
        document.getElementById('viewGender').textContent = user.gender || 'Unspecified';
        document.getElementById('viewRole').textContent = user.role;
        document.getElementById('viewStatus').textContent = user.status;
        document.getElementById('viewCreatedDate').textContent = user.created_at || 'N/A';
        document.getElementById('viewAddress').textContent = user.address || 'No address provided.';

        const avatarSrc = user.photo && user.photo !== '' 
            ? user.photo 
            : `https://ui-avatars.com/api/?name=${encodeURIComponent(user.name)}&background=6B9080&color=fff`;
        document.getElementById('viewImg').src = avatarSrc;

        const statusBadge = document.getElementById('viewStatus');
        if (statusBadge) {
            statusBadge.className = `status-badge badge-status ${(user.status || 'Active').toLowerCase()}`;
        }

        openModal(viewUserModal);
    }

    function populateEditModal(user) {
        document.getElementById('editUserId').value = user.id;
        document.getElementById('editFullName').value = user.name;
        document.getElementById('editEmail').value = user.email;
        document.getElementById('editPhone').value = user.phone || '';
        document.getElementById('editGender').value = user.gender || 'Male';
        document.getElementById('editRole').value = user.role;
        document.getElementById('editStatus').value = user.status || 'Active';
        document.getElementById('editAddress').value = user.address || '';
        if (document.getElementById('editPassword')) {
            document.getElementById('editPassword').value = '';
        }

        openModal(editUserModal);
    }

    /* -------------------------------------------------------------------------
       10. TOAST NOTIFICATION SYSTEM (TOP RIGHT CORNER)
    ------------------------------------------------------------------------- */
    function showToast(title, message, type = 'success') {
        const toastContainer = document.getElementById('toastContainer');
        if (!toastContainer) return;

        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;

        let iconSvg = '';
        if (type === 'success') {
            iconSvg = `<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>`;
        } else if (type === 'error') {
            iconSvg = `<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>`;
        } else if (type === 'info') {
            iconSvg = `<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>`;
        } else {
            iconSvg = `<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>`;
        }

        toast.innerHTML = `
            <div class="toast-icon">${iconSvg}</div>
            <div class="toast-content">
                <div class="toast-title">${escapeHtml(title)}</div>
                <div class="toast-message">${escapeHtml(message)}</div>
            </div>
            <button type="button" class="toast-close">&times;</button>
        `;

        toastContainer.appendChild(toast);

        setTimeout(() => toast.classList.add('show'), 10);

        toast.querySelector('.toast-close').addEventListener('click', () => {
            removeToast(toast);
        });

        setTimeout(() => {
            removeToast(toast);
        }, 4000);
    }

    function removeToast(toast) {
        toast.classList.remove('show');
        setTimeout(() => {
            if (toast && toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        }, 300);
    }

    function escapeHtml(str) {
        return String(str || '')
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    // Self-starting execution trigger
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();