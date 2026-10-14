import { ArrowLeft } from 'lucide';
import { onDomReady } from './dom-ready';
import { renderIcons } from './icon-renderer';
import { formatCopy, parseJson } from './utils';

const iconSet = { ArrowLeft };

const creditValue = (creditString) => Number.parseInt(String(creditString), 10) || 0;

const initializePlansPage = () => {
    const page = document.querySelector('[data-plans-page]');

    if (!page) return;

    renderIcons(iconSet);

    const plans = parseJson(document.querySelector('[data-study-plans]'), {});
    const years = parseJson(document.querySelector('[data-plan-years]'), [])
        .map(Number)
        .filter(Number.isFinite);
    const copy = parseJson(document.querySelector('[data-plan-copy]'), {});
    const selection = page.querySelector('[data-plan-selection]');
    const detail = page.querySelector('[data-plan-detail]');
    const majorGrid = page.querySelector('[data-major-grid]');
    const majorTitle = page.querySelector('[data-plan-major-title]');
    const yearTabs = page.querySelector('[data-plan-year-tabs]');
    const termList = page.querySelector('[data-plan-term-list]');
    const back = page.querySelector('[data-plan-back]');
    const yearTemplate = document.querySelector('[data-plan-year-template]');
    const termTemplate = document.querySelector('[data-plan-term-template]');
    const courseRowTemplate = document.querySelector('[data-plan-course-row-template]');

    if (!selection || !detail || !majorGrid || !majorTitle || !yearTabs || !termList
        || !back || !yearTemplate || !termTemplate || !courseRowTemplate) return;

    const majorCards = [...majorGrid.querySelectorAll('[data-major-key]')];
    let activeMajor = null;
    let activeYear = years[0];

    const availableYears = years;
    const firstYear = availableYears[0];
    const currentMajor = (key) => majorCards.find((card) => card.dataset.majorKey === key);
    const normalizeYear = (year) => {
        const requestedYear = Number(year);

        return availableYears.includes(requestedYear) ? requestedYear : firstYear;
    };
    const updateUrl = () => {
        const url = new URL(window.location.href);

        if (!activeMajor) {
            url.searchParams.delete('major');
            url.searchParams.delete('year');
        } else {
            url.searchParams.set('major', activeMajor);
            url.searchParams.set('year', String(activeYear));
        }

        window.history.replaceState({}, '', url);
    };

    const createCourseRow = (course) => {
        const row = courseRowTemplate.content.firstElementChild.cloneNode(true);
        const englishName = row.querySelector('[data-plan-course-english]');

        row.querySelector('[data-plan-course-code]').textContent = course.code ?? '';
        row.querySelector('[data-plan-course-name]').textContent = course.name ?? '';
        row.querySelector('[data-plan-course-credits]').textContent = course.credits ?? '';
        row.querySelector('[data-plan-course-prerequisite]').textContent = course.prerequisite ?? '';
        englishName.textContent = course.english_name ?? '';
        englishName.hidden = !course.english_name;

        return row;
    };

    const createTerm = (term, termIndex, accumulatedCredits) => {
        const article = termTemplate.content.firstElementChild.cloneNode(true);
        const body = article.querySelector('[data-plan-term-body]');
        const registeredRow = article.querySelector('[data-plan-registered-row]');
        const registeredCredits = term.reduce(
            (total, course) => total + creditValue(course.credits),
            0,
        );

        article.querySelector('[data-plan-term-heading]').textContent = formatCopy(
            copy.term_heading,
            { year: activeYear, term: termIndex + 1 },
        );
        article.querySelector('[data-plan-registered-credits]').textContent = String(registeredCredits);
        article.querySelector('[data-plan-accumulated-credits]').textContent = String(
            accumulatedCredits + registeredCredits,
        );

        term.forEach((course) => body.insertBefore(createCourseRow(course), registeredRow));

        return { article, registeredCredits };
    };

    const renderTerms = () => {
        const terms = plans[activeMajor]?.[activeYear] || [];
        let accumulatedCredits = 0;

        availableYears
            .filter((year) => year < activeYear)
            .forEach((year) => {
                (plans[activeMajor]?.[year] || []).forEach((term) => {
                    accumulatedCredits += term.reduce(
                        (total, course) => total + creditValue(course.credits),
                        0,
                    );
                });
            });

        const termElements = terms.map((term, termIndex) => {
            const renderedTerm = createTerm(term, termIndex, accumulatedCredits);

            accumulatedCredits += renderedTerm.registeredCredits;

            return renderedTerm.article;
        });

        termList.replaceChildren(...termElements);
    };

    const renderYearTabs = () => {
        const tabs = availableYears.map((year) => {
            const tab = yearTemplate.content.firstElementChild.cloneNode(true);

            tab.dataset.planYear = String(year);
            tab.setAttribute('aria-pressed', String(year === activeYear));
            tab.textContent = formatCopy(copy.year_label, { year });

            return tab;
        });

        yearTabs.replaceChildren(...tabs);
    };

    const renderDetail = () => {
        const card = currentMajor(activeMajor);

        if (!card || !plans[activeMajor]) return;

        majorTitle.textContent = card.querySelector('b, strong')?.textContent || '';
        renderYearTabs();
        renderTerms();
        updateUrl();
    };

    const openMajor = (key, year = firstYear) => {
        if (!plans[key] || firstYear === undefined) return;

        activeMajor = key;
        activeYear = normalizeYear(year);
        selection.hidden = true;
        detail.hidden = false;
        renderDetail();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    };

    const closeDetail = () => {
        activeMajor = null;
        selection.hidden = false;
        detail.hidden = true;
        updateUrl();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    };

    majorGrid.addEventListener('click', (event) => {
        const card = event.target.closest('[data-major-key]');

        if (card) openMajor(card.dataset.majorKey);
    });
    yearTabs.addEventListener('click', (event) => {
        const tab = event.target.closest('[data-plan-year]');

        if (!tab) return;

        activeYear = normalizeYear(tab.dataset.planYear);
        renderDetail();
    });
    back.addEventListener('click', closeDetail);

    const params = new URLSearchParams(window.location.search);

    if (params.has('major')) openMajor(params.get('major'), params.get('year'));
};

onDomReady(initializePlansPage);
