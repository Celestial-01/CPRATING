import {
    AtSign,
    BookOpen,
    Building2,
    createIcons,
    Eye,
    EyeOff,
    GraduationCap,
    KeyRound,
    Laptop,
    Mail,
    Smartphone,
    Sparkle,
} from 'lucide';

const iconSet = {
    AtSign,
    BookOpen,
    Building2,
    Eye,
    EyeOff,
    GraduationCap,
    KeyRound,
    Laptop,
    Mail,
    Smartphone,
    Sparkle,
};

const renderIcons = () => {
    createIcons({ icons: iconSet });
};

const getPasswordInput = (toggle) => {
    const inputId = toggle.getAttribute('aria-controls');

    return inputId ? document.getElementById(inputId) : null;
};

const updatePasswordToggle = (toggle, input) => {
    const isPassword = input.type === 'password';

    input.type = isPassword ? 'text' : 'password';
    toggle.setAttribute(
        'aria-label',
        isPassword ? 'ซ่อนรหัสผ่าน' : 'แสดงรหัสผ่าน'
    );
    toggle.innerHTML = `<i data-lucide="${isPassword ? 'eye-off' : 'eye'}" class="size-5"></i>`;
    renderIcons();
};

const initializePasswordToggles = () => {
    document.querySelectorAll('[data-password-toggle]').forEach((toggle) => {
        toggle.addEventListener('click', () => {
            const input = getPasswordInput(toggle);

            if (input) {
                updatePasswordToggle(toggle, input);
            }
        });
    });
};

const initializeAuth = () => {
    renderIcons();
    initializePasswordToggles();
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeAuth, { once: true });
} else {
    initializeAuth();
}
