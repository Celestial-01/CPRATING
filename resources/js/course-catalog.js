import { ArrowUpRight, BookOpen, Bookmark, ChevronDown, ListFilter, Search, Star } from 'lucide';
import { onDomReady } from './dom-ready';
import { renderIcons } from './icon-renderer';
import { getSavedCodes, toggleSaved } from './course-storage';
import { animatePress, applyTilt, createToast, formatCopy, parseJson } from './utils';

const initializeCoursesPage = () => {
    const page = document.querySelector('[data-courses-page]');

    if (!page) return;

    renderIcons({ ArrowUpRight, BookOpen, Bookmark, ChevronDown, ListFilter, Search, Star });

    const form = page.querySelector('[data-course-filters]');
    const search = page.querySelector('[data-course-search]');
    const filter = page.querySelector('[data-course-filter]');
    const grid = page.querySelector('[data-course-grid]');
    const copy = parseJson(document.querySelector('[data-course-copy]'), {});
    const showToast = createToast();
    const savedCodes = new Set(getSavedCodes());

    grid.querySelectorAll('[data-course-card]').forEach((card) => {
        applyTilt(card);

        const button = card.querySelector('[data-course-save]');

        if (button) button.setAttribute('aria-pressed', String(savedCodes.has(button.dataset.courseSave)));
    });

    search.addEventListener('keydown', (event) => {
        if (event.key !== 'Enter') return;

        event.preventDefault();
        form.requestSubmit();
    });
    filter.addEventListener('change', () => form.requestSubmit());

    grid.addEventListener('click', (event) => {
        const button = event.target.closest('[data-course-save]');

        if (!button) return;

        event.preventDefault();
        event.stopPropagation();

        const saved = toggleSaved(button.dataset.courseSave);

        button.setAttribute('aria-pressed', String(saved));
        showToast(formatCopy(saved ? copy.saved : copy.unsaved, { course: button.dataset.courseSave }));
        animatePress(button);
    });
};

onDomReady(initializeCoursesPage);
