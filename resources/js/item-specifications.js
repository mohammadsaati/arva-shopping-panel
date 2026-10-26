document.addEventListener('DOMContentLoaded', () => {
    const container = document.querySelector('[data-item-specifications]');
    const category = document.querySelector('select[name="category_id"]');

    if (!container || !category) return;

    let request = null;

    category.addEventListener('change', async () => {
        const categoryId = category.value;
        if (categoryId === container.dataset.currentCategory) return;

        request?.abort();
        container.dataset.currentCategory = categoryId;

        if (!categoryId) {
            container.innerHTML = '<p class="text-sm text-gray-500">برای نمایش مشخصات، دسته‌بندی را انتخاب کنید.</p>';
            return;
        }

        request = new AbortController();
        container.setAttribute('aria-busy', 'true');

        try {
            const url = container.dataset.urlTemplate.replace('__CATEGORY__', encodeURIComponent(categoryId));
            const response = await fetch(url, { signal: request.signal, headers: { Accept: 'text/html' } });
            if (!response.ok) throw new Error('Could not load specifications');
            container.innerHTML = await response.text();
        } catch (error) {
            if (error.name !== 'AbortError') {
                container.innerHTML = '<p class="text-sm text-danger" role="alert">بارگذاری مشخصات انجام نشد. دسته‌بندی را دوباره انتخاب کنید.</p>';
                container.dataset.currentCategory = '';
            }
        } finally {
            container.removeAttribute('aria-busy');
        }
    });
});
