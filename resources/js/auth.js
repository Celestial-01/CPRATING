import {
    BookOpen,
    Building2,
    createIcons,
    Eye,
    EyeOff,
    GraduationCap,
    Laptop,
    Smartphone,
    Sparkle,
} from 'lucide';

const iconSet = {
    BookOpen,
    Building2,
    Eye,
    EyeOff,
    GraduationCap,
    Laptop,
    Smartphone,
    Sparkle,
};

function renderIcons() {
    createIcons({ icons: iconSet });
}

function getPasswordInput(toggle) {
    const inputId = toggle.getAttribute('aria-controls');

    return inputId ? document.getElementById(inputId) : null;
}

function updatePasswordToggle(toggle, input) {
    const isPassword = input.type === 'password';

    input.type = isPassword ? 'text' : 'password';
    toggle.setAttribute(
        'aria-label',
        isPassword ? 'ซ่อนรหัสผ่าน' : 'แสดงรหัสผ่าน'
    );
    toggle.innerHTML = `<i data-lucide="${isPassword ? 'eye-off' : 'eye'}" class="size-5"></i>`;
    renderIcons();
}

function initializePasswordToggles() {
    document.querySelectorAll('[data-password-toggle]').forEach((toggle) => {
        toggle.addEventListener('click', () => {
            const input = getPasswordInput(toggle);

            if (input) {
                updatePasswordToggle(toggle, input);
            }
        });
    });
}

function initializeAuth() {
    renderIcons();
    initializePasswordToggles();
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeAuth, { once: true });
} else {
    initializeAuth();
}
