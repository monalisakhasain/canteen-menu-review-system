// script.js — Main JavaScript for Canteen Menu & Review System

document.addEventListener('DOMContentLoaded', function () {

    // ── Mobile Hamburger Menu ────────────────────
    const hamburger = document.getElementById('hamburger');
    const mobileNav = document.getElementById('mobileNav');

    if (hamburger && mobileNav) {
        hamburger.addEventListener('click', function (e) {
            e.stopPropagation();
            const isOpen = mobileNav.classList.toggle('open');
            hamburger.setAttribute('aria-expanded', isOpen);
            const spans = hamburger.querySelectorAll('span');
            if (isOpen) {
                spans[0].style.transform = 'translateY(7.5px) rotate(45deg)';
                spans[1].style.opacity   = '0';
                spans[2].style.transform = 'translateY(-7.5px) rotate(-45deg)';
            } else {
                spans[0].style.transform = '';
                spans[1].style.opacity   = '';
                spans[2].style.transform = '';
            }
        });

        mobileNav.querySelectorAll('.nav-link').forEach(function (link) {
            link.addEventListener('click', function () {
                mobileNav.classList.remove('open');
                const spans = hamburger.querySelectorAll('span');
                spans[0].style.transform = '';
                spans[1].style.opacity   = '';
                spans[2].style.transform = '';
            });
        });

        document.addEventListener('click', function (e) {
            if (mobileNav.classList.contains('open') && !mobileNav.contains(e.target)) {
                mobileNav.classList.remove('open');
                const spans = hamburger.querySelectorAll('span');
                spans[0].style.transform = '';
                spans[1].style.opacity   = '';
                spans[2].style.transform = '';
            }
        });
    }

    // ── Scroll Reveal Animation ──────────────────
    const revealEls = document.querySelectorAll('.reveal');
    if (revealEls.length > 0) {
        const observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.12 });

        revealEls.forEach(function (el) { observer.observe(el); });
    }

    // ── Dish Card Filter (Menu Page) ─────────────
    const filterBtns = document.querySelectorAll('.filter-btn');
    const dishCards  = document.querySelectorAll('.dish-card');

    if (filterBtns.length > 0) {
        filterBtns.forEach(function (btn) {
            btn.addEventListener('click', function () {
                filterBtns.forEach(function (b) { b.classList.remove('active'); });
                btn.classList.add('active');

                const filter = btn.dataset.filter;
                dishCards.forEach(function (card) {
                    if (filter === 'all' || card.dataset.category === filter) {
                        card.style.display = 'flex';
                        card.style.animation = 'fadeInUp 0.4s ease';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        });
    }

    // ── Star Rating Hover Labels ─────────────────
    const starLabels = {1:'Poor',2:'Fair',3:'Good',4:'Great',5:'Excellent'};
    const ratingInputs = document.querySelectorAll('.star-rating-input input');
    ratingInputs.forEach(function (input) {
        input.addEventListener('change', function () {
            const hint = document.getElementById('ratingHint');
            if (hint) hint.textContent = starLabels[this.value] || '';
        });
    });

    // ── Auto-dismiss Alerts ──────────────────────
    setTimeout(function () {
        document.querySelectorAll('.alert').forEach(function (alert) {
            alert.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-10px)';
            setTimeout(function () { alert.remove(); }, 500);
        });
    }, 4000);

    // ── Smooth stagger for cards ─────────────────
    document.querySelectorAll('.dish-card, .feature-card').forEach(function (card, i) {
        card.style.animationDelay = (i * 0.08) + 's';
        card.classList.add('fade-in');
    });

});
