import {
    AtSign,
    BookOpen,
    Building2,
    Check,
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

    input.type = isPassword ? 'text' : 'password';
    toggle.setAttribute('aria-label', isPassword ? 'ซ่อนรหัสผ่าน' : 'แสดงรหัสผ่าน');
    toggle.innerHTML = `<i data-lucide="${isPassword ? 'eye-off' : 'eye'}" class="size-5"></i>`;
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

const initializeAuth = () => {
    if (!document.querySelector('.auth-scene')) return;

    renderIcons(iconSet);
    initializePasswordToggles();
    initializeLegalModal();
};

onDomReady(initializeAuth);
