import {
    ArrowUpRight,
    BookOpen,
    Bookmark,
    ChevronDown,
    ChevronRight,
    Clock,
    CircleCheck,
    Eye,
    GraduationCap,
    Heart,
    Lock,
    MessageCircle,
    Send,
    Star,
} from 'lucide';
import { renderIcons } from './icon-renderer';
import { onDomReady } from './dom-ready';

const iconSet = {
    ArrowUpRight,
    BookOpen,
    Bookmark,
    ChevronDown,
    ChevronRight,
    Clock,
    CircleCheck,
    Eye,
    GraduationCap,
    Heart,
    Lock,
    MessageCircle,
    Send,
    Star,
};

const initializeCourseIcons = () => {
    if (!document.querySelector('[aria-labelledby="course-title"]')) return;

    renderIcons(iconSet);
};

const initializeCommentSort = () => {
    const sortControl = document.querySelector('[data-comment-sort]');

    if (!sortControl) return;

    const options = [...sortControl.querySelectorAll('[data-comment-sort-option]')];

    const setActiveOption = (activeOption) => {
        options.forEach((option) => {
            const isActive = option === activeOption;

            option.setAttribute('aria-pressed', String(isActive));
            option.setAttribute('data-active', String(isActive));
        });
    };

    sortControl.addEventListener('click', (event) => {
        const option = event.target.closest('[data-comment-sort-option]');

        if (option && sortControl.contains(option)) {
            setActiveOption(option);
        }
    });
};

onDomReady(() => {
    initializeCourseIcons();
    initializeCommentSort();
});
