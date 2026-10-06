/* =====================================================
   Landing JS — reveal, counters, navbar, PWA install
   ===================================================== */
(function () {
  'use strict';

  /* ---------- Navbar scroll ---------- */
  const nav = document.getElementById('lpNav');
  const toTop = document.getElementById('toTop');
  function onScroll() {
    if (nav) nav.classList.toggle('scrolled', window.scrollY > 24);
    if (toTop) toTop.classList.toggle('show', window.scrollY > 480);
  }
  window.addEventListener('scroll', onScroll, { passive: true });
  onScroll();
  if (toTop) toTop.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));

  /* ---------- Mobile menu ---------- */
  const burger = document.getElementById('lpBurger');
  const links = document.getElementById('lpNavLinks');
  if (burger && links) {
    burger.addEventListener('click', e => { e.stopPropagation(); links.classList.toggle('open'); });
    document.addEventListener('click', e => { if (!links.contains(e.target)) links.classList.remove('open'); });
    // Tutup menu setelah link diklik (nyaman di mobile)
    links.querySelectorAll('a').forEach(a => a.addEventListener('click', () => links.classList.remove('open')));
    window.addEventListener('resize', () => { if (window.innerWidth > 860) links.classList.remove('open'); });
  }

  /* ---------- Reveal on scroll ---------- */
  const io = new IntersectionObserver(function (entries) {
    entries.forEach(function (entry) {
      if (entry.isIntersecting) {
        entry.target.classList.add('visible');
        io.unobserve(entry.target);
      }
    });
  }, { threshold: 0.14 });
  document.querySelectorAll('.reveal, .reveal-left, .reveal-right').forEach(el => io.observe(el));

  /* ---------- Counter animation ---------- */
  const cio = new IntersectionObserver(function (entries) {
    entries.forEach(function (entry) {
      if (!entry.isIntersecting) return;
      const el = entry.target;
      const target = parseInt(el.dataset.count, 10) || 0;
      const dur = 1500;
      const start = performance.now();
      (function tick(now) {
        const p = Math.min((performance.now() - start) / dur, 1);
        const eased = 1 - Math.pow(1 - p, 3);
        el.textContent = Math.round(target * eased).toLocaleString('id-ID');
        if (p < 1) requestAnimationFrame(tick);
      })();
      cio.unobserve(el);
    });
  }, { threshold: 0.6 });
  document.querySelectorAll('[data-count]').forEach(el => cio.observe(el));

  /* ---------- Parallax halus pada hero mockup ---------- */
  const mockup = document.querySelector('.hero-mockup');
  if (mockup && window.matchMedia('(pointer:fine)').matches) {
    window.addEventListener('mousemove', function (e) {
      const x = (e.clientX / window.innerWidth - 0.5) * 10;
      const y = (e.clientY / window.innerHeight - 0.5) * 8;
      mockup.style.transform = 'translate(' + x + 'px,' + y + 'px)';
      mockup.style.transition = 'transform .25s ease-out';
    }, { passive: true });
  }

  /* ---------- Smooth anchor dengan offset ---------- */
  document.querySelectorAll('a[href^="#"]').forEach(function (a) {
    a.addEventListener('click', function (e) {
      const id = a.getAttribute('href');
      if (id.length < 2) return;
      const target = document.querySelector(id);
      if (!target) return;
      e.preventDefault();
      window.scrollTo({ top: target.getBoundingClientRect().top + window.scrollY - 78, behavior: 'smooth' });
    });
  });

  /* ---------- PWA: tombol install + beforeinstallprompt ---------- */
  let deferredPrompt = null;
  const installBtns = document.querySelectorAll('.pwa-install-btn');
  const toast = document.getElementById('installToast');

  window.addEventListener('beforeinstallprompt', function (e) {
    e.preventDefault();
    deferredPrompt = e;
    if (toast) setTimeout(() => toast.classList.add('show'), 2200);
  });

  function doInstall() {
    if (!deferredPrompt) {
      alert('Untuk memasang aplikasi:\n\n• Chrome/Edge Desktop: klik ikon install di address bar\n• Safari iOS: Share → Add to Home Screen\n• Chrome Android: menu ⋮ → Install app');
      return;
    }
    deferredPrompt.prompt();
    deferredPrompt.userChoice.then(function () {
      deferredPrompt = null;
      if (toast) toast.classList.remove('show');
    });
  }

  installBtns.forEach(function (btn) {
    btn.addEventListener('click', doInstall);
  });

  const toastYes = document.getElementById('installYes');
  const toastNo = document.getElementById('installNo');
  if (toastYes) toastYes.addEventListener('click', doInstall);
  if (toastNo) toastNo.addEventListener('click', () => toast.classList.remove('show'));

  /* ---------- Service worker registration ---------- */
  if ('serviceWorker' in navigator) {
    window.addEventListener('load', function () {
      navigator.serviceWorker.register('/sw.js').catch(function () {});
    });
  }
})();
