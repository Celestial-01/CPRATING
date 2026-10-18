
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
        const first = data.errors
            ? Object.values(data.errors).flat()[0]
            : null;

        throw new Error(first ?? data.message ?? 'บันทึกไม่สำเร็จ');
    }

    return data;
};

const csv = (value) =>
    (value ?? '').split(',').map((s) => s.trim()).filter(Boolean);

const joinKeys = (item, keys) =>
    keys.map((key) => item[key]).filter(Boolean).join(' ');

const cloneTemplate = (root, name) =>
    root
        .querySelector(`template[data-${name}-template]`)
        .content.firstElementChild.cloneNode(true);

const fillSlots = (node, values) => {
    Object.entries(values).forEach(([slot, text]) => {
        const target = node.querySelector(`[data-slot="${slot}"]`);
        if (target) target.textContent = text ?? '';
    });
};

const initTagPicker = (root) => {
    const tags = root.querySelector('[data-tags]');
    const search = root.querySelector('[data-search]');
    const results = root.querySelector('[data-results]');
    const list = results.querySelector('[data-list]');
    const toggle = root.querySelector('[data-toggle]');
    const labelKeys = csv(root.dataset.labelKeys);
    const subKeys = csv(root.dataset.subKeys);
    const allowCreate = root.dataset.allowCreate === 'true';
    const allowTopicEdit = root.dataset.allowTopicEdit === 'true';

    let timer;
    let requestNumber = 0;

    const tagElements = () => [...tags.querySelectorAll('[data-tag]')];

    const selectedIds = () =>
        new Set(tagElements().map((tag) => tag.dataset.id).filter(Boolean));

    const normalizeName = (name) => name.trim().toLocaleLowerCase();

    const hasTopicName = (name) => {
        const normalized = normalizeName(name);

        return tagElements().some(
            (tag) => normalizeName(tag.dataset.label ?? '') === normalized,
        );
    };

    const hasPendingTopic = (name) => {
        const normalized = normalizeName(name);

        return tagElements().some(
            (tag) =>
                tag.dataset.pendingName &&
                normalizeName(tag.dataset.pendingName) === normalized,
        );
    };

    const syncEmpty = () => {
        root.dataset.empty = String(tagElements().length === 0);
    };

    const setState = (state) => {
        results.dataset.state = state;

        if (state === 'idle') {
            list.replaceChildren();
        }
    };

    const addTag = ({ id, label, sublabel }) => {
        if (selectedIds().has(String(id))) return;

        const tag = cloneTemplate(root, 'tag');
        tag.dataset.id = String(id);
        tag.dataset.label = label;
        tag.dataset.sublabel = sublabel ?? '';

        fillSlots(tag, { label, sublabel });

        const input = tag.querySelector('input[type="hidden"]');
        input.name = `${root.dataset.name}[]`;
        input.value = id;

        tags.appendChild(tag);
        syncEmpty();
    };

    const addPendingTopic = (name) => {
        const cleanName = name.trim();

        if (!allowCreate || !cleanName || hasTopicName(cleanName)) return;
        if (hasPendingTopic(cleanName)) return;

        const tag = cloneTemplate(root, 'tag');
        tag.dataset.pendingName = cleanName;
        tag.dataset.label = cleanName;
        tag.dataset.sublabel = '';

        fillSlots(tag, {
            label: cleanName,
            sublabel: '',
        });

        const input = tag.querySelector('input[type="hidden"]');
        input.name = 'topic_names[]';
        input.value = cleanName;

        tags.appendChild(tag);
        syncEmpty();
    };

    const renderResult = (item) => {
        const label = joinKeys(item, labelKeys);
        const sublabel = joinKeys(item, subKeys);
        const button = cloneTemplate(root, 'result');

        button.dataset.id = String(item.id);
        button.dataset.label = label;
        button.dataset.sublabel = sublabel;

        fillSlots(button, { label, sublabel });

        return button;
    };

    const renderCreateOption = (name) => {
        const button = document.createElement('button');

        button.type = 'button';
        button.dataset.createTopic = '';
        button.className =
            'block w-full border-b border-slate-100 px-4 py-2.5 text-left text-sm font-medium text-sky-600 transition hover:bg-[#edf7ff]';

        button.textContent = `+ สร้างหัวข้อ "${name}"`;

        return button;
    };

    // ดับเบิลคลิกชื่อ Topic เพื่อแก้ไขชื่อ
    tags.addEventListener('dblclick', (event) => {
        if (!allowTopicEdit) return;

        const label = event.target.closest('[data-slot="label"]');
        const tag = label?.closest('[data-tag]');

        // แก้ได้เฉพาะ Topic ที่มีอยู่ในฐานข้อมูลแล้ว
        if (!label || !tag || !tag.dataset.id || tag.dataset.pendingName) {
            return;
        }

        if (tag.querySelector('[data-topic-inline-input]')) return;

        const originalName = label.textContent.trim();
        const topicId = tag.dataset.id;
        const input = document.createElement('input');

        input.type = 'text';
        input.value = originalName;
        input.maxLength = 255;
        input.dataset.topicInlineInput = '';
        input.setAttribute('aria-label', 'แก้ไขชื่อหัวข้อ');
        input.className =
            'w-36 min-w-0 rounded-md border border-sky-300 bg-white px-2 py-1 text-sm text-sky-800 outline-none focus:ring-2 focus:ring-sky-100';

        label.replaceWith(input);
        input.focus();
        input.select();

        let finished = false;
        let saving = false;

        const restoreLabel = (name) => {
            const newLabel = document.createElement('span');
            newLabel.dataset.slot = 'label';
            newLabel.textContent = name;

            input.replaceWith(newLabel);
            tag.dataset.label = name;
            syncEmpty();
        };

        const cancel = () => {
            if (finished || saving) return;

            finished = true;
            restoreLabel(originalName);
        };

        const save = async () => {
            if (finished || saving) return;

            const name = input.value.trim();

            if (!name) {
                input.focus();
                return;
            }

            if (name === originalName) {
                finished = true;
                restoreLabel(originalName);
                return;
            }

            const template = root.dataset.topicUpdateUrl;

            if (!template) {
                finished = true;
                restoreLabel(originalName);
                toast('ไม่พบ URL สำหรับแก้ไขหัวข้อ', true);
                return;
            }

            saving = true;
            input.disabled = true;

            const url = template.replace(
                '__TOPIC_ID__',
                encodeURIComponent(topicId),
            );

            try {
                await send(url, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ name }),
                });

                finished = true;
                restoreLabel(name);
                toast('แก้ไขชื่อหัวข้อเรียบร้อยแล้ว');
            } catch (error) {
                finished = true;
                restoreLabel(originalName);
                toast(error.message || 'แก้ไขชื่อหัวข้อไม่สำเร็จ', true);
            }
        };

        input.addEventListener('keydown', (keyEvent) => {
            if (keyEvent.key === 'Enter') {
                keyEvent.preventDefault();
                save();
            }

            if (keyEvent.key === 'Escape') {
                keyEvent.preventDefault();
                cancel();
            }
        });

        input.addEventListener('blur', save);
    });

    toggle?.addEventListener('click', () => {
        const isOpen = root.dataset.panelState === 'open';
        root.dataset.panelState = isOpen ? 'closed' : 'open';

        if (!isOpen) search.focus();
    });

    tags.addEventListener('click', (event) => {
        const remove = event.target.closest('[data-remove]');
        if (!remove) return;

        remove.closest('[data-tag]').remove();
        syncEmpty();
    });

    list.addEventListener('click', (event) => {
        const createButton = event.target.closest('[data-create-topic]');

        if (createButton) {
            addPendingTopic(search.value);
            search.value = '';
            setState('idle');
            search.focus();
            return;
        }

        const result = event.target.closest('[data-result]');
        if (!result) return;

        addTag({
            id: result.dataset.id,
            label: result.dataset.label,
            sublabel: result.dataset.sublabel,
        });

        search.value = '';
        setState('idle');
        search.focus();
    });

    search.addEventListener('input', () => {
        clearTimeout(timer);

        const keyword = search.value.trim();
        const current = ++requestNumber;

        if (!keyword) return setState('idle');

        timer = setTimeout(async () => {
            list.replaceChildren();
            setState('loading');

            try {
                const response = await fetch(
                    `${root.dataset.url}?keyword=${encodeURIComponent(keyword)}`,
                    { headers: { Accept: 'application/json' } },
                );

                if (!response.ok) throw new Error('Search failed');

                const items = await response.json();
                if (current !== requestNumber) return;

                const taken = selectedIds();
                const available = items.filter(
                    (item) => !taken.has(String(item.id)),
                );

                list.replaceChildren(...available.map(renderResult));

                const exactMatchExists = items.some(
                    (item) =>
                        normalizeName(joinKeys(item, labelKeys)) ===
                        normalizeName(keyword),
                );

                const canCreate =
                    allowCreate &&
                    !exactMatchExists &&
                    !hasTopicName(keyword) &&
                    !hasPendingTopic(keyword);

                if (canCreate) {
                    list.appendChild(renderCreateOption(keyword));
                }

                setState(list.childElementCount > 0 ? 'list' : 'empty');
            } catch {
                if (current !== requestNumber) return;

                list.replaceChildren();
                setState('error');
            }
        }, 250);
    });

    document.addEventListener('click', (event) => {
        if (!root.contains(event.target)) setState('idle');
    });

    root.addEventListener('picker:sync', syncEmpty);
};

