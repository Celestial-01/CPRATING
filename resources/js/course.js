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
import { onDomReady } from './dom-ready';
import { renderIcons } from './icon-renderer';

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

const initializeCommentSort = () => {
    const sortControl = document.querySelector('[data-comment-sort]');

    if (!sortControl) return;

    const options = [...sortControl.querySelectorAll('[data-comment-sort-option]')];

    const setActiveOption = (activeOption) => {
        options.forEach((option) => {
            const isActive = option === activeOption;

            option.setAttribute('aria-pressed', String(isActive));
            option.dataset.active = String(isActive);
        });
    };

    sortControl.addEventListener('click', (event) => {
        const option = event.target.closest('[data-comment-sort-option]');

        if (!option || !sortControl.contains(option)) return;

        setActiveOption(option);
    });
};

const initializeCourse = () => {
    if (document.querySelector('[aria-labelledby="course-title"]')) {
        renderIcons(iconSet);
    }

    initializeCommentSort();
};

onDomReady(initializeCourse);
