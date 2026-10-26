document.addEventListener('click', (event) => {
    const button = event.target.closest('[data-copy-link]');
    if (! button) {
        return;
    }

    const link = button.getAttribute('data-copy-link') || window.location.href;
    navigator.clipboard.writeText(link).then(() => {
        button.dataset.copied = 'true';
        window.setTimeout(() => {
            delete button.dataset.copied;
        }, 2000);
    });
});