const initTopicEdit = (form) => {
    const label = form.querySelector('[data-topic-label]');
    const input = form.querySelector('[data-topic-input]');

    let originalName = input.value;
    let submitting = false;

    const isEditing = () => form.dataset.editing === 'true';

    const stopEditing = () => {
        input.value = originalName;
        form.dataset.editing = 'false';
    };

    const startEditing = () => {
        if (isEditing()) return;

        originalName = label.textContent.trim();
        input.value = originalName;
        form.dataset.editing = 'true';
        input.focus();
        input.select();
    };

    const commit = () => {
        if (submitting) return;

        const name = input.value.trim();
        if (!name || name === originalName) return stopEditing();

        input.value = name;
        submitting = true;
        form.requestSubmit();
    };

    label.addEventListener('dblclick', startEditing);

    input.addEventListener('keydown', (event) => {
        if (event.key === 'Enter') {
            event.preventDefault();
            if (input.value.trim() === '') return input.focus();
            commit();
        }

        if (event.key === 'Escape') {
            event.preventDefault();
            stopEditing();
        }
    });

    input.addEventListener('blur', () => {
        if (isEditing()) commit();
    });
};

onDomReady(() => {
    document.querySelectorAll('[data-tag-picker]').forEach(initTagPicker);
    document.querySelectorAll('[data-topic-edit]').forEach(initTopicEdit);
});
