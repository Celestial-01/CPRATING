// ---------- Tag Picker (อาจารย์ / หัวข้อ) ----------
function initTagPickers() {
    const pickers = document.querySelectorAll('[data-tag-picker]');
    if (pickers.length === 0) return;

    const labelOf = (item) =>
        [item.prefix, item.teacher_name ?? item.name].filter(Boolean).join(' ');

    const refreshEmpty = (root) => {
        const empty = root.querySelector('[data-empty]');
        if (empty) {
            empty.classList.toggle('hidden', root.querySelectorAll('[data-tag]').length > 0);
        }
    };

    const hideResults = (root) =>
        root.querySelector('[data-results]').classList.add('hidden');

    const addTag = (root, id, label) => {
        if (root.querySelector(`[data-tag][data-id="${id}"]`)) return;

        const tag = document.createElement('span');
        tag.dataset.tag = '';
        tag.dataset.id = id;
        tag.className =
            'inline-flex items-center gap-2 rounded-full bg-[#e9f5ff] px-3 py-1 text-sm font-medium text-[#1684e5]';

        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = `${root.dataset.name}[]`;
        input.value = id;

        const text = document.createElement('span');
        text.textContent = label;

        const remove = document.createElement('button');
        remove.type = 'button';
        remove.dataset.remove = '';
        remove.className = 'text-[#1684e5] hover:text-red-500';
        remove.setAttribute('aria-label', 'ลบ');
        remove.textContent = '×';

        tag.append(input, text, remove);
        root.querySelector('[data-tags]').appendChild(tag);
        refreshEmpty(root);
    };

    document.addEventListener('click', (e) => {
        const root = e.target.closest('[data-tag-picker]');

        pickers.forEach((el) => {
            if (el !== root) hideResults(el);
        });
        if (!root) return;

        if (e.target.closest('[data-toggle]')) {
            const panel = root.querySelector('[data-panel]');
            panel.classList.toggle('hidden');
            if (!panel.classList.contains('hidden')) {
                root.querySelector('[data-search]').focus();
            }
        }

        const remove = e.target.closest('[data-remove]');
        if (remove) {
            remove.closest('[data-tag]').remove();
            refreshEmpty(root);
        }

        const result = e.target.closest('[data-result]');
        if (result) {
            addTag(root, result.dataset.id, result.dataset.label);
            const search = root.querySelector('[data-search]');
            search.value = '';
            hideResults(root);
            search.focus();
        }
    });

    let timer;
    document.addEventListener('input', (e) => {
        const search = e.target.closest('[data-search]');
        if (!search) return;

        const root = search.closest('[data-tag-picker]');
        const box = root.querySelector('[data-results]');
        clearTimeout(timer);

        timer = setTimeout(async () => {
            const keyword = search.value.trim();
            if (keyword === '') return hideResults(root);

            box.replaceChildren();

            try {
                const res = await fetch(
                    `${root.dataset.url}?keyword=${encodeURIComponent(keyword)}`,
                    { headers: { Accept: 'application/json' } }
                );
                if (!res.ok) throw new Error('Search failed');

                const items = (await res.json()).filter(
                    (item) => !root.querySelector(`[data-tag][data-id="${item.id}"]`)
                );

                if (items.length === 0) {
                    const none = document.createElement('p');
                    none.className = 'px-4 py-3 text-sm text-slate-400';
                    none.textContent = 'ไม่พบข้อมูล';
                    box.appendChild(none);
                }

                items.forEach((item) => {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.dataset.result = '';
                    btn.dataset.id = item.id;
                    btn.dataset.label = labelOf(item);
                    btn.className =
                        'block w-full px-4 py-2.5 text-left text-sm text-slate-700 hover:bg-[#edf7ff]';
                    btn.textContent = labelOf(item);
                    box.appendChild(btn);
                });
            } catch (error) {
                const msg = document.createElement('p');
                msg.className = 'px-4 py-3 text-sm text-red-500';
                msg.textContent = 'ค้นหาไม่สำเร็จ กรุณาลองใหม่';
                box.appendChild(msg);
            }

            box.classList.remove('hidden');
        }, 250);
    });
}

// ---------- วิชาบังคับก่อน (select -> tag) ----------

