import { ChevronRight, Menu, X } from 'lucide';
import { onDomReady } from './dom-ready';
import { renderIcons } from './icon-renderer';

const iconSet = { ChevronRight, Menu, X };

const initializeMobileMenu = () => {
    const toggle = document.querySelector('[data-mobile-menu-toggle]');
    const menu = document.querySelector('[data-mobile-menu]');
    const close = menu?.querySelector('[data-mobile-menu-close]');
    const overlay = document.querySelector('[data-mobile-menu-overlay]');

    if (!toggle || !menu || !close || !overlay) return;

    renderIcons(iconSet);

    const setMenuOpen = (isOpen, restoreFocus = true) => {
        if ((menu.dataset.open === 'true') === isOpen) return;

        menu.dataset.open = String(isOpen);
        overlay.dataset.open = String(isOpen);
        menu.inert = !isOpen;
        menu.setAttribute('aria-hidden', String(!isOpen));
        toggle.setAttribute('aria-expanded', String(isOpen));
        document.body.classList.toggle('overflow-hidden', isOpen);

        if (isOpen) close.focus();
        else if (restoreFocus) toggle.focus();
    };

    toggle.addEventListener('click', () => setMenuOpen(true));
    close.addEventListener('click', () => setMenuOpen(false));
    overlay.addEventListener('click', () => setMenuOpen(false));
    menu.addEventListener('click', (event) => {
        if (!event.target.closest('[data-mobile-menu-link]')) return;

        setMenuOpen(false, false);
    });
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && menu.dataset.open === 'true') setMenuOpen(false);
    });
    window.matchMedia('(min-width: 48rem)').addEventListener('change', (event) => {
        if (event.matches) setMenuOpen(false, false);
    });
};

onDomReady(initializeMobileMenu);
