import { onDomReady } from './dom-ready';

const csrf = () => document.querySelector('[data-csrf]')?.dataset.csrf ?? '';

const toast = (message, isError = false) => {
    const el = document.createElement('div');
    el.textContent = message;
    el.className =
        'fixed bottom-6 right-6 z-[100] rounded-xl px-4 py-3 text-sm text-white shadow-lg ' +
        (isError ? 'bg-red-600' : 'bg-[#1684e5]');
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

// ---------- ฟิลด์ธรรมดา ----------
const initField = (root) => {
    const view = root.querySelector('[data-view]');
    const editor = root.querySelector('[data-editor]');
    const input = root.querySelector('[data-input]');
    const display = root.querySelector('[data-display]');
    const error = root.querySelector('[data-error]');
    const saveButton = root.querySelector('[data-save]');
    let original = input.value;

    const setOpen = (isOpen) => {
        view.classList.toggle('hidden', isOpen);
        editor.classList.toggle('hidden', !isOpen);
    };

    const open = () => {
        original = input.value;
        setOpen(true);
        input.focus();
        if (input.tagName === 'INPUT') input.select();
    };

    const cancel = () => {
        input.value = original;
        error.classList.add('hidden');
        setOpen(false);
    };

    const save = async () => {
        const value = input.value.trim();

        if (value === original) return cancel();

        saveButton.disabled = true;
        saveButton.textContent = 'กำลังบันทึก...';
        error.classList.add('hidden');

        try {
            const data = await patch(root.dataset.url, { [root.dataset.field]: value });
            const shown = data.displays?.[root.dataset.field];

            display.textContent =
                shown !== null && shown !== undefined && shown !== ''
                    ? shown
                    : display.dataset.placeholder;
            input.value = value;
            original = value;
            setOpen(false);
            toast('บันทึกแล้ว');
        } catch (e) {
            error.textContent = e.message;
            error.classList.remove('hidden');
        } finally {
            saveButton.disabled = false;
            saveButton.textContent = 'บันทึก';
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

// ---------- ความสัมพันธ์ (อาจารย์ / หัวข้อ / วิชาบังคับก่อน) ----------
const tagLabel = (tag) => {
    const el = tag.querySelector('span');
    const parts = [...el.children];

    return parts.length
        ? parts.map((part) => part.textContent.trim()).join(' ')
        : el.textContent.trim();
};

const initRelation = (root) => {
    const view = root.querySelector('[data-view]');
    const editor = root.querySelector('[data-editor]');
    const chips = root.querySelector('[data-chips]');
    const error = root.querySelector('[data-error]');
    const saveButton = root.querySelector('[data-save]');
    const editButton = root.querySelector('[data-edit]');

    const setOpen = (isOpen) => {
        view.classList.toggle('hidden', isOpen);
        editor.classList.toggle('hidden', !isOpen);
        editButton.classList.toggle('hidden', isOpen);
    };

    const renderChips = () => {
        chips.replaceChildren();
        const tags = editor.querySelectorAll('[data-tag], [data-prerequisite-tag]');

        if (tags.length === 0) {
            const empty = document.createElement('p');
            empty.className = 'text-sm text-slate-400';
            empty.textContent = root.dataset.emptyText;
            chips.appendChild(empty);
            return;
        }

        tags.forEach((tag) => {
            const chip = document.createElement('span');
            chip.className =
                'inline-flex items-center rounded-full bg-[#e9f5ff] px-3 py-1 text-sm font-medium text-[#1684e5]';
            chip.textContent = tagLabel(tag);
            chips.appendChild(chip);
        });
    };

    editButton.addEventListener('click', () => setOpen(true));

    // ยกเลิก = โหลดหน้าใหม่ เพื่อคืนค่าแท็กที่เลือกไว้เดิมให้ครบ
    root.querySelector('[data-cancel]').addEventListener('click', () => location.reload());

    saveButton.addEventListener('click', async () => {
        const ids = [...editor.querySelectorAll('input[type="hidden"]')]
            .filter((input) => input.name.endsWith('[]'))
            .map((input) => Number(input.value));

        saveButton.disabled = true;
        saveButton.textContent = 'กำลังบันทึก...';
        error.classList.add('hidden');

        try {
            await patch(root.dataset.url, { [root.dataset.field]: ids });
            renderChips();
            setOpen(false);
            toast('บันทึกแล้ว');
        } catch (e) {
            error.textContent = e.message;
            error.classList.remove('hidden');
        } finally {
            saveButton.disabled = false;
            saveButton.textContent = 'บันทึก';
        }
    });
};

// ---------- รูปปก ----------
const initImage = (box) => {
    const input = box.querySelector('[data-image-input]');
    const image = box.querySelector('[data-course-image]');
    const label = box.querySelector('[data-image-label]');
    const error = document.querySelector('[data-image-error]');

    input.addEventListener('change', async () => {
        const file = input.files[0];
        if (!file) return;

        const formData = new FormData();
        formData.append('image', file);

        label.textContent = 'กำลังอัปโหลด...';
        error.classList.add('hidden');

        try {
            const data = await send(box.dataset.url, { method: 'POST', body: formData });
            image.src = data.url;
            image.classList.remove('hidden');
            toast('เปลี่ยนรูปปกแล้ว');
        } catch (e) {
            error.textContent = e.message;
            error.classList.remove('hidden');
        } finally {
            label.textContent = 'เปลี่ยนรูปปก';
            input.value = '';
        }
    });
};

onDomReady(() => {
    document.querySelectorAll('[data-inline]').forEach(initField);
    document.querySelectorAll('[data-inline-relation]').forEach(initRelation);
    document.querySelectorAll('[data-image-box]').forEach(initImage);
});

