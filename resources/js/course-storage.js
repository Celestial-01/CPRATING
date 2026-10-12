const STORAGE_KEY = 'cprating.course-history.v1';
const MAX_VIEWED_COURSES = 30;

const normalizeCode = (code) => String(code || '').trim().toUpperCase();

const emptyState = () => ({ viewed: [], saved: [] });

const getStorage = () => {
    try {
        return window.localStorage;
    } catch {
        return null;
    }
};

const normalizeState = (value) => {
    if (!value || typeof value !== 'object') return emptyState();

    const viewed = Array.isArray(value.viewed)
        ? value.viewed
            .map((item) => {
                if (typeof item === 'string') return { code: normalizeCode(item), viewedAt: 0 };

                return {
                    code: normalizeCode(item?.code),
                    viewedAt: Number(item?.viewedAt) || 0,
                };
            })
            .filter((item) => item.code)
            .filter((item, index, items) => items.findIndex((entry) => entry.code === item.code) === index)
            .slice(0, MAX_VIEWED_COURSES)
        : [];
    const saved = Array.isArray(value.saved)
        ? value.saved
            .map(normalizeCode)
            .filter(Boolean)
            .filter((code, index, codes) => codes.indexOf(code) === index)
        : [];

    return { viewed, saved };
};

const readState = () => {
    const storage = getStorage();

    if (!storage) return emptyState();

    try {
        return normalizeState(JSON.parse(storage.getItem(STORAGE_KEY) || 'null'));
    } catch {
        return emptyState();
    }
};

const writeState = (state) => {
    const storage = getStorage();

    if (!storage) return;

    try {
        storage.setItem(STORAGE_KEY, JSON.stringify(state));
        window.dispatchEvent(new CustomEvent('course-history-change', { detail: state }));
    } catch {
        // Storage can be unavailable in private browsing or when its quota is full.
    }
};

export const getViewedCodes = () => readState().viewed.map(({ code }) => code);

export const getSavedCodes = () => readState().saved;

export const isSaved = (code) => getSavedCodes().includes(normalizeCode(code));

export const recordViewed = (code) => {
    const normalizedCode = normalizeCode(code);

    if (!normalizedCode) return;

    const state = readState();
    state.viewed = [
        { code: normalizedCode, viewedAt: Date.now() },
        ...state.viewed.filter((item) => item.code !== normalizedCode),
    ].slice(0, MAX_VIEWED_COURSES);
    writeState(state);
};

export const toggleSaved = (code) => {
    const normalizedCode = normalizeCode(code);

    if (!normalizedCode) return false;

    const state = readState();
    const saved = state.saved.includes(normalizedCode);

    state.saved = saved
        ? state.saved.filter((item) => item !== normalizedCode)
        : [normalizedCode, ...state.saved];
    writeState(state);

    return !saved;
};

export const initializeCourseLinkTracking = () => {
    document.addEventListener('click', (event) => {
        const link = event.target.closest('[data-course-link]');

        if (link) recordViewed(link.dataset.courseLink);
    });
};