function initPrerequisites() {
    const picker = document.getElementById('prerequisite-picker');
    if (!picker) return;

    const search = picker.querySelector('#prerequisite-search');
    const results = picker.querySelector('#prerequisite-results');
    const container = picker.querySelector('#selected-prerequisites');
    const url = picker.dataset.url;

    const selectedIds = new Set(
        [...container.querySelectorAll('[data-prerequisite-tag]')]
            .map(tag => tag.dataset.id)
    );

    let timer;
    let requestNumber = 0;

    function hideResults() {
        results.classList.add('hidden');
        results.replaceChildren();
    }

    function addTag(course) {
        const id = String(course.id);

        if (selectedIds.has(id)) return;

        selectedIds.add(id);

        const tag = document.createElement('span');
        tag.dataset.prerequisiteTag = '';
        tag.dataset.id = id;
        tag.className =
            'inline-flex items-center gap-3 rounded-xl bg-[#e9f5ff] px-3 py-2';

        const info = document.createElement('span');
        info.className = 'flex flex-col gap-0.5';

        const code = document.createElement('span');
        code.className = 'text-sm font-semibold text-[#1684e5]';
        code.textContent = course.course_code;

        const name = document.createElement('span');
        name.className = 'text-xs text-[#5792bd]';
        name.textContent = course.course_name_en;

        info.append(code, name);

        const hidden = document.createElement('input');
        hidden.type = 'hidden';
        hidden.name = 'prerequisite_ids[]';
        hidden.value = id;

        const remove = document.createElement('button');
        remove.type = 'button';
        remove.dataset.removePrerequisite = '';
        remove.setAttribute('aria-label', 'นำวิชาบังคับก่อนออก');
        remove.className =
            'text-xl leading-none text-[#1684e5] hover:text-red-500';
        remove.textContent = '×';

        tag.append(info, hidden, remove);
        container.appendChild(tag);
    }

    // ลบแท็กได้ทั้งแท็กเดิมและแท็กที่เพิ่มใหม่
    container.addEventListener('click', event => {
        const button = event.target.closest('[data-remove-prerequisite]');
        if (!button) return;

        const tag = button.closest('[data-prerequisite-tag]');
        selectedIds.delete(tag.dataset.id);
        tag.remove();
    });

    // ค้นหาจากรหัสวิชา ชื่อภาษาไทย หรือชื่อภาษาอังกฤษ
    search.addEventListener('input', () => {
        clearTimeout(timer);

        const keyword = search.value.trim();
        const currentRequest = ++requestNumber;

        if (!keyword) {
            hideResults();
            return;
        }

        timer = setTimeout(async () => {
            results.replaceChildren();

            const loading = document.createElement('p');
            loading.className = 'px-4 py-3 text-sm text-slate-400';
            loading.textContent = 'กำลังค้นหา...';
            results.appendChild(loading);
            results.classList.remove('hidden');

            try {
                const response = await fetch(
                    `${url}?keyword=${encodeURIComponent(keyword)}`,
                    { headers: { Accept: 'application/json' } }
                );

                if (!response.ok) throw new Error('Search failed');

                const courses = await response.json();

                if (currentRequest !== requestNumber) return;

                results.replaceChildren();

                const available = courses.filter(course =>
                    !selectedIds.has(String(course.id))
                );

                if (available.length === 0) {
                    const empty = document.createElement('p');
                    empty.className = 'px-4 py-3 text-sm text-slate-400';
                    empty.textContent = 'ไม่พบรายวิชาที่เลือกได้';
                    results.appendChild(empty);
                }

                available.forEach(course => {
                    const button = document.createElement('button');
                    button.type = 'button';
                    button.className =
                        'block w-full border-b border-gray-100 px-4 py-3 text-left transition hover:bg-[#edf7ff]';

                    const code = document.createElement('span');
                    code.className =
                        'block text-sm font-semibold text-[#1684e5]';
                    code.textContent = course.course_code;

                    const name = document.createElement('span');
                    name.className =
                        'mt-0.5 block text-sm text-slate-500';
                    name.textContent = course.course_name_en;

                    button.append(code, name);

                    button.addEventListener('click', () => {
                        addTag(course);
                        search.value = '';
                        hideResults();
                        search.focus();
                    });

                    results.appendChild(button);
                });

                results.classList.remove('hidden');
            } catch (error) {
                if (currentRequest !== requestNumber) return;

                results.replaceChildren();

                const message = document.createElement('p');
                message.className = 'px-4 py-3 text-sm text-red-500';
                message.textContent = 'ค้นหาไม่สำเร็จ กรุณาลองใหม่';

                results.appendChild(message);
                results.classList.remove('hidden');
            }
        }, 250);
    });

    // คลิกนอกช่องค้นหาเพื่อปิดผลลัพธ์
    document.addEventListener('click', event => {
        if (!picker.contains(event.target)) {
            hideResults();
        }
    });
}


document.addEventListener('DOMContentLoaded', () => {
    initTagPickers();
    initPrerequisites();
});

document.querySelectorAll('[data-topic-edit]').forEach((form) => {
            const label = form.querySelector('[data-topic-label]');
            const input = form.querySelector('[data-topic-input]');

            let editing = false;
            let originalName = input.value;

            function startEditing() {
                if (editing) return;

                editing = true;
                originalName = label.textContent.trim();

                label.classList.add('hidden');
                input.classList.remove('hidden');
                input.value = originalName;
                input.focus();
                input.select();
            }

            function cancelEditing() {
                input.value = originalName;
                input.classList.add('hidden');
                label.classList.remove('hidden');
                editing = false;
            }

            label.addEventListener('dblclick', startEditing);

            input.addEventListener('keydown', (event) => {
                if (event.key === 'Enter') {
                    event.preventDefault();

                    if (input.value.trim() === '') {
                        input.focus();
                        return;
                    }

                    if (input.value.trim() === originalName) {
                        cancelEditing();
                        return;
                    }

                    input.value = input.value.trim();
                    form.requestSubmit();
                }

                if (event.key === 'Escape') {
                    event.preventDefault();
                    cancelEditing();
                }
            });

            input.addEventListener('blur', () => {
                if (!editing) return;

                const newName = input.value.trim();

                if (!newName || newName === originalName) {
                    cancelEditing();
                    return;
                }

                input.value = newName;
                form.requestSubmit();
            });
        });