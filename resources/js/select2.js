const selector = '[data-select2]';
const instances = new WeakMap();

class CustomSelect2 {
    constructor(root) {
        this.root = root;
        this.select = null;
        this.boundSelects = new WeakSet();
        this.searchable = root.dataset.searchable === 'true';
        this.clearable = root.dataset.clearable === 'true';
        this.remoteUrl = root.dataset.remoteUrl || null;
        this.remoteController = null;
        this.remoteRequestToken = 0;
        this.remoteSearchTimer = null;
        this.remotePage = 0;
        this.remoteMore = false;
        this.remoteLoading = false;
        this.remoteQuery = '';

        root.addEventListener('click', (event) => this.onClick(event));
        root.addEventListener('input', (event) => this.onInput(event));
        root.addEventListener('keydown', (event) => this.onKeydown(event));
        root.addEventListener('scroll', (event) => {
            if (event.target === this.optionsBox) this.loadNextPageIfNeeded();
        }, true);
        this.refresh();
    }

    get trigger() {
        return this.root.querySelector('[data-select2-trigger]');
    }

    get panel() {
        return this.root.querySelector('[data-select2-panel]');
    }

    get optionsBox() {
        return this.root.querySelector('[data-select2-options]');
    }

    get searchInput() {
        return this.root.querySelector('[data-select2-search]');
    }

    get optionElements() {
        return Array.from(this.select.options).filter((option) => !option.hasAttribute('data-select2-placeholder'));
    }

    onClick(event) {
        const clearButton = event.target.closest('[data-select2-clear]');
        if (clearButton) {
            event.preventDefault();
            this.clear();
            return;
        }

        const removeButton = event.target.closest('[data-select2-remove]');
        if (removeButton) {
            event.preventDefault();
            event.stopPropagation();
            this.removeValue(removeButton.dataset.select2Remove);
            this.trigger?.focus();
            return;
        }

        const optionButton = event.target.closest('[data-select2-option]');
        if (optionButton) {
            event.preventDefault();
            this.choose(optionButton.dataset.value);
            return;
        }

        if (event.target.closest('[data-select2-trigger]')) {
            event.preventDefault();
            this.isOpen() ? this.close() : this.open();
        }
    }

    onInput(event) {
        if (event.target.matches('[data-select2-search]')) {
            if (this.remoteUrl) {
                window.clearTimeout(this.remoteSearchTimer);
                this.remoteController?.abort();
                this.remoteRequestToken++;
                this.remoteLoading = false;
                this.remoteMore = false;
                this.remotePage = 0;
                this.remoteQuery = event.target.value.trim();
                this.root.querySelector('[data-select2-loading]')?.setAttribute('hidden', '');
                this.root.classList.remove('is-loading');
                this.renderOptions(event.target.value);
                this.remoteSearchTimer = window.setTimeout(() => this.fetchRemote(event.target.value), 250);
            } else {
                this.renderOptions(event.target.value);
            }
        }
    }

    onKeydown(event) {
        if (event.target.matches('[data-select2-search]')) {
            if (event.key === 'Escape') {
                event.preventDefault();
                this.close(true);
            } else if (event.key === 'ArrowDown') {
                event.preventDefault();
                this.focusOption(1);
            } else if (event.key === 'ArrowUp') {
                event.preventDefault();
                this.focusOption(-1);
            }
            return;
        }

        if (event.target.matches('[data-select2-option]')) {
            const visibleOptions = this.visibleOptionButtons();
            const index = visibleOptions.indexOf(event.target);

            if (event.key === 'ArrowDown' || event.key === 'ArrowUp') {
                event.preventDefault();
                const offset = event.key === 'ArrowDown' ? 1 : -1;
                visibleOptions[(index + offset + visibleOptions.length) % visibleOptions.length]?.focus();
            } else if (event.key === 'Home' || event.key === 'End') {
                event.preventDefault();
                (event.key === 'Home' ? visibleOptions[0] : visibleOptions.at(-1))?.focus();
            } else if (event.key === 'Escape') {
                event.preventDefault();
                this.close(true);
            }
            return;
        }

        if (!event.target.matches('[data-select2-trigger]')) return;

        if (event.key === 'ArrowDown' || event.key === 'Enter' || event.key === ' ') {
            event.preventDefault();
            if (this.isOpen()) {
                this.focusOption(1);
            } else {
                this.open();
            }
        } else if (event.key === 'ArrowUp') {
            event.preventDefault();
            this.open();
            this.focusOption(-1);
        } else if (event.key === 'Escape' && this.isOpen()) {
            event.preventDefault();
            this.close();
        } else if (event.key === 'Backspace' && this.multiple && !this.searchable) {
            const selected = this.selectedOptions();
            if (selected.length) this.removeValue(selected.at(-1).value);
        }
    }

