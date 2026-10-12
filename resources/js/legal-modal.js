export const initializeLegalModal = () => {
    const modal = document.querySelector('[data-legal-modal]');

    if (!modal) return;

    const title = modal.querySelector('[data-legal-modal-title]');
    const content = modal.querySelector('[data-legal-modal-content]');
    const closeButton = modal.querySelector('[data-legal-modal-close]');

    if (!title || !content || !closeButton) return;

    let closeTimer;

    const closeModal = () => {
        if (!modal.open || modal.dataset.open === 'false') return;

        modal.dataset.open = 'false';
        window.clearTimeout(closeTimer);
        closeTimer = window.setTimeout(() => modal.close(), 200);
    };

    const openModal = (type) => {
        const template = document.querySelector(`[data-legal-content="${type}"]`);

        if (!template) return;

        window.clearTimeout(closeTimer);
        title.textContent = template.dataset.legalTitle || '';
        content.replaceChildren(template.content.cloneNode(true));
        content.scrollTop = 0;

        if (!modal.open) modal.showModal();

        document.body.classList.add('overflow-hidden');
        requestAnimationFrame(() => {
            modal.dataset.open = 'true';
            closeButton.focus();
        });
    };

    document.querySelectorAll('[data-legal-modal-trigger]').forEach((trigger) => {
        trigger.addEventListener('click', (event) => {
            event.preventDefault();
            openModal(trigger.dataset.legalModalTrigger);
        });
    });

    closeButton.addEventListener('click', closeModal);
    modal.addEventListener('click', (event) => {
        if (event.target === modal) closeModal();
    });
    modal.addEventListener('cancel', (event) => {
        event.preventDefault();
        closeModal();
    });
    modal.addEventListener('close', () => {
        delete modal.dataset.open;
        document.body.classList.remove('overflow-hidden');
    });
};
