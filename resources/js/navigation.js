import { ChevronDown, ChevronRight, House, LayoutDashboard, LogOut, Menu, UserRound, X } from 'lucide';
import { onDomReady } from './dom-ready';
import { renderIcons } from './icon-renderer';
import { prefersReducedMotion } from './utils';

const iconSet = { ChevronDown, ChevronRight, House, LayoutDashboard, LogOut, Menu, UserRound, X };

const initializeMobileMenu = () => {
    const toggle = document.querySelector('[data-mobile-menu-toggle]');
    const menu = document.querySelector('[data-mobile-menu]');
    const close = menu?.querySelector('[data-mobile-menu-close]');
    const overlay = document.querySelector('[data-mobile-menu-overlay]');

    if (!toggle || !menu || !close || !overlay) return;

    let previouslyFocusedElement = null;

    const setMenuOpen = (isOpen, restoreFocus = true) => {
        if ((menu.dataset.open === 'true') === isOpen) return;

        if (isOpen) previouslyFocusedElement = document.activeElement;

        menu.dataset.open = String(isOpen);
        overlay.dataset.open = String(isOpen);
        menu.inert = !isOpen;
        menu.setAttribute('aria-hidden', String(!isOpen));
        overlay.setAttribute('aria-hidden', String(!isOpen));
        toggle.setAttribute('aria-expanded', String(isOpen));
        toggle.setAttribute('aria-label', isOpen ? 'ปิดเมนู' : 'เปิดเมนู');
        document.body.classList.toggle('overflow-hidden', isOpen);

        if (isOpen) {
            close.focus();

            return;
        }

        if (restoreFocus) {
            (previouslyFocusedElement ?? toggle).focus();
            previouslyFocusedElement = null;
        }
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
    const desktopQuery = window.matchMedia('(min-width: 861px)');
    const closeOnDesktop = (event) => {
        if (event.matches) setMenuOpen(false, false);
    };

    if (desktopQuery.addEventListener) {
        desktopQuery.addEventListener('change', closeOnDesktop);
    } else {
        desktopQuery.addListener(closeOnDesktop);
    }
};

const initializeUserMenu = () => {
    const root = document.querySelector('[data-user-menu]');
    const toggle = root?.querySelector('[data-user-menu-toggle]');
    const panel = root?.querySelector('[data-user-menu-panel]');

    if (!root || !toggle || !panel) return;

    const setMenuOpen = (isOpen) => {
        panel.classList.toggle('hidden', !isOpen);
        panel.setAttribute('aria-hidden', String(!isOpen));
        toggle.setAttribute('aria-expanded', String(isOpen));
    };

    toggle.addEventListener('click', () => {
        setMenuOpen(toggle.getAttribute('aria-expanded') !== 'true');
    });

    root.addEventListener('click', (event) => {
        if (event.target.closest('[role="menuitem"]')) setMenuOpen(false);
    });

    document.addEventListener('click', (event) => {
        if (!root.contains(event.target)) setMenuOpen(false);
    });

    document.addEventListener('keydown', (event) => {
        if (event.key !== 'Escape' || toggle.getAttribute('aria-expanded') !== 'true') return;

        setMenuOpen(false);
        toggle.focus();
    });
};

const initializeHomeNavbar = () => {
    const navbar = document.querySelector('[data-home-navbar]');

    if (!navbar) return;

    let frame = 0;
    let previousState = null;

    const updateNavbarVisibility = () => {
        const isVisible = window.scrollY > 48;

        if (isVisible === previousState) return;

        previousState = isVisible;
        navbar.dataset.scrolled = String(isVisible);
        navbar.inert = !isVisible;
        navbar.setAttribute('aria-hidden', String(!isVisible));
    };

    const scheduleUpdate = () => {
        if (frame) return;

        frame = window.requestAnimationFrame(() => {
            frame = 0;
            updateNavbarVisibility();
        });
    };

    updateNavbarVisibility();
    window.addEventListener('scroll', scheduleUpdate, { passive: true });
};

const initializeHomeNavigation = () => {
    const hero = document.querySelector('[data-home-hero]');
    const homeLinks = document.querySelectorAll('[data-home-nav-link]');

    if (!hero || !homeLinks.length) return;

    homeLinks.forEach((link) => {
        link.addEventListener('click', (event) => {
            event.preventDefault();

            hero.scrollIntoView({
                behavior: prefersReducedMotion() ? 'auto' : 'smooth',
                block: 'start',
            });
        });
    });
};

onDomReady(() => {
    renderIcons(iconSet);
    initializeMobileMenu();
    initializeHomeNavbar();
    initializeHomeNavigation();
    initializeUserMenu();
});
