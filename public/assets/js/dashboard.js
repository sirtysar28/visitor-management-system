/* =====================================================
   Dashboard JS — bar chart animasi + venn diagram SVG
   ===================================================== */
(function () {
  'use strict';

  /* ---------- Bar Chart (Chart.js) : kunjungan 7 hari ---------- */
  const barCanvas = document.getElementById('barChart');
  if (barCanvas && window.Chart) {
    const labels = JSON.parse(barCanvas.dataset.labels || '[]');
    const data = JSON.parse(barCanvas.dataset.data || '[]');

    const gradient = barCanvas.getContext('2d').createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, '#3b82f6');
    gradient.addColorStop(1, 'rgba(37, 99, 235, .25)');

    new Chart(barCanvas, {
      type: 'bar',
      data: {
        labels: labels,
        datasets: [{
          label: 'Kunjungan',
          data: data,
          backgroundColor: gradient,
          hoverBackgroundColor: '#7c3aed',
          borderRadius: 10,
          borderSkipped: false,
          maxBarThickness: 46,
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        animation: {
          duration: 1400,
          easing: 'easeOutQuart',
          delay: ctx => ctx.dataIndex * 90
        },
        plugins: {
          legend: { display: false },
          tooltip: {
            backgroundColor: '#0f172a',
            padding: 12,
            cornerRadius: 10,
            displayColors: false,
            callbacks: {
              label: ctx => ctx.parsed.y + ' kunjungan'
            }
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            ticks: { precision: 0, color: '#94a3b8' },
            grid: { color: '#eef2f7' }
          },
          x: {
            ticks: { color: '#94a3b8', maxRotation: 0 },
            grid: { display: false }
          }
        }
      }
    });
  }

  /* ---------- Venn Diagram (SVG animasi custom) ---------- */
  const venn = document.getElementById('vennDiagram');
  if (venn) {
    const d = {
      pre: +venn.dataset.pre || 0,
      walk: +venn.dataset.walk || 0,
      vendor: +venn.dataset.vendor || 0
    };

    const NS = 'http://www.w3.org/2000/svg';
    const cx = 160, cy = 150, r = 78;

    const circles = [
      { x: cx - 34, y: cy - 26, color: '#2563eb', op: .78, anim: 'venn-in', delay: 0 },
      { x: cx + 34, y: cy - 26, color: '#7c3aed', op: .78, anim: 'venn-in', delay: 180 },
      { x: cx, y: cy + 36, color: '#06b6d4', op: .78, anim: 'venn-in', delay: 360 }
    ];

    circles.forEach(function (c, i) {
      const circle = document.createElementNS(NS, 'circle');
      circle.setAttribute('cx', c.x);
      circle.setAttribute('cy', c.y);
      circle.setAttribute('r', r);
      circle.setAttribute('fill', c.color);
      circle.setAttribute('fill-opacity', c.op);
      circle.setAttribute('class', 'venn-circle');
      circle.style.animationDelay = c.delay + 'ms';
      if (i === 1) circle.classList.add('venn-pulse');
      venn.appendChild(circle);
    });

    // center total label
    const total = d.pre + d.walk + d.vendor;
    const t1 = document.createElementNS(NS, 'text');
    t1.setAttribute('x', cx); t1.setAttribute('y', cy + 2);
    t1.setAttribute('text-anchor', 'middle');
    t1.setAttribute('fill', '#fff');
    t1.setAttribute('font-size', '26');
    t1.setAttribute('font-weight', '800');
    t1.setAttribute('style', 'animation: venn-in .9s 1s both; font-family:inherit');
    t1.textContent = total.toLocaleString('id-ID');
    venn.appendChild(t1);

    const t2 = document.createElementNS(NS, 'text');
    t2.setAttribute('x', cx); t2.setAttribute('y', cy + 22);
    t2.setAttribute('text-anchor', 'middle');
    t2.setAttribute('fill', 'rgba(255,255,255,.85)');
    t2.setAttribute('font-size', '11');
    t2.setAttribute('font-weight', '600');
    t2.setAttribute('style', 'animation: venn-in .9s 1.1s both; font-family:inherit');
    t2.textContent = 'Total Tamu';
    venn.appendChild(t2);

    // legend counters
    document.querySelectorAll('.venn-legend [data-venn-count]').forEach(function (el) {
      const key = el.dataset.vennCount;
      const target = d[key] || 0;
      const start = performance.now();
      setTimeout(function tick() {
        const p = Math.min((performance.now() - start) / 1200, 1);
        el.textContent = Math.round(target * (1 - Math.pow(1 - p, 3))).toLocaleString('id-ID');
        if (p < 1) requestAnimationFrame(tick);
      }, 400);
    });
  }

  /* ---------- Donut status (jika ada) ---------- */
  const donutCanvas = document.getElementById('statusChart');
  if (donutCanvas && window.Chart) {
    const labels = JSON.parse(donutCanvas.dataset.labels || '[]');
    const data = JSON.parse(donutCanvas.dataset.data || '[]');
    const colors = ['#d97706', '#16a34a', '#dc2626', '#0891b2', '#64748b', '#94a3b8'];

    new Chart(donutCanvas, {
      type: 'doughnut',
      data: {
        labels: labels,
        datasets: [{ data: data, backgroundColor: colors, borderWidth: 3, borderColor: '#fff', hoverOffset: 10 }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '66%',
        animation: { animateRotate: true, duration: 1300, easing: 'easeOutQuart' },
        plugins: {
          legend: { position: 'bottom', labels: { usePointStyle: true, pointStyle: 'circle', padding: 16, color: '#475569' } }
        }
      }
    });
  }
})();
