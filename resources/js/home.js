import {
    ArrowLeft,
    ArrowRight,
    ArrowUp,
    ArrowUpRight,
    Archive,
    BookOpen,
    Bookmark,
    ChevronDown,
    EyeOff,
    GraduationCap,
    Monitor,
    Pause,
    Play,
    Plus,
    Search,
    Star,
    ThumbsUp,
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

const showToast = createToast();

document.documentElement.classList.add('js');

const iconSet = {
    ArrowLeft,
    ArrowRight,
    ArrowUp,
    ArrowUpRight,
    Archive,
    BookOpen,
    Bookmark,
    ChevronDown,
    EyeOff,
    GraduationCap,
    Monitor,
    Pause,
    Play,
    Plus,
    Search,
    Star,
    ThumbsUp,
};

const select = (selector, root = document) => root?.querySelector(selector) ?? null;
const selectAll = (selector, root = document) => root ? [...root.querySelectorAll(selector)] : [];
const getRailStep = (rail, card) => {
    if (!card) return 0;

    const styles = window.getComputedStyle(rail);
    const gap = Number.parseFloat(styles.columnGap || styles.gap) || 0;

    return card.getBoundingClientRect().width + gap;
};

const animateCount = (element) => {
    const rawValue = element.dataset.count;
    const target = Number.parseFloat(rawValue);
    const decimals = (rawValue.split('.')[1] || '').length;

    if (prefersReducedMotion()) {
        element.textContent = target.toFixed(decimals);
        return;
    }

    const startedAt = performance.now();
    const tick = (now) => {
        const progress = Math.min((now - startedAt) / 900, 1);
        element.textContent = (target * (1 - ((1 - progress) ** 3))).toFixed(decimals);

        if (progress < 1) window.requestAnimationFrame(tick);
    };

    window.requestAnimationFrame(tick);
};

const setHighlightAnimated = (card, active) => {
    card.querySelectorAll('[data-highlight-progress]').forEach((element) => {
        const target = Number(element.dataset.highlightProgress || 0);

        element.classList.toggle('is-filled', active);
        element.closest('[role="progressbar"]')?.setAttribute(
            'aria-valuenow',
            String(active ? target : 0),
        );
    });

    card.querySelectorAll('[data-count]').forEach((element) => {
        if (active) animateCount(element);
        else element.textContent = element.dataset.countStart || element.dataset.count;
    });
};

const initializeScrollChrome = (page) => {
    const hero = select('#top', page);
    const progress = select('#scroll-progress i');
    const topButton = select('#back-to-top');
    const statement = select('#home-statement', page);
    const statementWords = [];

    if (statement) {
        const fragment = document.createDocumentFragment();
        const text = page.dataset.homeStatementCopy || statement.getAttribute('aria-label') || '';
        const words = text.split(' ');

        words.forEach((word, index) => {
            const isKeyword = word.startsWith('*');
            const span = document.createElement('span');
            span.className = `statement-word${isKeyword ? ' k' : ''}`;
            span.textContent = word.replace('*', '');
            span.setAttribute('aria-hidden', 'true');
            fragment.append(span);
            statementWords.push(span);
            if (index < words.length - 1) fragment.append(' ');
        });

        statement.replaceChildren(fragment);
    }

    const update = () => {
        const documentHeight = document.documentElement.scrollHeight - window.innerHeight || 1;
        if (progress) {
            progress.style.transform = `scaleX(${Math.min(window.scrollY / documentHeight, 1)})`;
        }
        if (topButton) {
            topButton.classList.toggle('is-visible', window.scrollY >= window.innerHeight * 0.8);
        }

        if (!statement || !statementWords.length) return;

        const bounds = statement.getBoundingClientRect();
        const ratio =
            (window.innerHeight * 0.8 - bounds.top) /
            (bounds.height + window.innerHeight * 0.2);
        const visibleWords = Math.ceil(statementWords.length * Math.max(0, Math.min(1, ratio)));

        statementWords.forEach((word, index) => {
            word.classList.toggle('is-active', index < visibleWords);
        });
    };

    hero?.addEventListener('pointermove', (event) => {
        const bounds = hero.getBoundingClientRect();
        hero.style.setProperty('--mx', `${event.clientX - bounds.left}px`);
        hero.style.setProperty('--my', `${event.clientY - bounds.top}px`);
    });

    window.addEventListener('scroll', update, { passive: true });
    window.addEventListener('resize', update);
    topButton?.addEventListener('click', () => {
        window.scrollTo({
            top: 0,
            behavior: prefersReducedMotion() ? 'auto' : 'smooth',
        });
    });
    update();
};

const initializeSectionReveal = (page) => {
    const sections = selectAll(':scope > section', page);

    if (!sections.length) return;

    sections.forEach((section, index) => {
        section.dataset.homeReveal = '';
        section.style.setProperty('--reveal-delay', `${Math.min(index * 70, 280)}ms`);
    });
    page.classList.add('home-reveal-ready');

    const showAll = () => {
        sections.forEach((section) => {
            section.dataset.visible = 'true';
        });
    };

    if (prefersReducedMotion() || !('IntersectionObserver' in window)) {
        showAll();
        return;
    }

    const observer = new IntersectionObserver(
        (entries, instance) => entries.forEach((entry) => {
            if (!entry.isIntersecting) return;

            entry.target.dataset.visible = 'true';
            instance.unobserve(entry.target);
        }),
        {
            threshold: 0.12,
            rootMargin: '0px 0px -8% 0px',
        },
    );

    sections.forEach((section) => observer.observe(section));
};

const initializePopularRail = (page, homeCopy) => {
    const rail = select('#popular-course-rail', page);

    if (!rail) return;

    const cards = selectAll('[data-course-card]', rail);

    if (!cards.length) return;

    const savedCodes = new Set(getSavedCodes());
    const step = () => getRailStep(rail, cards[0]);
    select('[data-home-rail-prev]', page)?.addEventListener('click', () => {
        rail.scrollBy({
            left: -step(),
            behavior: prefersReducedMotion() ? 'auto' : 'smooth',
        });
    });
    select('[data-home-rail-next]', page)?.addEventListener('click', () => {
        rail.scrollBy({
            left: step(),
            behavior: prefersReducedMotion() ? 'auto' : 'smooth',
        });
    });

    cards.forEach((card) => {
        const saveButton = select('[data-save]', card);

        if (saveButton) {
            saveButton.setAttribute('aria-pressed', String(savedCodes.has(saveButton.dataset.save)));
        }

        saveButton?.addEventListener('click', (event) => {
            event.preventDefault();
            event.stopPropagation();

            const code = saveButton.dataset.save;
            const saved = toggleSaved(code);
            saveButton.setAttribute('aria-pressed', String(saved));
            showToast(formatCopy(saved ? homeCopy.saved_course : homeCopy.unsaved_course, { course: code }));

            animatePress(saveButton, 1.35);
        });

        applyTilt(card);
    });
};

const initializeHighlights = (page, homeCopy) => {
    const rail = select('#home-highlight-rail', page);

    if (!rail) return;

    const cards = selectAll('[data-home-highlight]', rail);
    const pause = select('[data-home-highlight-toggle]', page);
    const previous = select('[data-home-highlight-prev]', page);
    const next = select('[data-home-highlight-next]', page);

    if (!cards.length) return;

    let playing = !prefersReducedMotion();
    let hovering = false;
    let visible = false;
    let index = 0;

    const step = () => getRailStep(rail, cards[0]);
    const current = () => Math.max(
        0,
        Math.min(cards.length - 1, Math.round(rail.scrollLeft / step())),
    );
    const goTo = (nextIndex) => {
        index = Math.max(0, Math.min(cards.length - 1, nextIndex));
        rail.scrollTo({
            left: index * step(),
            behavior: prefersReducedMotion() ? 'auto' : 'smooth',
        });
    };
    const updateToggle = () => {
        if (!pause) return;
        pause.setAttribute('aria-label', playing ? homeCopy.pause : homeCopy.play);
        pause.replaceChildren();
        const icon = document.createElement('i');
        icon.dataset.lucide = playing ? 'pause' : 'play';
        icon.className = 'ic size-4';
        icon.setAttribute('aria-hidden', 'true');
        pause.append(icon);
        renderIcons(iconSet);
    };

    pause?.addEventListener('click', () => {
        playing = !playing;
        updateToggle();
    });
    previous?.addEventListener('click', () => goTo(current() - 1));
    next?.addEventListener('click', () => goTo(current() + 1));
    rail.addEventListener('scroll', () => {
        index = current();
    }, { passive: true });
    rail.addEventListener('mouseenter', () => {
        hovering = true;
    });
    rail.addEventListener('mouseleave', () => {
        hovering = false;
    });
    rail.addEventListener('focusin', () => {
        hovering = true;
    });
    rail.addEventListener('focusout', () => {
        hovering = false;
    });

    if ('IntersectionObserver' in window) {
        new IntersectionObserver(([entry]) => {
            visible = entry.isIntersecting;
        }, { threshold: 0.3 }).observe(rail);
        const observer = new IntersectionObserver(
            (entries) => entries.forEach((entry) => {
                setHighlightAnimated(entry.target, entry.isIntersecting);
            }),
            { root: rail, threshold: 0.35 },
        );
        cards.forEach((card) => observer.observe(card));
    } else {
        visible = true;
        cards.forEach((card) => setHighlightAnimated(card, true));
    }

    window.setInterval(() => {
        if (!playing || hovering || !visible || document.hidden) return;
        goTo(index >= cards.length - 1 ? 0 : index + 1);
    }, 4500);

    updateToggle();
};

const initializeReviews = (page) => {
    const reviews = selectAll('.review-card', page);

    if (!reviews.length) return;
    if (prefersReducedMotion() || !('IntersectionObserver' in window)) {
        reviews.forEach((review) => review.classList.add('is-visible'));
        return;
    }

    const observer = new IntersectionObserver(
        (entries, instance) => entries.forEach((entry) => {
            if (!entry.isIntersecting) return;
            entry.target.classList.add('is-visible');
            instance.unobserve(entry.target);
        }),
        { threshold: 0.25 },
    );

    reviews.forEach((review, index) => {
        review.style.transitionDelay = `${index * 90}ms`;
        observer.observe(review);
    });
};

const initializeHome = () => {
    const page = select('[data-home-hero]')?.closest('.home-page');
    if (!page) return;

    renderIcons(iconSet);
    const homeCopy = parseJson(document.querySelector('[data-home-copy]'), {});
    page.dataset.homeStatementCopy = homeCopy.statement || '';
    initializeScrollChrome(page);
    initializeSectionReveal(page);
    initializePopularRail(page, homeCopy);
    initializeHighlights(page, homeCopy);
    initializeReviews(page);
};

onDomReady(initializeHome);
