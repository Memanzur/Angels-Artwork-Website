/* ============================================================
   Angel's Artwork — Main JS
   ============================================================ */
(function () {
  'use strict';

  // ── Floating particles ──────────────────────────────────────
  function createParticles() {
    var container = document.querySelector('.particles');
    if (!container) return;
    var count = window.innerWidth < 768 ? 15 : 30;
    for (var i = 0; i < count; i++) {
      var p = document.createElement('div');
      p.className = 'particle';
      var size = Math.random() * 6 + 3;
      p.style.width = size + 'px';
      p.style.height = size + 'px';
      p.style.left = Math.random() * 100 + '%';
      p.style.animationDuration = Math.random() * 18 + 14 + 's';
      p.style.animationDelay = Math.random() * 18 + 's';
      container.appendChild(p);
    }
  }

  // ── Nav scroll ──────────────────────────────────────────────
  function initNav() {
    var nav = document.getElementById('nav');
    if (!nav) return;
    window.addEventListener('scroll', function () {
      nav.classList.toggle('scrolled', window.scrollY > 60);
    });
  }

  // ── Mobile menu ─────────────────────────────────────────────
  function initMobileMenu() {
    var toggle = document.getElementById('nav-toggle');
    var links = document.getElementById('nav-links');
    if (!toggle || !links) return;
    toggle.addEventListener('click', function () {
      toggle.classList.toggle('active');
      links.classList.toggle('open');
    });
    links.querySelectorAll('a').forEach(function (a) {
      a.addEventListener('click', function () {
        toggle.classList.remove('active');
        links.classList.remove('open');
      });
    });
  }

  // ── Fade in on scroll ──────────────────────────────────────
  function initFadeIn() {
    var targets = document.querySelectorAll(
      '.section-header, .art-card, .featured-grid, .about-inner, .contact-grid, .collection-cta'
    );
    targets.forEach(function (el) { el.classList.add('fade-in'); });

    if (!('IntersectionObserver' in window)) {
      targets.forEach(function (el) { el.classList.add('visible'); });
      return;
    }

    var observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add('visible');
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });

    targets.forEach(function (el) { observer.observe(el); });
  }

  // ── Gallery filters ────────────────────────────────────────
  function initFilters() {
    var buttons = document.querySelectorAll('.filter-btn');
    var items = document.querySelectorAll('.art-card');

    buttons.forEach(function (btn) {
      btn.addEventListener('click', function () {
        buttons.forEach(function (b) { b.classList.remove('active'); });
        btn.classList.add('active');
        var filter = btn.getAttribute('data-filter');

        items.forEach(function (item) {
          if (filter === 'all' || item.getAttribute('data-category') === filter) {
            item.style.display = '';
            requestAnimationFrame(function () {
              item.style.opacity = '1';
              item.style.transform = '';
            });
          } else {
            item.style.opacity = '0';
            item.style.transform = 'scale(0.96)';
            setTimeout(function () { item.style.display = 'none'; }, 300);
          }
        });
      });
    });
  }

  // ── Lightbox ────────────────────────────────────────────────
  function initLightbox() {
    var lightbox = document.getElementById('lightbox');
    var lbImg = lightbox.querySelector('.lightbox-img');
    var lbCaption = lightbox.querySelector('.lightbox-caption');
    var lbClose = lightbox.querySelector('.lightbox-close');

    var lbBuy = lightbox.querySelector('.lightbox-buy');

    document.querySelectorAll('.art-card-img').forEach(function (el) {
      el.addEventListener('click', function () {
        var card = el.closest('.art-card');
        var img = el.querySelector('img');
        var title = card.querySelector('h3');
        var buyLink = card.querySelector('.btn-small');
        lbImg.src = img.src;
        lbImg.alt = img.alt;
        lbCaption.textContent = title ? title.textContent : '';
        if (buyLink && lbBuy) lbBuy.href = buyLink.href;
        lightbox.classList.add('active');
        document.body.style.overflow = 'hidden';
      });
    });

    function close() {
      lightbox.classList.remove('active');
      document.body.style.overflow = '';
    }

    lbClose.addEventListener('click', close);
    lightbox.addEventListener('click', function (e) {
      if (e.target === lightbox) close();
    });
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') close();
    });
  }

  // ── Contact form (handled by Formspree) ─────────────────────
  function initContactForm() {}

  // ── Smooth scroll ──────────────────────────────────────────
  function initSmoothScroll() {
    document.querySelectorAll('a[href^="#"]').forEach(function (a) {
      a.addEventListener('click', function (e) {
        var target = document.querySelector(a.getAttribute('href'));
        if (target) {
          e.preventDefault();
          target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
      });
    });
  }

  // ── Init ────────────────────────────────────────────────────
  document.addEventListener('DOMContentLoaded', function () {
    createParticles();
    initNav();
    initMobileMenu();
    initFadeIn();
    initFilters();
    initLightbox();
    initContactForm();
    initSmoothScroll();
  });
})();
