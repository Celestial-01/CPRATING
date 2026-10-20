const savedCodes = new Set(JSON.parse(
    document.querySelector('[data-saved-course-codes]')?.textContent || '[]',
));
const pendingCodes = new Set();

export const getSavedCodes = () => [...savedCodes];

export const isSaved = (code) => savedCodes.has(code);

export const toggleSaved = async (button) => {
    const code = button.dataset.courseSave;
    const loginUrl = document.querySelector('[name="course-login-url"]')?.content;

    if (!document.querySelector('[data-saved-course-codes]')) {
        window.location.assign(loginUrl);
        return null;
    }

    if (!code || pendingCodes.has(code)) return null;

    const wasSaved = savedCodes.has(code);
    pendingCodes.add(code);
    button.disabled = true;

    try {
        const response = await fetch(wasSaved ? button.dataset.unsaveUrl : button.dataset.saveUrl, {
            method: wasSaved ? 'DELETE' : 'POST',
            headers: {
                Accept: 'application/json',
                'X-CSRF-TOKEN': document.querySelector('[name="csrf-token"]')?.content,
            },
            credentials: 'same-origin',
        });

        if (response.status === 401 || response.status === 419) {
            window.location.assign(loginUrl);
            return null;
        }

        if (!response.ok) throw new Error('Could not save course');

        const { saved } = await response.json();
        if (saved) savedCodes.add(code);
        else savedCodes.delete(code);

        document.querySelectorAll('[data-course-save]').forEach((saveButton) => {
            if (saveButton.dataset.courseSave === code) {
                saveButton.setAttribute('aria-pressed', String(saved));
            }
        });

        return saved;
    } finally {
        pendingCodes.delete(code);
        button.disabled = false;
    }
};
