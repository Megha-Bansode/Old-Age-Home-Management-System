document.addEventListener('DOMContentLoaded', () => {

  // ==========================================
  // 1. MOBILE MENU TOGGLE
  // ==========================================
  const sidebar = document.getElementById('sidebar');
  const menuBtn = document.getElementById('menuBtn');
  const backdrop = document.getElementById('backdrop');

  // Open Mobile Drawer
  if (menuBtn) {
    menuBtn.addEventListener('click', () => {
      sidebar.classList.add('open');
      backdrop.classList.add('on');
    });
  }

  // Close Mobile Drawer
  if (backdrop) {
    backdrop.addEventListener('click', closeMobileMenu);
  }

  function closeMobileMenu() {
    sidebar.classList.remove('open');
    backdrop.classList.remove('on');
  }


  // ==========================================
  // 2. PAGE NAVIGATION & CRUMBS
  // ==========================================
  const navItems = document.querySelectorAll('.nav-item[data-page]');
  const pages = document.querySelectorAll('.page');
  const crumbPage = document.getElementById('crumbPage');

  navItems.forEach(item => {
    item.addEventListener('click', (e) => {
      e.preventDefault();
      const targetPage = item.getAttribute('data-page');

      // Update Navigation Active Class
      navItems.forEach(nav => nav.classList.remove('active'));
      item.classList.add('active');

      // Update Visible Page
      pages.forEach(page => {
        page.classList.remove('active');
        if (page.id === `page-${targetPage}`) {
          page.classList.add('active');
        }
      });

      // Update Breadcrumb Text
      if (crumbPage) {
        crumbPage.textContent = item.textContent.trim().replace(/^[\u2000-\u3300\ud83c-\udfff]\s*/, ''); 
      }

      // Close mobile menu on navigate
      closeMobileMenu();
    });
  });


  // ==========================================
  // 3. ATTENDANCE INTERACTION & SEARCH
  // ==========================================
  const attendanceTable = document.getElementById('attendanceTable');
  const attSearch = document.getElementById('attSearch');

  if (attendanceTable) {
    // Attendance Chip Button Toggle
    attendanceTable.addEventListener('click', (e) => {
      const chip = e.target.closest('.chip');
      if (!chip) return;

      const chipGroup = chip.closest('.chip-group');
      const row = chip.closest('tr');
      const statusBadge = row.querySelector('.badge');
      const setStatus = chip.getAttribute('data-set');

      // Reset active states
      chipGroup.querySelectorAll('.chip').forEach(btn => btn.classList.remove('on'));
      chip.classList.add('on');

      // Update Badge Text & Styling
      if (statusBadge) {
        statusBadge.className = 'badge';
        if (setStatus === 'present') {
          statusBadge.textContent = 'Present';
          statusBadge.classList.add('green');
        } else if (setStatus === 'absent') {
          statusBadge.textContent = 'Absent';
          statusBadge.classList.add('red');
        } else if (setStatus === 'late') {
          statusBadge.textContent = 'Late';
          statusBadge.classList.add('amber');
        }
      }
    });
  }

  // Attendance Table Real-time Search Filter
  if (attSearch && attendanceTable) {
    attSearch.addEventListener('input', () => {
      const query = attSearch.value.toLowerCase();
      const rows = attendanceTable.querySelectorAll('tbody tr');

      rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(query) ? '' : 'none';
      });
    });
  }

  // Save Attendance Notification
  const saveAttendanceBtn = document.getElementById('saveAttendance');
  if (saveAttendanceBtn) {
    saveAttendanceBtn.addEventListener('click', () => {
      alert('Attendance records saved successfully!');
    });
  }


  // ==========================================
  // 4. EMERGENCY FORM HANDLER
  // ==========================================
  const emergencyForm = document.getElementById('emergencyForm');
  if (emergencyForm) {
    emergencyForm.addEventListener('submit', (e) => {
      e.preventDefault();
      
      const formData = new FormData(emergencyForm);
      const resident = formData.get('resident') || 'Unspecified';
      const type = formData.get('type') || 'General';

      alert(`🚨 Emergency Report Submitted!\n\nType: ${type}\nResident: ${resident}`);
      emergencyForm.reset();
    });
  }


  // ==========================================
  // 5. MINI CALENDAR GENERATOR (ACTIVITIES PAGE)
  // ==========================================
  const calGrid = document.getElementById('calGrid');
  if (calGrid) {
    const today = new Date().getDate();
    const totalDays = 31; // Days in month representation

    for (let i = 1; i <= totalDays; i++) {
      const dayEl = document.createElement('div');
      dayEl.classList.add('day');
      dayEl.textContent = i;

      if (i === today) {
        dayEl.classList.add('today');
      } else if (i % 3 === 0 || i % 7 === 0) {
        dayEl.classList.add('has'); // Highlight active event days
      }

      calGrid.appendChild(dayEl);
    }
  }

});