    selectedOptions() {
        return this.optionElements.filter((option) => option.selected);
    }

    refresh() {
        const currentSelect = this.root.querySelector('[data-select2-native]');
        if (!currentSelect) return;

        if (currentSelect !== this.select) {
            this.select = currentSelect;
            this.multiple = this.select.multiple;

            if (!this.boundSelects.has(this.select)) {
                this.select.addEventListener('change', () => this.sync());
                this.select.form?.addEventListener('reset', () => window.setTimeout(() => this.sync(), 0));
                this.boundSelects.add(this.select);
            }
        }

        this.root.classList.add('is-enhanced');
        this.select.tabIndex = -1;
        this.select.setAttribute('aria-hidden', 'true');
        this.select.required = false;
        this.sync();

        if (this.remoteUrl && this.optionElements.some((option) => option.hasAttribute('data-select2-pending'))) {
            this.fetchRemote('');
        }
    }

    sync() {
        const trigger = this.trigger;
        const value = this.root.querySelector('[data-select2-value]');
        if (!trigger || !value) return;

        trigger.setAttribute('aria-disabled', String(this.select.disabled));
        trigger.setAttribute('tabindex', this.select.disabled ? '-1' : '0');
        this.root.classList.toggle('is-disabled', this.select.disabled);

        const selected = this.selectedOptions();
        value.replaceChildren();

        if (selected.length === 0) {
            const placeholder = document.createElement('span');
            placeholder.className = 'ui-select2__placeholder';
            placeholder.textContent = this.select.querySelector('[data-select2-placeholder]')?.textContent.trim()
                || this.root.dataset.placeholder
                || 'انتخاب کنید';
            value.appendChild(placeholder);
        } else if (this.multiple) {
            const chips = document.createElement('span');
            chips.className = 'ui-select2__chips';

            selected.forEach((option) => {
                const chip = document.createElement('span');
                chip.className = 'ui-select2__chip';

                const text = document.createElement('span');
                text.textContent = option.textContent.trim();
                chip.appendChild(text);

                if (this.clearable && !this.select.disabled) {
                    const remove = document.createElement('button');
                    remove.type = 'button';
                    remove.className = 'ui-select2__chip-remove';
                    remove.dataset.select2Remove = option.value;
                    remove.setAttribute('aria-label', `حذف ${option.textContent.trim()}`);
                    remove.textContent = '×';
                    chip.appendChild(remove);
                }

                chips.appendChild(chip);
            });

            value.appendChild(chips);
        } else {
            value.textContent = selected[0].textContent.trim();
        }

        const clearButton = this.root.querySelector('[data-select2-clear]');
        if (clearButton) clearButton.hidden = !this.clearable || selected.length === 0 || this.select.disabled;

        if (this.isOpen()) this.renderOptions(this.searchInput?.value ?? '');
    }

    renderOptions(query = '') {
        const box = this.optionsBox;
        if (!box) return;

        const normalizedQuery = this.remoteUrl && this.remotePage > 0
            ? ''
            : query.trim().toLocaleLowerCase();
        const fragment = document.createDocumentFragment();
        let visibleCount = 0;
        let activeGroup = null;

        this.optionElements.forEach((option) => {
            const label = option.textContent.trim();
            const group = option.parentElement?.tagName === 'OPTGROUP' ? option.parentElement.label : null;
            const searchText = `${label} ${group ?? ''}`.toLocaleLowerCase();
            if (normalizedQuery && !searchText.includes(normalizedQuery)) return;

            if (group && group !== activeGroup) {
                const heading = document.createElement('div');
                heading.className = 'ui-select2__group';
                heading.textContent = group;
                fragment.appendChild(heading);
                activeGroup = group;
            } else if (!group) {
                activeGroup = null;
            }

            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'ui-select2__option';
            button.dataset.select2Option = '';
            button.dataset.value = option.value;
            button.setAttribute('role', 'option');
            button.setAttribute('aria-selected', String(option.selected));
            button.disabled = option.disabled || (option.parentElement?.tagName === 'OPTGROUP' && option.parentElement.disabled);

            const text = document.createElement('span');
            text.className = 'ui-select2__option-label';
            text.textContent = label;
            button.appendChild(text);

            const check = document.createElement('span');
            check.className = 'ui-select2__check';
            check.setAttribute('aria-hidden', 'true');
            check.textContent = option.selected ? '✓' : '';
            button.appendChild(check);

            fragment.appendChild(button);
            visibleCount++;
        });

        box.replaceChildren(fragment);
        const empty = this.root.querySelector('[data-select2-empty]');
        if (empty) empty.hidden = visibleCount > 0;
    }

