import { ArrowUpRight, BookOpen, CircleCheck, Eye, Lock, MessageCircle, Send, Star } from 'lucide';
import './admin';
import './course-inline-edit';
import './navigation';
import { onDomReady } from './dom-ready';
import { renderIcons } from './icon-renderer';

onDomReady(() => {
    renderIcons({ ArrowUpRight, BookOpen, CircleCheck, Eye, Lock, MessageCircle, Send, Star });
});
