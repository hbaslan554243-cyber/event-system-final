/* ============================================================
   EventPro — Main JavaScript
   ============================================================ */

document.addEventListener('DOMContentLoaded', () => {

  // ── Auto-dismiss alerts ──────────────────────────────────
  document.querySelectorAll('.alert:not(.alert-permanent)').forEach(el => {
    setTimeout(() => {
      const bsAlert = bootstrap.Alert.getOrCreateInstance(el);
      bsAlert?.close();
    }, 5000);
  });

  // ── Active nav highlight ─────────────────────────────────
  const currentPath = window.location.pathname;
  document.querySelectorAll('.navbar-nav .nav-link').forEach(link => {
    if (link.getAttribute('href') === currentPath) {
      link.classList.add('active');
    }
  });

  // ── Confirm delete dialogs ───────────────────────────────
  document.querySelectorAll('[data-confirm]').forEach(el => {
    el.addEventListener('click', e => {
      if (!confirm(el.dataset.confirm || 'Are you sure?')) {
        e.preventDefault();
      }
    });
  });

  // ── Image preview on file input ──────────────────────────
  document.querySelectorAll('.image-preview-input').forEach(input => {
    input.addEventListener('change', function () {
      const previewId = this.dataset.preview;
      const preview = document.getElementById(previewId);
      if (preview && this.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
          preview.src = e.target.result;
          preview.classList.remove('d-none');
        };
        reader.readAsDataURL(this.files[0]);
      }
    });
  });

  // ── Character counter ────────────────────────────────────
  document.querySelectorAll('[data-max-chars]').forEach(el => {
    const max = parseInt(el.dataset.maxChars);
    const counterId = el.dataset.counter;
    const counter = document.getElementById(counterId);
    if (!counter) return;
    const update = () => {
      const remaining = max - el.value.length;
      counter.textContent = `${remaining} characters remaining`;
      counter.style.color = remaining < 20 ? '#ef4444' : '#94a3b8';
    };
    el.addEventListener('input', update);
    update();
  });

  // ── Sidebar toggle (mobile) ──────────────────────────────
  const sidebarToggle = document.getElementById('sidebarToggle');
  const sidebar = document.getElementById('sidebar');
  if (sidebarToggle && sidebar) {
    sidebarToggle.addEventListener('click', () => sidebar.classList.toggle('show'));
    document.addEventListener('click', e => {
      if (sidebar.classList.contains('show') && !sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
        sidebar.classList.remove('show');
      }
    });
  }

  // ── Smooth scroll for anchor links ───────────────────────
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', e => {
      const target = document.querySelector(anchor.getAttribute('href'));
      if (target) {
        e.preventDefault();
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    });
  });

  // ── Tooltip init ─────────────────────────────────────────
  document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
    new bootstrap.Tooltip(el);
  });

  // ── Popover init ─────────────────────────────────────────
  document.querySelectorAll('[data-bs-toggle="popover"]').forEach(el => {
    new bootstrap.Popover(el);
  });

  // ── Dynamic search filter (client-side) ──────────────────
  const searchFilter = document.getElementById('tableSearch');
  if (searchFilter) {
    searchFilter.addEventListener('input', function () {
      const term = this.value.toLowerCase();
      document.querySelectorAll('.filterable-row').forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(term) ? '' : 'none';
      });
    });
  }

  // ── Copy to clipboard ────────────────────────────────────
  document.querySelectorAll('.copy-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      const text = btn.dataset.copy || btn.previousElementSibling?.textContent?.trim();
      if (!text) return;
      navigator.clipboard.writeText(text).then(() => {
        const orig = btn.innerHTML;
        btn.innerHTML = '<i class="bi bi-check"></i>';
        btn.classList.add('btn-success');
        setTimeout(() => { btn.innerHTML = orig; btn.classList.remove('btn-success'); }, 2000);
      });
    });
  });

  // ── QR Scanner (camera) ──────────────────────────────────
  const scanBtn = document.getElementById('startCamera');
  if (scanBtn) {
    scanBtn.addEventListener('click', async () => {
      try {
        const stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } });
        const video = document.getElementById('qrVideo');
        if (video) { video.srcObject = stream; video.play(); }
      } catch (err) {
        alert('Camera access denied. Please use manual input.');
      }
    });
  }

  // ── Number formatting in inputs ──────────────────────────
  document.querySelectorAll('.currency-input').forEach(input => {
    input.addEventListener('blur', function () {
      const val = parseFloat(this.value);
      if (!isNaN(val)) this.value = val.toFixed(2);
    });
  });

  // ── Form submit loading state ────────────────────────────
  document.querySelectorAll('form[data-loading]').forEach(form => {
    form.addEventListener('submit', function () {
      const btn = this.querySelector('[type="submit"]');
      if (btn) {
        btn.disabled = true;
        const orig = btn.innerHTML;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';
        setTimeout(() => { btn.disabled = false; btn.innerHTML = orig; }, 10000);
      }
    });
  });

  // ── Countdown timer ──────────────────────────────────────
  document.querySelectorAll('[data-countdown]').forEach(el => {
    const target = new Date(el.dataset.countdown);
    const update = () => {
      const diff = target - new Date();
      if (diff <= 0) { el.textContent = 'Event has started!'; return; }
      const d = Math.floor(diff / 86400000);
      const h = Math.floor((diff % 86400000) / 3600000);
      const m = Math.floor((diff % 3600000) / 60000);
      const s = Math.floor((diff % 60000) / 1000);
      el.textContent = `${d}d ${h}h ${m}m ${s}s`;
    };
    update();
    setInterval(update, 1000);
  });

  // ── Gallery lightbox (simple) ────────────────────────────
  document.querySelectorAll('.gallery-item').forEach(item => {
    item.addEventListener('click', () => {
      const img = item.querySelector('img');
      if (!img) return;
      const modal = document.createElement('div');
      modal.style.cssText = 'position:fixed;inset:0;background:rgba(0,0,0,.85);z-index:9999;display:flex;align-items:center;justify-content:center;cursor:pointer;';
      modal.innerHTML = `<img src="${img.src}" style="max-width:90vw;max-height:90vh;border-radius:8px;box-shadow:0 20px 60px rgba(0,0,0,.5)">`;
      modal.addEventListener('click', () => modal.remove());
      document.body.appendChild(modal);
    });
  });

  // ── Tabs: Remember active tab ────────────────────────────
  const tabLinks = document.querySelectorAll('[data-bs-toggle="tab"]');
  tabLinks.forEach(link => {
    link.addEventListener('shown.bs.tab', e => {
      const hash = e.target.getAttribute('href');
      if (hash) history.replaceState(null, null, hash);
    });
  });

  // Restore tab from hash
  const hash = window.location.hash;
  if (hash) {
    const tabLink = document.querySelector(`[href="${hash}"]`);
    if (tabLink) bootstrap.Tab.getOrCreateInstance(tabLink)?.show();
  }

  // ── Table sort ───────────────────────────────────────────
  document.querySelectorAll('th[data-sortable]').forEach(th => {
    th.style.cursor = 'pointer';
    th.addEventListener('click', () => {
      const table = th.closest('table');
      const tbody = table.querySelector('tbody');
      const idx   = [...th.parentElement.children].indexOf(th);
      const asc   = th.dataset.order !== 'asc';
      th.dataset.order = asc ? 'asc' : 'desc';

      [...tbody.querySelectorAll('tr')].sort((a, b) => {
        const av = a.cells[idx]?.textContent?.trim() ?? '';
        const bv = b.cells[idx]?.textContent?.trim() ?? '';
        return asc ? av.localeCompare(bv, undefined, { numeric: true }) : bv.localeCompare(av, undefined, { numeric: true });
      }).forEach(row => tbody.appendChild(row));
    });
  });

});

