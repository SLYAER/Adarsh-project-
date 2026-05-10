<?php
// =============================================
//  includes/footer.php — Reusable Footer
// =============================================
?>
<footer>
    <div class="footer-brand">Smart<span>Portal</span></div>
    <p style="margin:6px 0 4px;">Created by <strong style="color:#fff;">Adarsh Ray</strong> &mdash; BCA Project 2026</p>
    <p>Built with ❤️ using PHP, MySQL &amp; modern CSS</p>
</footer>

<!-- Particles -->
<div class="particles" id="particles"></div>

<!-- Scroll Reveal + Navbar Shrink + Particles JS -->
<script>
// Navbar scroll effect
const navbar = document.getElementById('navbar');
window.addEventListener('scroll', () => {
    navbar.classList.toggle('scrolled', window.scrollY > 40);
});

// Scroll reveal
const reveals = document.querySelectorAll('.reveal');
const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry, i) => {
        if (entry.isIntersecting) {
            entry.target.style.transitionDelay = (i * 0.08) + 's';
            entry.target.classList.add('visible');
            observer.unobserve(entry.target);
        }
    });
}, { threshold: 0.1 });
reveals.forEach(el => observer.observe(el));

// Floating particles
const container = document.getElementById('particles');
if (container) {
    for (let i = 0; i < 30; i++) {
        const p = document.createElement('div');
        p.className = 'particle';
        p.style.cssText = `
            left: ${Math.random() * 100}%;
            width: ${Math.random() * 4 + 1}px;
            height: ${Math.random() * 4 + 1}px;
            animation-duration: ${Math.random() * 15 + 10}s;
            animation-delay: ${Math.random() * 10}s;
            opacity: ${Math.random() * 0.5 + 0.1};
        `;
        container.appendChild(p);
    }
}

// Animated counters
document.querySelectorAll('.counter').forEach(el => {
    const target = parseInt(el.dataset.target, 10);
    let count = 0;
    const step = Math.ceil(target / 60);
    const timer = setInterval(() => {
        count = Math.min(count + step, target);
        el.textContent = count.toLocaleString();
        if (count >= target) clearInterval(timer);
    }, 25);
});
</script>
