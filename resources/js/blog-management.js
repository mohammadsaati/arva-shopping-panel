function initMediaPreviews(form) {
    form.querySelectorAll('[data-blog-preview]').forEach((preview) => {
        const name = preview.dataset.blogPreview;
        const input = form.querySelector(`[name="${name}"][type="file"]`);
        const remove = form.querySelector(`[name="remove_${name}"][type="checkbox"]`);
        const image = preview.querySelector('[data-blog-preview-image]');
        const empty = preview.querySelector('[data-blog-preview-empty]');
        const currentSrc = preview.dataset.currentSrc || '';
        let objectUrl = null;

        if (!input || !image || !empty) return;

        const update = () => {
            if (objectUrl) URL.revokeObjectURL(objectUrl);
            objectUrl = input.files?.[0] ? URL.createObjectURL(input.files[0]) : null;
            const src = objectUrl || (remove?.checked ? '' : currentSrc);

            if (src) image.src = src;
            else image.removeAttribute('src');

            image.classList.toggle('hidden', !src);
            empty.classList.toggle('hidden', Boolean(src));
        };

        input.addEventListener('change', () => {
            if (input.files?.length && remove) remove.checked = false;
            update();
        });
        remove?.addEventListener('change', () => {
            if (remove.checked && input.files?.length) {
                input.value = '';
                input.dispatchEvent(new Event('change', { bubbles: true }));
            }
            update();
        });
        input.closest('[data-file-input]')?.querySelector('[data-file-input-clear]')?.addEventListener('click', () => queueMicrotask(update));
        update();
    });
}

function initCategorySlug(form) {
    if (form.dataset.blogCategoryEditing === '1') return;

    const name = form.querySelector('[data-blog-category-name]');
    const slug = form.querySelector('[data-blog-category-slug]');
    if (!name || !slug) return;

    let previousSuggestion = '';
    name.addEventListener('input', () => {
        if (slug.value !== previousSuggestion) return;

        previousSuggestion = name.value
            .normalize('NFKC')
            .toLocaleLowerCase()
            .replace(/[^\p{L}\p{N}\s_-]/gu, '')
            .trim()
            .replace(/[\s_]+/gu, '-')
            .replace(/-+/g, '-');

        slug.value = previousSuggestion;
    });
}

function initBlogManagementForms() {
    document.querySelectorAll('.blog-manage-form').forEach((form) => {
        initMediaPreviews(form);
        initCategorySlug(form);
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initBlogManagementForms, { once: true });
} else {
    initBlogManagementForms();
}
