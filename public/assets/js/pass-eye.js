/* =====================================================
   Password eye toggle — dipakai di SEMUA halaman
   (login, profil, user, SMTP) agar konsisten
   ===================================================== */
(function () {
  'use strict';

  function findInput(btn) {
    // 1) via data-target (id)
    var byId = document.getElementById(btn.dataset.target);
    if (byId) return byId;

    // 2) fallback: input di dalam wrapper yang sama
    var wrap = btn.closest('.pass-wrap');
    if (wrap) return wrap.querySelector('input');

    return null;
  }

  function toggle(btn) {
    var input = findInput(btn);
    if (!input) return;

    var show = input.type === 'password';
    input.type = show ? 'text' : 'password';
    btn.classList.toggle('active', show);
    btn.setAttribute('title', show ? 'Sembunyikan password' : 'Lihat password');
    btn.setAttribute('aria-label', btn.getAttribute('title'));

    var eyeOn = btn.querySelector('.eye-on');
    var eyeOff = btn.querySelector('.eye-off');
    if (eyeOn && eyeOff) {
      eyeOn.style.display = show ? 'none' : 'block';
      eyeOff.style.display = show ? 'block' : 'none';
    }

    // pertahankan fokus/kursor di input
    input.focus();
    try {
      var len = input.value.length;
      input.setSelectionRange(len, len);
    } catch (e) { /* type=time dsb */ }
  }

  function init(root) {
    (root || document).querySelectorAll('.pass-eye').forEach(function (btn) {
      if (btn.dataset.init === '1') return;
      btn.dataset.init = '1';
      btn.addEventListener('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
        toggle(btn);
      });
      // keyboard accessible
      btn.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); toggle(btn); }
      });
    });
  }

  window.vmsInitPassEyes = init;

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function () { init(document); });
  } else {
    init(document);
  }
})();
