import { ChevronRight, Menu, X } from 'lucide';
import { renderIcons } from './icon-renderer';
import { onDomReady } from './dom-ready';

const iconSet = { ChevronRight, Menu, X };

const initializeMobileMenu = () => {
    const toggle = document.querySelector('[data-mobile-menu-toggle]');
    const menu = document.querySelector('[data-mobile-menu]');
    const close = document.querySelector('[data-mobile-menu-close]');
    const overlay = document.querySelector('[data-mobile-menu-overlay]');
    const links = [...document.querySelectorAll('[data-mobile-menu-link]')];
    const content = [...document.querySelectorAll('[data-mobile-menu-content]')];
    const toggleIcon = document.querySelector('[data-mobile-menu-toggle-icon]');
    const closeIcon = document.querySelector('[data-mobile-menu-close-icon]');

    if (!toggle || !menu || !close || !overlay) return;

    renderIcons(iconSet);

    const setMenuOpen = (isOpen) => {
        menu.classList.toggle('pointer-events-none', !isOpen);
        menu.classList.toggle('pointer-events-auto', isOpen);
        menu.classList.toggle('translate-x-full', !isOpen);
        menu.classList.toggle('translate-x-0', isOpen);
        menu.classList.toggle('opacity-0', !isOpen);
        menu.classList.toggle('opacity-100', isOpen);
        content.forEach((item) => {
            item.classList.toggle('translate-y-3', !isOpen);
            item.classList.toggle('translate-y-0', isOpen);
            item.classList.toggle('opacity-0', !isOpen);
            item.classList.toggle('opacity-100', isOpen);
        });
        toggleIcon?.classList.toggle('rotate-90', isOpen);
        closeIcon?.classList.toggle('rotate-90', !isOpen);
        overlay.classList.toggle('pointer-events-none', !isOpen);
        overlay.classList.toggle('pointer-events-auto', isOpen);
        overlay.classList.toggle('opacity-0', !isOpen);
        overlay.classList.toggle('opacity-100', isOpen);
        menu.setAttribute('aria-hidden', String(!isOpen));
        toggle.setAttribute('aria-expanded', String(isOpen));
        document.body.classList.toggle('overflow-hidden', isOpen);
    };

    toggle.addEventListener('click', () => setMenuOpen(true));
    close.addEventListener('click', () => setMenuOpen(false));
    overlay.addEventListener('click', () => setMenuOpen(false));
    links.forEach((link) => link.addEventListener('click', () => setMenuOpen(false)));
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') setMenuOpen(false);
    });
};

onDomReady(initializeMobileMenu);
