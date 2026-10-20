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
    X,
} from 'lucide';
import { onDomReady } from './dom-ready';
import { renderIcons } from './icon-renderer';
import { getSavedCodes, isSaved, toggleSaved } from './course-storage';
import { animatePress, applyTilt, createToast, formatCopy, parseJson } from './utils';

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
    X,
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

const initializeProgressValues = () => {
    document.querySelectorAll('[data-progress-width]').forEach((element) => {
        const percentage = Number(element.dataset.progressWidth);

        if (Number.isFinite(percentage)) {
            element.style.width = `${Math.min(100, Math.max(0, percentage))}%`;
        }
    });

    document.querySelectorAll('[data-progress-height]').forEach((element) => {
        const height = Number(element.dataset.progressHeight);

        if (Number.isFinite(height)) {
            element.style.height = `${Math.min(100, Math.max(0, height))}%`;
        }
    });
};

const initializeRatingDialog = () => {
    const dialog = document.querySelector('[data-course-rating-dialog]');
    const openButton = document.querySelector('[data-course-rating-open]');
    const closeButton = dialog?.querySelector('[data-course-rating-close]');
    const submitButton = dialog?.querySelector('[data-course-rating-submit]');
    const gradeSelect = dialog?.querySelector('[name="grade"]');
    const groups = dialog ? [...dialog.querySelectorAll('[data-rating-group]')] : [];

    if (!dialog || !openButton || !submitButton || !groups.length) return;

    const reset = () => {
        groups.forEach((group) => {
            delete group.dataset.value;
            group.querySelector('[data-rating-input]').value = '';
            group.querySelectorAll('[data-rating-option]').forEach((option) => {
                option.dataset.selected = 'false';
                option.setAttribute('aria-checked', 'false');
            });
        });
        if (gradeSelect) gradeSelect.value = '';
        submitButton.disabled = true;
    };

    const updateSubmitState = () => {
        submitButton.disabled = groups.some((group) => !group.dataset.value);
    };

    const setRating = (group, value) => {
        group.dataset.value = String(value);
        group.querySelector('[data-rating-input]').value = String(value);
        group.querySelectorAll('[data-rating-option]').forEach((option) => {
            const optionValue = Number(option.dataset.ratingValue);
            const isSelected = optionValue <= value;
            const isCurrent = optionValue === value;

            option.dataset.selected = String(isSelected);
            option.setAttribute('aria-checked', String(isCurrent));
        });
        updateSubmitState();
    };

    groups.forEach((group) => {
        const previousValue = Number(group.querySelector('[data-rating-input]').value);

        if (Number.isInteger(previousValue) && previousValue >= 1 && previousValue <= 5) {
            setRating(group, previousValue);
        }

        group.addEventListener('click', (event) => {
            const option = event.target.closest('[data-rating-option]');

            if (!option || !group.contains(option)) return;

            setRating(group, Number(option.dataset.ratingValue));
        });
    });

    openButton.addEventListener('click', () => {
        if (!dialog.open) dialog.showModal();
    });
    closeButton.addEventListener('click', () => dialog.close());
    dialog.addEventListener('click', (event) => {
        if (event.target === dialog) dialog.close();
    });
    dialog.addEventListener('close', reset);

    if (dialog.hasAttribute('data-rating-errors')) dialog.showModal();
};

const initializeRatingCancellation = () => {
    const dialog = document.querySelector('[data-course-rating-cancel-dialog]');
    const openButton = document.querySelector('[data-course-rating-cancel-open]');
    const closeButton = dialog?.querySelector('[data-course-rating-cancel-close]');

    if (!dialog || !openButton || !closeButton) return;

    openButton.addEventListener('click', () => {
        if (!dialog.open) dialog.showModal();
    });
    closeButton.addEventListener('click', () => dialog.close());
    dialog.addEventListener('click', (event) => {
        if (event.target === dialog) dialog.close();
    });
};

const initializeRelatedCourses = (copy, showToast) => {
    const relatedCourses = document.querySelector('[data-related-courses]');

    if (!relatedCourses) return;

    const savedCodes = new Set(getSavedCodes());

    relatedCourses.querySelectorAll('[data-course-card]').forEach((card) => {
        const saveButton = card.querySelector('[data-course-save]');

        if (saveButton) {
            saveButton.setAttribute('aria-pressed', String(savedCodes.has(saveButton.dataset.courseSave)));
        }

        applyTilt(card);
    });

    relatedCourses.addEventListener('click', async (event) => {
        const saveButton = event.target.closest('[data-course-save]');

        if (!saveButton || !relatedCourses.contains(saveButton)) return;

        const code = saveButton.dataset.courseSave;
        let saved;
        try {
            saved = await toggleSaved(saveButton);
        } catch {
            showToast('บันทึกวิชาไม่สำเร็จ กรุณาลองอีกครั้ง');
            return;
        }
        if (saved === null) return;

        saveButton.setAttribute('aria-pressed', String(saved));
        showToast(formatCopy(saved ? copy.saved : copy.unsaved, { course: code }));
        animatePress(saveButton);
    });
};

const initializeCourse = () => {
    initializeProgressValues();
    const detail = document.querySelector('[data-course-detail]');

    if (detail) {
        renderIcons(iconSet);

        const code = detail.dataset.courseDetail;
        const saveButton = detail.querySelector('[data-course-save]');
        const copy = parseJson(document.querySelector('[data-course-detail-copy]'), {});
        const showToast = createToast();

        initializeRatingDialog();
        initializeRatingCancellation();
        initializeRelatedCourses(copy, showToast);

        if (detail.dataset.ratingStatus) showToast(detail.dataset.ratingStatus);

        if (saveButton) {
            const syncSaveButton = (saved) => {
                saveButton.setAttribute('aria-pressed', String(saved));
                saveButton.setAttribute('aria-label', formatCopy(
                    saved ? copy.unsave_label : copy.save_label,
                    { course: code },
                ));
            };

            syncSaveButton(isSaved(code));
            saveButton.addEventListener('click', async () => {
                let saved;
                try {
                    saved = await toggleSaved(saveButton);
                } catch {
                    showToast('บันทึกวิชาไม่สำเร็จ กรุณาลองอีกครั้ง');
                    return;
                }
                if (saved === null) return;
                syncSaveButton(saved);
                showToast(formatCopy(saved ? copy.saved : copy.unsaved, { course: code }));
            });
        }
    }

    initializeCommentSort();
};

onDomReady(initializeCourse);
