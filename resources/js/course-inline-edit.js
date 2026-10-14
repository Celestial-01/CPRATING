import { onDomReady } from './dom-ready';
const csrf = () => document.querySelector('[data-csrf]')?.dataset.csrf ?? '';

const toast = (message, isError = false) => {
    const template = document.querySelector('template[data-toast-template]');
    if (!template) return;

    const el = template.content.firstElementChild.cloneNode(true);
    el.dataset.type = isError ? 'error' : 'success';
    el.textContent = message;
    document.body.appendChild(el);
    setTimeout(() => el.remove(), 2500);
};

const send = async (url, options) => {
    const response = await fetch(url, {
        ...options,
        headers: {
            Accept: 'application/json',
            'X-CSRF-TOKEN': csrf(),
            ...(options.headers ?? {}),
        },
    });

    if (response.status === 419) {
        throw new Error('เซสชันหมดอายุ กรุณารีโหลดหน้าแล้วลองใหม่');
    }

    const data = await response.json().catch(() => ({}));

    if (!response.ok) {
        const first = data.errors ? Object.values(data.errors).flat()[0] : null;
        throw new Error(first ?? data.message ?? 'บันทึกไม่สำเร็จ');
    }

    return data;
};

const patch = (url, body) =>
    send(url, {
        method: 'PATCH',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(body),
    });

const showError = (root, errorEl, message) => {
    errorEl.textContent = message;
    root.dataset.hasError = 'true';
};

const clearError = (root) => {
    root.dataset.hasError = 'false';
};

const setSaving = (root, button, isSaving) => {
    root.dataset.saving = String(isSaving);
    button.disabled = isSaving;
};

const initField = (root) => {
    const input = root.querySelector('[data-input]');
    const display = root.querySelector('[data-display]');
    const error = root.querySelector('[data-error]');
    const saveButton = root.querySelector('[data-save]');
    let original = input.value;

    const setEditing = (isEditing) => {
        root.dataset.editing = String(isEditing);
    };

    const open = () => {
        original = input.value;
        clearError(root);
        setEditing(true);
        input.focus();
        if (input.tagName === 'INPUT') input.select();
    };

    const cancel = () => {
        input.value = original;
        clearError(root);
        setEditing(false);
    };

    const save = async () => {
        const value = input.value.trim();

        if (value === original) return cancel();

        setSaving(root, saveButton, true);
        clearError(root);

        try {
            const data = await patch(root.dataset.url, { [root.dataset.field]: value });
            const shown = data.displays?.[root.dataset.field];

            display.textContent =
                shown !== null && shown !== undefined && shown !== ''
                    ? shown
                    : display.dataset.placeholder;

            document
                .querySelectorAll(`[data-mirror="${root.dataset.field}"]`)
                .forEach((mirror) => {
                    mirror.textContent = display.textContent;
                });

            input.value = value;
            original = value;
            setEditing(false);
            toast('บันทึกแล้ว');
        } catch (e) {
            showError(root, error, e.message);
        } finally {
            setSaving(root, saveButton, false);
        }
    };

    root.querySelector('[data-edit]').addEventListener('click', open);
    root.querySelector('[data-cancel]').addEventListener('click', cancel);
    saveButton.addEventListener('click', save);

    input.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            event.preventDefault();
            cancel();
        }

        const isTextarea = input.tagName === 'TEXTAREA';
        if (event.key === 'Enter' && (!isTextarea || event.ctrlKey || event.metaKey)) {
            event.preventDefault();
            save();
        }
    });
};

const initRelation = (root) => {
    const editor = root.querySelector('[data-editor]');
    const chips = root.querySelector('[data-chips]');
    const error = root.querySelector('[data-error]');
    const saveButton = root.querySelector('[data-save]');
    const chipTemplate = root.querySelector('template[data-chip-template]');
    const picker = editor.querySelector('[data-tag-picker]');
    const tagsBox = picker.querySelector('[data-tags]');

    let snapshot = [];

    const currentTags = () => [...tagsBox.querySelectorAll('[data-tag]')];

    const renderChips = () => {
        chips.querySelectorAll('[data-chip]').forEach((chip) => chip.remove());

        const tags = currentTags();

        tags.forEach((tag) => {
            const chip = chipTemplate.content.firstElementChild.cloneNode(true);
            chip.querySelector('[data-slot="label"]').textContent = tag.dataset.label ?? '';
            chip.querySelector('[data-slot="sublabel"]').textContent = tag.dataset.sublabel ?? '';
            chips.appendChild(chip);
        });

        root.dataset.empty = String(tags.length === 0);
    };

    const open = () => {
        snapshot = [...tagsBox.children].map((node) => node.cloneNode(true));
        clearError(root);
        root.dataset.editing = 'true';
    };

    const cancel = () => {
        tagsBox.replaceChildren(...snapshot.map((node) => node.cloneNode(true)));
        picker.dispatchEvent(new Event('picker:sync'));
        clearError(root);
        root.dataset.editing = 'false';
    };

    root.querySelector('[data-edit]').addEventListener('click', open);
    root.querySelector('[data-cancel]').addEventListener('click', cancel);

    saveButton.addEventListener('click', async () => {
        const ids = currentTags().map((tag) =>
            Number(tag.querySelector('input[type="hidden"]').value),
        );

        setSaving(root, saveButton, true);
        clearError(root);

        try {
            await patch(root.dataset.url, { [root.dataset.field]: ids });
            renderChips();
            root.dataset.editing = 'false';
            toast('บันทึกแล้ว');
        } catch (e) {
            showError(root, error, e.message);
        } finally {
            setSaving(root, saveButton, false);
        }
    });
};

const initImage = (box) => {
    const input = box.querySelector('[data-image-input]');
    const image = box.querySelector('[data-course-image]');
    const error = box.querySelector('[data-image-error]');

    input.addEventListener('change', async () => {
        const file = input.files[0];
        if (!file) return;

        const formData = new FormData();
        formData.append('image', file);

        box.dataset.uploading = 'true';
        clearError(box);

        try {
            const data = await send(box.dataset.url, { method: 'POST', body: formData });
            image.src = data.url;
            box.dataset.hasImage = 'true';
            toast('เปลี่ยนรูปปกแล้ว');
        } catch (e) {
            showError(box, error, e.message);
        } finally {
            box.dataset.uploading = 'false';
            input.value = '';
        }
    });
};

onDomReady(() => {
    document.querySelectorAll('[data-inline]').forEach(initField);
    document.querySelectorAll('[data-inline-relation]').forEach(initRelation);
    document.querySelectorAll('[data-image-box]').forEach(initImage);
});
