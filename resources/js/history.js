import {
    ArrowLeft,
    ArrowRight,
    ArrowUpRight,
    BookOpen,
    Bookmark,
    Clock,
} from 'lucide';
import { onDomReady } from './dom-ready';
import { renderIcons } from './icon-renderer';
import { getSavedCodes, toggleSaved } from './course-storage';
import { createToast, formatCopy, parseJson } from './utils';

const iconSet = { ArrowLeft, ArrowRight, ArrowUpRight, BookOpen, Bookmark, Clock };
const PREVIEW_LIMIT = 4;
const sectionNames = ['viewed', 'saved'];

const getActiveSection = () => {
    const section = new URL(window.location.href).searchParams.get('section');

    return sectionNames.includes(section) ? section : null;
};

const getRailStep = (grid) => {
    const card = grid.querySelector('[data-course-card]:not([hidden])');

    if (!card) return grid.clientWidth;

    const gap = Number.parseFloat(window.getComputedStyle(grid).gap) || 0;

    return card.getBoundingClientRect().width + gap;
};

const updateRailControls = (grid, controls) => {
    if (!grid || !controls) return;

    const previous = controls.querySelector('[data-history-rail-prev]');
    const next = controls.querySelector('[data-history-rail-next]');
    const hasOverflow = grid.scrollWidth > grid.clientWidth + 1;

    if (previous) previous.disabled = !hasOverflow || grid.scrollLeft <= 1;
    if (next) {
        next.disabled = !hasOverflow
            || grid.scrollLeft + grid.clientWidth >= grid.scrollWidth - 1;
    }
};

const updateSaveButton = (button, saved, copy) => {
    const code = button.dataset.courseSave;

    button.setAttribute('aria-pressed', String(saved));
    button.setAttribute('aria-label', formatCopy(
        saved ? copy.unsave_label : copy.save_label,
        { course: code },
    ));
};

const initializeHistory = () => {
    const page = document.querySelector('[data-history-page]');

    if (!page) return;

    renderIcons(iconSet);

    const copy = parseJson(document.querySelector('[data-history-copy]'), {});

    const viewedGrid = page.querySelector('[data-history-viewed-grid]');
    const savedGrid = page.querySelector('[data-history-saved-grid]');
    const viewedEmpty = page.querySelector('[data-history-viewed-empty]');
    const savedEmpty = page.querySelector('[data-history-saved-empty]');
    const viewedCount = page.querySelector('[data-history-viewed-count]');
    const savedCount = page.querySelector('[data-history-saved-count]');
    const viewedSummary = page.querySelector('[data-history-viewed-summary]');
    const savedSummary = page.querySelector('[data-history-saved-summary]');
    const sections = new Map(sectionNames.map((name) => [
        name,
        page.querySelector(`[data-history-section="${name}"]`),
    ]));
    const showToast = createToast();

    if (!viewedGrid || !savedGrid || !viewedEmpty || !savedEmpty) return;

    const updateGrid = (grid, showAll) => {
        const cards = [...grid.querySelectorAll('[data-course-card]')];

        cards.forEach((card, index) => {
            card.hidden = !showAll && index >= PREVIEW_LIMIT;
        });

        return cards.length;
    };

    const updateCount = (element, summary, count, noun) => {
        if (element) element.textContent = String(count);
        if (summary) {
            summary.textContent = count
                ? formatCopy(copy.count, { count, noun })
                : formatCopy(copy.empty, { noun });
        }
    };

    const render = () => {
        const activeSection = getActiveSection();
        const savedCodeSet = new Set(getSavedCodes());
        const visibleViewed = updateGrid(viewedGrid, activeSection === 'viewed');
        const visibleSaved = updateGrid(savedGrid, activeSection === 'saved');

        viewedEmpty.hidden = visibleViewed > 0;
        savedEmpty.hidden = visibleSaved > 0;
        updateCount(viewedCount, viewedSummary, visibleViewed, copy.viewed_noun);
        updateCount(savedCount, savedSummary, visibleSaved, copy.saved_noun);

        page.dataset.historyFocus = activeSection || '';
        sections.forEach((section, name) => {
            if (!section) return;

            const grid = section.querySelector('.history-grid');
            const railControls = section.querySelector('[data-history-rail-controls]');
            const sectionCount = name === 'viewed' ? visibleViewed : visibleSaved;

            section.hidden = activeSection !== null && activeSection !== name;
            section.querySelector('[data-history-show]').hidden = activeSection !== null
                || sectionCount <= PREVIEW_LIMIT;
            section.querySelector('[data-history-overview]').hidden = activeSection !== name;

            if (railControls) {
                railControls.hidden = activeSection !== null || sectionCount === 0;
                updateRailControls(grid, railControls);
            }
        });

        page.querySelectorAll('[data-course-save]').forEach((button) => {
            updateSaveButton(button, savedCodeSet.has(button.dataset.courseSave), copy);
        });
        renderIcons(iconSet);
    };

    const navigate = (section) => {
        const url = new URL(window.location.href);

        if (section) url.searchParams.set('section', section);
        else url.searchParams.delete('section');

        window.history.pushState(null, '', url);
        render();
        window.scrollTo({ top: 0, behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth' });
        page.querySelector(section ? `#history-${section}-title` : '#history-title')?.focus({ preventScroll: true });
    };

    page.addEventListener('click', async (event) => {
        const saveButton = event.target.closest('[data-course-save]');

        if (saveButton) {
            event.preventDefault();
            event.stopPropagation();
            let saved;
            try {
                saved = await toggleSaved(saveButton);
            } catch {
                showToast('บันทึกวิชาไม่สำเร็จ กรุณาลองอีกครั้ง');
                return;
            }
            if (saved === null) return;
            const matchingCard = [...savedGrid.querySelectorAll('[data-course-code]')]
                .find((card) => card.dataset.courseCode === saveButton.dataset.courseSave);
            if (saved && !matchingCard) {
                const sourceCard = saveButton.closest('[data-course-code]');
                if (sourceCard) savedGrid.prepend(sourceCard.cloneNode(true));
            } else if (!saved) {
                matchingCard?.remove();
            }
            showToast(formatCopy(
                saved ? copy.saved : copy.unsaved,
                { course: saveButton.dataset.courseSave },
            ));
            render();
            return;
        }

        const showButton = event.target.closest('[data-history-show]');

        if (showButton) {
            navigate(showButton.dataset.historyShow);
            return;
        }

        if (event.target.closest('[data-history-overview]')) {
            navigate(null);
            return;
        }

        const railButton = event.target.closest(
            '[data-history-rail-prev], [data-history-rail-next]',
        );

        if (!railButton) return;

        const sectionName = railButton.dataset.historyRailPrev
            ?? railButton.dataset.historyRailNext;
        const grid = page.querySelector(
            `[data-history-section="${sectionName}"] .history-grid`,
        );

        if (!grid) return;

        grid.scrollBy({
            left: (
                railButton.hasAttribute('data-history-rail-prev') ? -1 : 1
            ) * getRailStep(grid),
            behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches
                ? 'auto'
                : 'smooth',
        });
    });

    page.addEventListener('scroll', (event) => {
        const grid = event.target.closest?.('.history-grid');

        if (!grid) return;

        updateRailControls(
            grid,
            grid.parentElement?.querySelector('[data-history-rail-controls]'),
        );
    }, true);

    window.addEventListener('popstate', render);
    render();
};

onDomReady(initializeHistory);
