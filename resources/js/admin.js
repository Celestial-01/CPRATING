import { onDomReady } from './dom-ready';

const csv = (value) => (value ?? '').split(',').map((s) => s.trim()).filter(Boolean);
const joinKeys = (item, keys) => keys.map((key) => item[key]).filter(Boolean).join(' ');

const cloneTemplate = (root, name) =>
    root.querySelector(`template[data-${name}-template]`).content.firstElementChild.cloneNode(true);

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

    let timer;
    let requestNumber = 0;

    const tagElements = () => [...tags.querySelectorAll('[data-tag]')];
    const selectedIds = () => new Set(tagElements().map((tag) => tag.dataset.id));
    const syncEmpty = () => {
        root.dataset.empty = String(tagElements().length === 0);
    };

    const setState = (state) => {
        results.dataset.state = state;
        if (state === 'idle') list.replaceChildren();
    };

    const addTag = ({ id, label, sublabel }) => {
        if (selectedIds().has(id)) return;

        const tag = cloneTemplate(root, 'tag');
        tag.dataset.id = id;
        tag.dataset.label = label;
        tag.dataset.sublabel = sublabel;
        fillSlots(tag, { label, sublabel });

        const input = tag.querySelector('input[type="hidden"]');
        input.name = `${root.dataset.name}[]`;
        input.value = id;

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
                const available = items.filter((item) => !taken.has(String(item.id)));

                list.replaceChildren(...available.map(renderResult));
                setState(available.length ? 'list' : 'empty');
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