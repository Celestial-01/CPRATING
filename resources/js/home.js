import {
    BookOpen,
    ChevronLeft,
    ChevronRight,
    Eye,
    Flame,
    Star,
} from 'lucide';
import { onDomReady } from './dom-ready';
import { renderIcons } from './icon-renderer';

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
