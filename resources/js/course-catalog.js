import {
    ArrowLeft,
    ArrowRight,
    ArrowUpRight,
    Bookmark,
    ChevronDown,
    ListFilter,
    Search,
    SearchX,
    Star,
    X,
} from 'lucide';
import { onDomReady } from './dom-ready';
import { renderIcons } from './icon-renderer';
import { getSavedCodes, toggleSaved } from './course-storage';
import {
    animatePress,
    applyTilt,
    createToast,
    formatCopy,
    parseJson,
    prefersReducedMotion,
} from './utils';

const iconSet = {
    ArrowLeft,
    ArrowRight,
    ArrowUpRight,
    Bookmark,
    ChevronDown,
    ListFilter,
    Search,
    SearchX,
    Star,
    X,
};

const initializeCoursesPage = () => {
    const page = document.querySelector('[data-courses-page]');

    if (!page) return;

    renderIcons(iconSet);

    const copy = parseJson(document.querySelector('[data-course-copy]'), {});
    const search = page.querySelector('[data-course-search]');
    const filterSelect = page.querySelector('[data-course-filter]');
    const count = page.querySelector('[data-course-count]');
    const grid = page.querySelector('[data-course-grid]');
    const pagination = page.querySelector('[data-course-pagination]');
    const empty = page.querySelector('[data-course-empty]');

    if (!search || !filterSelect || !count || !grid || !pagination || !empty) return;

    const cards = [...grid.querySelectorAll('[data-course-card]')];
    const savedCodes = new Set(getSavedCodes());
    cards.forEach((card) => {
        const button = card.querySelector('[data-course-save]');

        if (button) {
            button.setAttribute('aria-pressed', String(savedCodes.has(button.dataset.courseSave)));
        }
    });
    const parsedPageSize = Number.parseInt(page.dataset.pageSize, 10);
    const perPage = Number.isFinite(parsedPageSize) && parsedPageSize > 0
        ? parsedPageSize
        : cards.length || 1;
    let activeFilter = filterSelect.value;
    let currentPage = 1;
    const showToast = createToast();

    const pageList = (totalPages) => {
        if (totalPages <= 7) return Array.from({ length: totalPages }, (_, index) => index + 1);

        const pages = new Set([1, totalPages, currentPage - 1, currentPage, currentPage + 1]);

        if (currentPage <= 3) [2, 3, 4].forEach((pageNumber) => pages.add(pageNumber));
        if (currentPage >= totalPages - 2) {
            [totalPages - 3, totalPages - 2, totalPages - 1]
                .forEach((pageNumber) => pages.add(pageNumber));
        }

        const sortedPages = [...pages]
            .filter((pageNumber) => pageNumber >= 1 && pageNumber <= totalPages)
            .sort((first, second) => first - second);

        return sortedPages.reduce((items, pageNumber, index) => {
            if (index > 0 && pageNumber - sortedPages[index - 1] > 1) items.push('…');

            items.push(pageNumber);

            return items;
        }, []);
    };

    const renderPagination = (totalPages) => {
        pagination.hidden = totalPages <= 1;

        if (totalPages <= 1) {
            pagination.replaceChildren();
            return;
        }

        const createPageButton = ({ pageNumber, label, icon, disabled = false }) => {
            const button = document.createElement('button');

            button.type = 'button';
            button.dataset.page = String(pageNumber);
            button.setAttribute('aria-label', label);
            button.disabled = disabled;

            if (pageNumber === currentPage) button.setAttribute('aria-current', 'page');

            if (icon) {
                const iconElement = document.createElement('i');
                iconElement.dataset.lucide = icon;
                iconElement.className = 'size-4';
                iconElement.setAttribute('aria-hidden', 'true');
                button.append(iconElement);
            } else {
                button.textContent = String(pageNumber);
            }

            return button;
        };
        const items = [createPageButton({
            pageNumber: currentPage - 1,
            label: copy.previous_page,
            icon: 'arrow-left',
            disabled: currentPage === 1,
        })];

        pageList(totalPages).forEach((pageNumber) => {
            if (pageNumber === '…') {
                const ellipsis = document.createElement('span');
                ellipsis.className = 'el';
                ellipsis.setAttribute('aria-hidden', 'true');
                ellipsis.textContent = '…';
                items.push(ellipsis);
                return;
            }

            items.push(createPageButton({
                pageNumber,
                label: formatCopy(copy.page, { page: pageNumber }),
            }));
        });
        items.push(createPageButton({
            pageNumber: currentPage + 1,
            label: copy.next_page,
            icon: 'arrow-right',
            disabled: currentPage === totalPages,
        }));

        pagination.replaceChildren(...items);

        renderIcons(iconSet);
    };

    const getFilteredCards = () => {
        const query = search.value.trim().toLowerCase();

        return cards.filter((card) => {
            const matchesQuery = card.dataset.search.includes(query);
            const rating = Number(card.dataset.rating);
            const matchesFilter = activeFilter === 'all'
                || (activeFilter === 'popular' ? rating >= 4.5 : card.dataset.major === activeFilter);

            return matchesQuery && matchesFilter;
        });
    };

    const applyFilters = (resetPage = false) => {
        const filteredCards = getFilteredCards();

        if (resetPage) currentPage = 1;

        const totalPages = Math.max(1, Math.ceil(filteredCards.length / perPage));
        currentPage = Math.min(currentPage, totalPages);
        const visibleCards = new Set(filteredCards.slice((currentPage - 1) * perPage, currentPage * perPage));

        cards.forEach((card) => {
            card.hidden = !visibleCards.has(card);
        });

        const first = filteredCards.length ? (currentPage - 1) * perPage + 1 : 0;
        const last = filteredCards.length ? first + visibleCards.size - 1 : 0;
        count.textContent = filteredCards.length
            ? formatCopy(copy.count, { first, last, total: filteredCards.length })
            : copy.empty;
        empty.hidden = filteredCards.length > 0;
        renderPagination(totalPages);
    };

    search.value = page.dataset.initialQuery || search.value;
    search.addEventListener('input', () => applyFilters(true));
    filterSelect.addEventListener('change', () => {
        activeFilter = filterSelect.value;
        applyFilters(true);
    });
    pagination.addEventListener('click', (event) => {
        const button = event.target.closest('[data-page]');

        if (!button || button.disabled) return;

        currentPage = Number(button.dataset.page);
        applyFilters();
        page.querySelector('.tools')?.scrollIntoView({
            behavior: prefersReducedMotion() ? 'auto' : 'smooth',
            block: 'start',
        });
    });
    grid.addEventListener('click', (event) => {
        const button = event.target.closest('[data-course-save]');

        if (!button) return;

        event.preventDefault();
        event.stopPropagation();
        const saved = toggleSaved(button.dataset.courseSave);
        button.setAttribute('aria-pressed', String(saved));
        showToast(
            saved
                ? formatCopy(copy.saved, { course: button.dataset.courseSave })
                : formatCopy(copy.unsaved, { course: button.dataset.courseSave }),
        );

        animatePress(button);
    });

    cards.forEach((card) => {
        applyTilt(card);
    });

    applyFilters(true);
};

onDomReady(initializeCoursesPage);
