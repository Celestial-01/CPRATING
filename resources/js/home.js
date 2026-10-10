import { ArrowUpRight, BookOpen, Eye, Flame, Star } from 'lucide';
import { onDomReady } from './dom-ready';
import { renderIcons } from './icon-renderer';

const iconSet = { ArrowUpRight, BookOpen, Eye, Flame, Star };

const initializeHomeIcons = () => {
    if (!document.querySelector('.home-hero')) return;

    renderIcons(iconSet);
};

const initializeHomeReveal = () => {
    const elements = document.querySelectorAll('[data-home-reveal]');

    if (!elements.length || !('IntersectionObserver' in window)) return;
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) return;

            entry.target.dataset.visible = 'true';
            observer.unobserve(entry.target);
        });
    }, {
        rootMargin: '0px 0px -8% 0px',
        threshold: 0.1,
    });

    elements.forEach((element) => observer.observe(element));
    document.documentElement.classList.add('home-reveal-ready');
};

onDomReady(initializeHomeIcons);
onDomReady(initializeHomeReveal);