    async fetchRemote(query = '', page = 1) {
        if (!this.remoteUrl) return;
        if (page > 1 && (this.remoteLoading || !this.remoteMore || query.trim() !== this.remoteQuery)) return;

        if (page === 1) {
            this.remotePage = 0;
            this.remoteMore = false;
            this.remoteQuery = query.trim();
        }

        const requestToken = ++this.remoteRequestToken;
        this.remoteController?.abort();
        this.remoteController = new AbortController();
        this.remoteLoading = true;

        const url = new URL(this.remoteUrl, window.location.href);
        url.searchParams.set('page', String(page));
        if (query.trim()) url.searchParams.set('search', query.trim());
        else url.searchParams.delete('search');

        this.selectedOptions().forEach((option) => {
            url.searchParams.append('selected_ids[]', option.value);
        });

        const loading = this.root.querySelector('[data-select2-loading]');
        const empty = this.root.querySelector('[data-select2-empty]');
        if (empty) {
            empty.textContent = 'گزینه‌ای پیدا نشد.';
            empty.hidden = true;
        }
        if (loading) loading.hidden = false;
        this.root.classList.add('is-loading');

        try {
            const response = await fetch(url, {
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
                signal: this.remoteController.signal,
            });

            if (!response.ok) throw new Error('Unable to load select options.');

            const payload = await response.json();
            if (requestToken !== this.remoteRequestToken) return;

            this.remotePage = page;
            this.remoteMore = payload.pagination?.more === true;
            this.applyRemoteResults(Array.isArray(payload.results) ? payload.results : [], query, page > 1);
        } catch (error) {
            if (error.name === 'AbortError' || requestToken !== this.remoteRequestToken) return;

            if (empty) {
                empty.textContent = 'دریافت گزینه‌ها ناموفق بود. دوباره تلاش کنید.';
                empty.hidden = false;
            }
        } finally {
            if (requestToken === this.remoteRequestToken) {
                this.remoteLoading = false;
                if (loading) loading.hidden = true;
                this.root.classList.remove('is-loading');
                this.loadNextPageIfNeeded();
            }
        }
    }

    loadNextPageIfNeeded() {
        const box = this.optionsBox;
        if (!this.remoteUrl || !this.isOpen() || this.remoteLoading || !this.remoteMore || !box) return;

        if (box.scrollTop + box.clientHeight >= box.scrollHeight - 48) {
            this.fetchRemote(this.remoteQuery, this.remotePage + 1);
        }
    }

    applyRemoteResults(results, query, append = false) {
        const selected = new Set(this.selectedOptions().map((option) => option.value));
        const preserved = (append ? this.optionElements : this.selectedOptions())
            .filter((option) => !option.hasAttribute('data-select2-pending'));
        const options = [];
        const seen = new Set();
        const scrollTop = append ? this.optionsBox?.scrollTop ?? 0 : 0;

        preserved.forEach((option) => {
            if (seen.has(option.value)) return;
            const copy = option.cloneNode(true);
            copy.selected = selected.has(copy.value);
            options.push(copy);
            seen.add(copy.value);
        });

        results.forEach((result) => {
            if (result.id === undefined || result.id === null || result.text === undefined || result.text === null) return;

            const value = String(result.id);
            if (seen.has(value)) return;
            seen.add(value);

            const option = document.createElement('option');
            option.value = value;
            option.textContent = String(result.text);
            option.selected = selected.has(value);
            option.disabled = result.disabled === true;
            options.push(option);
        });

        const retainedSelection = new Set([...selected].filter((value) => seen.has(value)));

        const placeholder = this.select.querySelector('[data-select2-placeholder]')?.cloneNode(true);
        if (placeholder) placeholder.selected = retainedSelection.size === 0;
        this.select.replaceChildren(...(placeholder ? [placeholder, ...options] : options));
        if (!placeholder && !this.multiple && retainedSelection.size === 0) this.select.selectedIndex = -1;

        const empty = this.root.querySelector('[data-select2-empty]');
        if (empty) empty.textContent = 'گزینه‌ای پیدا نشد.';
        this.sync();
        this.renderOptions(query);
        if (this.optionsBox) this.optionsBox.scrollTop = scrollTop;
    }