/* ── Global AJAX helper ─────────────────────────────────── */
window.epFetch = async (url, opts = {}) => {
  const token = document.querySelector('meta[name="csrf-token"]')?.content;
  const defaults = {
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': token,
      'Accept': 'application/json',
    },
  };
  const res = await fetch(url, { ...defaults, ...opts, headers: { ...defaults.headers, ...(opts.headers ?? {}) } });
  if (!res.ok) throw new Error(await res.text());
  return res.json();
};

/* ── Toast notifications ────────────────────────────────── */
window.epToast = (message, type = 'success') => {
  const container = document.getElementById('toastContainer') ?? (() => {
    const c = document.createElement('div');
    c.id = 'toastContainer';
    c.style.cssText = 'position:fixed;bottom:1.5rem;right:1.5rem;z-index:11000;display:flex;flex-direction:column;gap:8px;';
    document.body.appendChild(c);
    return c;
  })();

  const icons = { success: 'check-circle-fill', error: 'exclamation-circle-fill', warning: 'exclamation-triangle-fill', info: 'info-circle-fill' };
  const colors = { success: '#22c55e', error: '#ef4444', warning: '#f59e0b', info: '#3b82f6' };

  const toast = document.createElement('div');
  toast.style.cssText = `background:#1e293b;color:#fff;padding:.75rem 1.25rem;border-radius:10px;box-shadow:0 8px 24px rgba(0,0,0,.2);display:flex;align-items:center;gap:.75rem;min-width:280px;max-width:400px;border-left:3px solid ${colors[type]};animation:fadeInUp .3s ease;font-size:.875rem;`;
  toast.innerHTML = `<i class="bi bi-${icons[type]}" style="color:${colors[type]};font-size:1.1rem;flex-shrink:0"></i><span style="flex:1">${message}</span><button onclick="this.parentElement.remove()" style="background:none;border:none;color:rgba(255,255,255,.5);cursor:pointer;padding:0;font-size:1rem;line-height:1">×</button>`;

  container.appendChild(toast);
  setTimeout(() => toast.style.animation = 'none', 300);
  setTimeout(() => { toast.style.opacity = '0'; toast.style.transition = 'opacity .3s'; setTimeout(() => toast.remove(), 300); }, 4000);
};
