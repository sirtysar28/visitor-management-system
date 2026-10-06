/* =====================================================
   VMS App JS — sidebar, password eye, notifications, dsb
   ===================================================== */
(function () {
  'use strict';

  /* ---------- Sidebar toggle (hide/unhide) ---------- */
  const app = document.getElementById('app');
  const sidebarToggle = document.getElementById('sidebarToggle');
  const backdrop = document.getElementById('sidebarBackdrop');

  function applySidebarState() {
    if (window.innerWidth < 992) {
      app.classList.remove('sidebar-collapsed');
      return;
    }
    const saved = localStorage.getItem('vms.sidebar');
    if (saved === 'collapsed') app.classList.add('sidebar-collapsed');
  }
  applySidebarState();
  window.addEventListener('resize', applySidebarState);

  if (sidebarToggle) {
    sidebarToggle.addEventListener('click', function () {
      if (window.innerWidth < 992) {
        app.classList.toggle('sidebar-open');
      } else {
        const collapsed = app.classList.toggle('sidebar-collapsed');
        localStorage.setItem('vms.sidebar', collapsed ? 'collapsed' : 'expanded');
      }
    });
  }
  if (backdrop) backdrop.addEventListener('click', () => app.classList.remove('sidebar-open'));

  /* ---------- Password eye toggle ----------
     Logic ada di pass-eye.js (dipakai juga oleh halaman login) ---------- */

  /* ---------- Dropdown (notif & profile) ---------- */
  function bindDropdown(btnId, panelId) {
    const btn = document.getElementById(btnId);
    const panel = document.getElementById(panelId);
    if (!btn || !panel) return;

    btn.addEventListener('click', function (e) {
      e.stopPropagation();
      document.querySelectorAll('.notif-panel.open, .profile-menu.open').forEach(p => {
        if (p !== panel) p.classList.remove('open');
      });
      panel.classList.toggle('open');
    });
    document.addEventListener('click', e => {
      if (!panel.contains(e.target) && !btn.contains(e.target)) panel.classList.remove('open');
    });
  }
  bindDropdown('notifBtn', 'notifPanel');
  bindDropdown('profileBtn', 'profileMenu');

  /* ---------- Notifications polling ---------- */
  const notifList = document.getElementById('notifList');
  const notifBadge = document.getElementById('notifBadge');
  const notifBadgeCount = document.getElementById('notifBadgeCount');
  const notifEmpty = document.getElementById('notifEmpty');

  const ICONS = {
    bell: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>',
    check: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>',
    x: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M18 6 6 18M6 6l12 12"/></svg>',
    user: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>',
    shield: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>',
    qr: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><path d="M14 14h3v3h-3zM21 14v.01M14 21v.01M17.5 17.5L21 21"/></svg>'
  };
  const COLORS = {
    bell: ['#eff6ff', '#2563eb'], check: ['#dcfce7', '#16a34a'], x: ['#fee2e2', '#dc2626'],
    user: ['#f5f3ff', '#7c3aed'], shield: ['#ecfeff', '#0891b2'], qr: ['#fef9c3', '#ca8a04']
  };

  function renderNotifications(data) {
    if (notifBadge) {
      notifBadge.style.display = data.unread > 0 ? 'flex' : 'none';
      if (notifBadgeCount) notifBadgeCount.textContent = data.unread > 99 ? '99+' : data.unread;
    }
    if (!notifList) return;

    if (!data.notifications.length) {
      notifList.innerHTML = '';
      if (notifEmpty) notifEmpty.style.display = 'block';
      return;
    }
    if (notifEmpty) notifEmpty.style.display = 'none';

    notifList.innerHTML = data.notifications.map(function (n) {
      const c = COLORS[n.icon] || COLORS.bell;
      return '<a class="notif-item ' + (n.is_read ? '' : 'unread') + '" href="' + (n.url || '#') + '">' +
        '<span class="notif-ico" style="background:' + c[0] + ';color:' + c[1] + '">' + (ICONS[n.icon] || ICONS.bell) + '</span>' +
        '<span style="min-width:0">' +
          '<span class="notif-title">' + n.title + '</span>' +
          (n.body ? '<span class="notif-body">' + n.body + '</span>' : '') +
          '<span class="notif-time">' + n.time + '</span>' +
        '</span></a>';
    }).join('');
  }

  /* ---------- Sidebar pending-approval badges ---------- */
  function renderSidebarBadges(data) {
    document.querySelectorAll('[data-pending-count], [data-approval-count]').forEach(function (el) {
      const n = data.pending_approvals || 0;
      el.textContent = n > 99 ? '99+' : n;
      el.style.display = n > 0 ? 'inline-flex' : 'none';
    });
  }

  function poll() {
    fetch('/notifications/poll', { headers: { 'Accept': 'application/json' } })
      .then(r => (r.ok ? r.json() : null))
      .then(data => {
        if (!data) return;
        renderNotifications(data);
        renderSidebarBadges(data);
      })
      .catch(() => {});
  }
  if (notifList !== null || notifBadge !== null) {
    poll();
    setInterval(poll, 30000);
  }

  /* ---------- Toast auto-hide ---------- */
  setTimeout(() => {
    document.querySelectorAll('.alert-auto').forEach(el => {
      el.style.transition = 'opacity .5s, transform .5s';
      el.style.opacity = '0';
      el.style.transform = 'translateY(-8px)';
      setTimeout(() => el.remove(), 520);
    });
  }, 4500);

  /* ---------- Tabs ---------- */
  document.querySelectorAll('.tabs').forEach(function (tabs) {
    tabs.querySelectorAll('.tab-btn').forEach(function (btn) {
      btn.addEventListener('click', function () {
        const group = btn.dataset.group;
        document.querySelectorAll('[data-group="' + group + '"].tab-btn').forEach(b => b.classList.remove('active'));
        document.querySelectorAll('[data-group="' + group + '"].tab-pane').forEach(p => p.classList.remove('active'));
        btn.classList.add('active');
        const pane = document.getElementById(btn.dataset.target);
        if (pane) pane.classList.add('active');
      });
    });
  });

  /* ---------- Counter animation (statistik dashboard) ---------- */
  window.vmsCounters = function () {
    document.querySelectorAll('[data-count]').forEach(function (el) {
      const target = parseInt(el.dataset.count, 10) || 0;
      const dur = 1100;
      const start = performance.now();
      function tick(now) {
        const p = Math.min((now - start) / dur, 1);
        const eased = 1 - Math.pow(1 - p, 3);
        el.textContent = Math.round(target * eased).toLocaleString('id-ID');
        if (p < 1) requestAnimationFrame(tick);
      }
      requestAnimationFrame(tick);
    });
  };
  if (document.querySelector('[data-count]')) window.vmsCounters();

  /* ---------- Confirm dialogs ---------- */
  document.querySelectorAll('[data-confirm]').forEach(function (el) {
    el.addEventListener('click', function (e) {
      if (!confirm(el.dataset.confirm || 'Yakin melanjutkan?')) e.preventDefault();
    });
  });

  /* ---------- PWA: service worker ---------- */
  if ('serviceWorker' in navigator) {
    window.addEventListener('load', function () {
      navigator.serviceWorker.register('/sw.js').catch(function () {});
    });
  }
})();
