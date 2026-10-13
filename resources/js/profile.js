import {
    ArrowRight,
    Check,
    ChevronDown,
    CircleAlert,
    LockKeyhole,
    Mail,
    UserRound,
} from 'lucide';
import { onDomReady } from './dom-ready';
import { renderIcons } from './icon-renderer';

const iconSet = {
    ArrowRight,
    Check,
    ChevronDown,
    CircleAlert,
    LockKeyhole,
    Mail,
    UserRound,
};

const STUDENT_ID_PATTERN = /^\d{9}-\d$/;

const formatStudentId = (value) => {
    const digits = value.replace(/\D/g, '').slice(0, 10);

    return digits.length > 9
        ? `${digits.slice(0, 9)}-${digits.slice(9)}`
        : digits;
};

const getCohortTag = (studentId) => {
    const match = studentId.match(/^(\d{2})/);

    if (!match) return null;

    const cohort = Number(match[1]) - 6;

    return cohort >= 0 ? `KKU ${cohort}` : null;
};

const initializeProfilePreview = () => {
    const input = document.querySelector('[data-student-id-input]');
    const majorSelect = document.querySelector('[data-major-select]');
    const validIcon = document.querySelector('[data-student-id-valid]');
    const cohortTag = document.querySelector('[data-profile-cohort]');
    const majorTag = document.querySelector('[data-profile-major]');

    if (!input || !majorSelect || !validIcon || !cohortTag || !majorTag) return;

    const updatePreview = () => {
        const isValid = STUDENT_ID_PATTERN.test(input.value);

        validIcon.classList.toggle('hidden', !isValid);
        validIcon.classList.toggle('grid', isValid);
        cohortTag.textContent = isValid ? (getCohortTag(input.value) ?? '—') : '—';
        majorTag.textContent = majorSelect.value || '—';
    };

    input.addEventListener('input', () => {
        input.value = formatStudentId(input.value);
        updatePreview();
    });
    majorSelect.addEventListener('change', updatePreview);

    updatePreview();
};

const initializeStudentIdInput = () => {
    if (!document.querySelector('[data-profile-form]')) return;

    renderIcons(iconSet);
    initializeProfilePreview();
};

onDomReady(initializeStudentIdInput);
