export const parseJson = (element, fallback) => {
    try {
        return JSON.parse(element?.textContent || '') || fallback;
    } catch {
        return fallback;
    }
};

export const formatCopy = (template, values) => Object.entries(values)
    .reduce((text, [key, value]) => String(text ?? '').replaceAll(`:${key}`, String(value)), template);

export const prefersReducedMotion = () => window.matchMedia('(prefers-reduced-motion: reduce)').matches;

export const canHover = () => window.matchMedia('(hover: hover)').matches;

export const animatePress = (element, scale = 1.25) => {
    if (!element || prefersReducedMotion()) return;

    element.animate(
        [
            { transform: 'scale(1)' },
            { transform: `scale(${scale})` },
            { transform: 'scale(1)' },
        ],
        { duration: 300 },
    );
};

export const applyTilt = (element, rotateX = 6, rotateY = 8) => {
    if (!element || prefersReducedMotion() || !canHover()) return;

    element.addEventListener('pointermove', (event) => {
        const bounds = element.getBoundingClientRect();
        const horizontalOffset = (event.clientX - bounds.left) / bounds.width - 0.5;
        const verticalOffset = (event.clientY - bounds.top) / bounds.height - 0.5;

        element.style.setProperty('--tilt-x', `${-verticalOffset * rotateX}deg`);
        element.style.setProperty('--tilt-y', `${horizontalOffset * rotateY}deg`);
    });
    element.addEventListener('pointerleave', () => {
        element.style.setProperty('--tilt-x', '0deg');
        element.style.setProperty('--tilt-y', '0deg');
    });
};

export const createToast = () => {
    let timer;

    return (message) => {
        const toast = document.querySelector('#toast');

        if (!toast) return;

        toast.textContent = message;
        toast.classList.add('is-visible');
        window.clearTimeout(timer);
        timer = window.setTimeout(() => toast.classList.remove('is-visible'), 2200);
    };
};
