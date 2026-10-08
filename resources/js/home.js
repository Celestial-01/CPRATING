import {
    BookOpen,
    ChevronLeft,
    ChevronRight,
    Eye,
    Flame,
    Star,
} from 'lucide';
import { renderIcons } from './icon-renderer';
import { onDomReady } from './dom-ready';

const iconSet = {
    BookOpen,
    ChevronLeft,
    ChevronRight,
    Eye,
    Flame,
    Star,
};

const initializeHomeIcons = () => {
    if (!document.querySelector('[data-course-carousel]')) return;

    renderIcons(iconSet);
};

onDomReady(initializeHomeIcons);
