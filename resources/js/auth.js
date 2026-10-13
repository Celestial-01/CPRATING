import {
    AtSign,
    BookOpen,
    Building2,
    Check,
    CircleAlert,
    Clock3,
    Eye,
    EyeOff,
    GraduationCap,
    KeyRound,
    Laptop,
    Mail,
    Smartphone,
    Sparkle,
    X,
} from 'lucide';
import { onDomReady } from './dom-ready';
import { renderIcons } from './icon-renderer';
import { initializeLegalModal } from './legal-modal';

const iconSet = {
    AtSign,
    BookOpen,
    Building2,
    Check,
    CircleAlert,
    Clock3,
    Eye,
    EyeOff,
    GraduationCap,
    KeyRound,
    Laptop,
    Mail,
    Smartphone,
    Sparkle,
    X,
};

const getPasswordInput = (toggle) => {
    const inputId = toggle.getAttribute('aria-controls');

    return inputId ? document.getElementById(inputId) : null;
};

const updatePasswordToggle = (toggle, input) => {
    const isPassword = input.type === 'password';
    const icon = document.createElement('i');

    input.type = isPassword ? 'text' : 'password';
    toggle.setAttribute('aria-label', isPassword ? 'ซ่อนรหัสผ่าน' : 'แสดงรหัสผ่าน');
    icon.dataset.lucide = isPassword ? 'eye-off' : 'eye';
    icon.className = 'size-5';
    toggle.replaceChildren(icon);
    renderIcons(iconSet);
};

const initializePasswordToggles = () => {
    document.querySelectorAll('[data-password-toggle]').forEach((toggle) => {
        toggle.addEventListener('click', () => {
            const input = getPasswordInput(toggle);

            if (!input) return;

            updatePasswordToggle(toggle, input);
        });
    });
};

const initializeEmailValidation = () => {
    document.querySelectorAll('[data-auth-email-input]').forEach((input) => {
        input.addEventListener('invalid', () => {
            const message = input.validity.valueMissing
                ? 'กรุณากรอกอีเมลมหาวิทยาลัย'
                : 'กรุณากรอกรูปแบบอีเมลให้ถูกต้อง';

            input.setCustomValidity(message);
        });
        input.addEventListener('input', () => input.setCustomValidity(''));
    });
};

const initializeAuthTabs = () => {
    const tabs = document.querySelector('.auth-tabs');

    if (!tabs) return;

    const activeTab = tabs.querySelector('[aria-current="page"]')?.dataset.authTab;
    let navigationTimer;

    tabs.querySelectorAll('[data-auth-tab]').forEach((link) => {
        link.addEventListener('click', (event) => {
            if (event.defaultPrevented || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return;

            if (link.dataset.authTab === activeTab) {
                event.preventDefault();

                return;
            }

            if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

            event.preventDefault();

            if (tabs.dataset.transitioning) return;

            tabs.dataset.transitioning = 'true';
            tabs.dataset.active = link.dataset.authTab;
            navigationTimer = window.setTimeout(() => window.location.assign(link.href), 300);
        });
    });

    window.addEventListener('pageshow', () => {
        window.clearTimeout(navigationTimer);
        delete tabs.dataset.transitioning;
        tabs.dataset.active = activeTab;
    });
    window.addEventListener('pagehide', () => window.clearTimeout(navigationTimer));
};

const initializeGoogleErrorDialog = () => {
    const dialog = document.querySelector('[data-google-error-dialog]');

    if (!dialog) return;

    let closeTimer;

    const closeDialog = () => {
        if (!dialog.open || dialog.dataset.open === 'false') return;

        dialog.dataset.open = 'false';
        closeTimer = window.setTimeout(() => dialog.close(), 200);
    };

    dialog.querySelectorAll('[data-google-error-close]').forEach((button) => {
        button.addEventListener('click', closeDialog);
    });
    dialog.addEventListener('click', (event) => {
        if (event.target === dialog) closeDialog();
    });
    dialog.addEventListener('cancel', (event) => {
        event.preventDefault();
        closeDialog();
    });
    dialog.addEventListener('close', () => {
        window.clearTimeout(closeTimer);
        delete dialog.dataset.open;
        document.body.classList.remove('overflow-hidden');
        document.getElementById('email')?.focus();
    });

    dialog.showModal();
    document.body.classList.add('overflow-hidden');
    requestAnimationFrame(() => {
        if (!dialog.open || dialog.dataset.open === 'false') return;

        dialog.dataset.open = 'true';
        dialog.querySelector('[data-google-error-close]')?.focus();
    });
};

const initializeAuth = () => {
    if (!document.querySelector('.auth-scene')) return;

    renderIcons(iconSet);
    initializeAuthTabs();
    initializePasswordToggles();
    initializeEmailValidation();
    initializeGoogleErrorDialog();
    initializeLegalModal();
};

onDomReady(initializeAuth);
