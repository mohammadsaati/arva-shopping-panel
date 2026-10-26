// Livewire re-renders (search, sort, filter, pagination, ...) morph the DOM,
// which can produce elements KTUI never scanned. KTComponents.init() only
// runs once on the initial page load, so KTUI widgets (dropdown menus,
// tooltips, etc.) rendered inside a Livewire component go dead after the
// first morph unless we re-scan for them.
document.addEventListener('livewire:init', () => {
    Livewire.hook('morph.updated', () => {
        window.KTMenu?.createInstances();
        window.KTDropdown?.createInstances();
        window.KTTooltip?.createInstances();
    });
});

document.addEventListener('menu-badge-updated', (event) => {
    const { key, value } = event.detail ?? {};

    document.querySelectorAll('[data-menu-badge]').forEach((badge) => {
        if (badge.dataset.menuBadge !== key) return;

        const count = Number(value);
        badge.textContent = Number.isFinite(count) && count > 0 ? String(count) : '';
        badge.classList.toggle('hidden', !(Number.isFinite(count) && count > 0));
    });
});
