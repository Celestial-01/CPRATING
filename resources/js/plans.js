import { ArrowLeft } from 'lucide';
import { onDomReady } from './dom-ready';
import { renderIcons } from './icon-renderer';

const initializePlansPage = () => {
    const page = document.querySelector('[data-plans-page]');

    if (!page) return;

    renderIcons({ ArrowLeft });

    const selection = page.querySelector('[data-plan-selection]');
    const majorGrid = page.querySelector('[data-major-grid]');
    const details = [...page.querySelectorAll('[data-plan-detail]')];

    if (!selection || !majorGrid) return;

    let activeDetail = null;
    let activeYear = null;

    const updateUrl = () => {
        const url = new URL(window.location.href);

        if (activeDetail) {
            url.searchParams.set('major', activeDetail.dataset.majorKey);
            url.searchParams.set('year', activeYear);
        } else {
            url.searchParams.delete('major');
            url.searchParams.delete('year');
        }

        window.history.replaceState({}, '', url);
    };

    const selectYear = (year) => {
        if (!activeDetail) return;

        const tabs = [...activeDetail.querySelectorAll('[data-plan-year]')];
        const requestedYear = Number(year);
        const selectedTab = tabs.find((tab) => Number(tab.dataset.planYear) === requestedYear)
            ?? tabs[0];

        if (!selectedTab) return;

        activeYear = selectedTab.dataset.planYear;

        tabs.forEach((tab) => {
            tab.setAttribute('aria-pressed', String(tab === selectedTab));
        });

        activeDetail.querySelectorAll('[data-plan-year-panel]').forEach((panel) => {
            panel.hidden = panel.dataset.planYearPanel !== activeYear;
        });

        updateUrl();
    };

    const openMajor = (majorCode, year) => {
        const selectedDetail = details.find((detail) => detail.dataset.majorKey === majorCode);

        if (!selectedDetail) return;

        activeDetail = selectedDetail;
        selection.hidden = true;
        details.forEach((detail) => {
            detail.hidden = detail !== selectedDetail;
        });
        selectYear(year);
        window.scrollTo({ top: 0, behavior: 'smooth' });
    };

    const closeDetail = () => {
        activeDetail = null;
        activeYear = null;
        selection.hidden = false;
        details.forEach((detail) => {
            detail.hidden = true;
        });
        updateUrl();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    };

    majorGrid.addEventListener('click', (event) => {
        const card = event.target.closest('[data-major-key]');

        if (card) openMajor(card.dataset.majorKey);
    });

    details.forEach((detail) => {
        detail.querySelector('[data-plan-year-tabs]')?.addEventListener('click', (event) => {
            const tab = event.target.closest('[data-plan-year]');

            if (tab) selectYear(tab.dataset.planYear);
        });
        detail.querySelector('[data-plan-back]')?.addEventListener('click', closeDetail);
    });

    const params = new URLSearchParams(window.location.search);

    if (params.has('major')) openMajor(params.get('major'), params.get('year'));
};

onDomReady(initializePlansPage);
