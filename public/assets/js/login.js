/* =====================================================
   Login page JS — captcha refresh + password eye
   ===================================================== */
(function () {
  'use strict';

  /* Refresh captcha image */
  const img = document.getElementById('captchaImg');
  const refresh = document.getElementById('captchaRefresh');
  const input = document.getElementById('captchaInput');

  function newCaptcha() {
    if (!img) return;
    img.style.opacity = '0';
    setTimeout(function () {
      img.src = '/captcha?' + Date.now();
      img.style.opacity = '1';
    }, 130);
  }

  if (img) {
    img.addEventListener('load', () => (img.style.opacity = '1'));
    img.addEventListener('click', newCaptcha);
  }
  if (refresh) refresh.addEventListener('click', newCaptcha);
  if (input) {
    input.addEventListener('input', function () {
      this.value = this.value.toUpperCase().trim();
    });
  }

  /* Shake on error */
  const card = document.querySelector('.auth-left-inner');
  if (card && card.dataset.error === '1') {
    card.classList.add('shake');
    setTimeout(() => card.classList.remove('shake'), 600);
    newCaptcha();
  }
})();
