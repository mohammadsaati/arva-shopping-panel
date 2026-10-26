function initProductReviewInteractions() {
    document.querySelectorAll('[data-review-dialog][data-auto-open="true"]').forEach((dialog) => {
        if (!dialog.open) dialog.showModal();
    });

    const refreshPointCount = (editor) => {
        const list = editor.querySelector('[data-review-points-list]');
        const addButton = editor.querySelector('[data-review-point-add]');
        const counter = editor.querySelector('[data-review-points-count]');
        const count = list?.querySelectorAll('[data-review-point-row]').length ?? 0;
        const max = Number(editor.dataset.max || 10);

        if (counter) counter.textContent = count.toLocaleString('fa-IR') + '/' + max.toLocaleString('fa-IR');
        if (addButton) {
            addButton.disabled = count >= max;
            addButton.setAttribute('aria-disabled', String(count >= max));
        }
    };

    document.addEventListener('click', (event) => {
        if (!(event.target instanceof Element)) return;

        const openButton = event.target.closest('[data-open-review-dialog]');
        if (openButton) {
            const dialog = document.querySelector('[data-review-dialog]');
            if (dialog && !dialog.open) dialog.showModal();
            return;
        }

        const closeButton = event.target.closest('[data-close-review-dialog]');
        if (closeButton) {
            closeButton.closest('[data-review-dialog]')?.close();
            return;
        }

        const addButton = event.target.closest('[data-review-point-add]');
        if (addButton) {
            const editor = addButton.closest('[data-review-points-editor]');
            const list = editor?.querySelector('[data-review-points-list]');
            const template = editor?.querySelector('[data-review-points-template]');
            if (!editor || !list || !template || list.children.length >= Number(editor.dataset.max || 10)) return;

            const row = template.content.firstElementChild?.cloneNode(true);
            if (!row) return;

            const index = Number(editor.dataset.nextIndex || 0);
            row.querySelectorAll('*').forEach((element) => {
                for (const attribute of [...element.attributes]) {
                    if (attribute.value.includes('__INDEX__')) {
                        element.setAttribute(attribute.name, attribute.value.replaceAll('__INDEX__', String(index)));
                    }
                }
            });
            editor.dataset.nextIndex = String(index + 1);
            list.append(row);
            row.querySelector('input')?.focus();
            refreshPointCount(editor);
            return;
        }

        const removeButton = event.target.closest('[data-review-point-remove]');
        if (removeButton) {
            const editor = removeButton.closest('[data-review-points-editor]');
            removeButton.closest('[data-review-point-row]')?.remove();
            if (editor) refreshPointCount(editor);
            return;
        }

        if (event.target.matches('[data-review-dialog]')) event.target.close();
    });

    document.querySelectorAll('[data-review-points-editor]').forEach(refreshPointCount);
}

document.addEventListener('DOMContentLoaded', initProductReviewInteractions);