    choose(value) {
        const option = this.optionElements.find((candidate) => candidate.value === value);
        if (!option || option.disabled || this.select.disabled) return;

        if (this.multiple) {
            option.selected = !option.selected;
        } else {
            this.optionElements.forEach((candidate) => { candidate.selected = candidate === option; });
        }

        this.dispatchChange();

        if (this.multiple) {
            this.searchInput?.focus();
        } else {
            this.close(true);
        }
    }

    removeValue(value) {
        const option = this.optionElements.find((candidate) => candidate.value === value);
        if (!option || this.select.disabled) return;

        option.selected = false;
        this.dispatchChange();
    }

    clear() {
        if (this.select.disabled) return;
        this.optionElements.forEach((option) => { option.selected = false; });

        const placeholder = this.select.querySelector('[data-select2-placeholder]');
        if (placeholder && !this.multiple) placeholder.selected = true;

        this.dispatchChange();
        this.trigger?.focus();
    }

    dispatchChange() {
        this.select.dispatchEvent(new Event('change', { bubbles: true }));
    }

    open() {
        if (this.select.disabled || !this.panel || !this.trigger) return;
        this.closeAll();
        this.panel.hidden = false;
        this.root.classList.add('is-open');
        this.trigger.setAttribute('aria-expanded', 'true');
        if (this.searchInput) {
            this.searchInput.value = '';
            this.searchInput.tabIndex = 0;
            this.searchInput.focus();
        } else {
            this.focusOption(1);
        }

        if (this.remoteUrl) this.fetchRemote(this.searchInput?.value ?? '');
        else this.renderOptions(this.searchInput?.value ?? '');
    }

    close(restoreFocus = false) {
        if (!this.panel || !this.trigger) return;
        this.panel.hidden = true;
        this.root.classList.remove('is-open');
        this.trigger.setAttribute('aria-expanded', 'false');
        if (this.searchInput) this.searchInput.tabIndex = -1;
        if (restoreFocus) this.trigger.focus();
    }

    closeAll() {
        document.querySelectorAll(`${selector}.is-open`).forEach((root) => {
            if (root !== this.root) instances.get(root)?.close();
        });
    }

    isOpen() {
        return this.root.classList.contains('is-open');
    }

    visibleOptionButtons() {
        return Array.from(this.optionsBox?.querySelectorAll('[data-select2-option]:not(:disabled)') ?? []);
    }

    focusOption(direction) {
        const options = this.visibleOptionButtons();
        const target = direction < 0 ? options.at(-1) : options[0];
        target?.focus();
    }
}

function initialize(root = document) {
    if (root.matches?.(selector) && !instances.has(root)) {
        const instance = new CustomSelect2(root);
        instances.set(root, instance);
    }

    root.querySelectorAll?.(selector).forEach((element) => {
        if (instances.has(element)) return;
        const instance = new CustomSelect2(element);
        instances.set(element, instance);
    });
}

document.addEventListener('click', (event) => {
    if (!(event.target instanceof Element)) return;

    const label = event.target.closest('[data-select2-label]');
    if (label) {
        event.preventDefault();
        const trigger = document.getElementById(label.dataset.select2For);
        const root = trigger?.closest(selector);
        if (root) {
            instances.get(root)?.open();
        }
        return;
    }

    if (event.target.closest(selector)) return;
    document.querySelectorAll(`${selector}.is-open`).forEach((root) => instances.get(root)?.close());
});

const initializeOnReady = () => initialize();
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeOnReady, { once: true });
} else {
    initializeOnReady();
}

document.addEventListener('livewire:init', () => {
    Livewire.hook('morph.added', ({ el }) => initialize(el));
    Livewire.hook('morph.updated', ({ el }) => {
        initialize(el);
        const root = el.matches?.(selector) ? el : el.closest?.(selector);
        if (root) instances.get(root)?.refresh();
        el.querySelectorAll?.(selector).forEach((nested) => instances.get(nested)?.refresh());
    });
});

document.addEventListener('livewire:navigated', initializeOnReady);
