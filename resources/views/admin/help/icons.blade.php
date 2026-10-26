<x-panel-layout title="فهرست آیکون‌ها" sub-title="آیکون‌های Filled را پیدا کنید و کلاس آمادهٔ استفاده را کپی کنید.">
    @push('styles')
        <link rel="stylesheet" href="{{ versioned_assets('assets/karin/styles/admin-guide.css') }}">
    @endpush

    <x-slot:actions>
        <x-button href="{{ route('admin.help.index') }}" variant="light" icon="ki-filled ki-arrow-right">راهنمای پنل</x-button>
    </x-slot:actions>

    @php
        $abstractIcons = array_values(array_filter($icons, static fn (string $name): bool => $name === 'abstract' || str_starts_with($name, 'abstract-')));
        $otherIcons = array_values(array_filter($icons, static fn (string $name): bool => $name !== 'abstract' && ! str_starts_with($name, 'abstract-')));
    @endphp

    <section class="admin-icon-directory" data-admin-icon-directory>
        <div class="admin-icon-directory__panel">
            <div class="admin-icon-directory__toolbar" role="search">
                <div class="admin-icon-directory__search-wrap">
                    <i class="ki-filled ki-magnifier" aria-hidden="true"></i>
                    <input id="admin-icon-search" type="search" dir="ltr" autocomplete="off" aria-label="جستجوی آیکون" placeholder="Search icons...">
                    <button type="button" data-clear-search aria-label="پاک کردن جستجو" hidden>×</button>
                </div>
                <div class="admin-icon-directory__style-picker" role="group" aria-label="سبک آیکون">
                    <span class="admin-icon-directory__style-label">سبک نمایش</span>
                    <span class="admin-icon-directory__style-option"><i aria-hidden="true"></i> Filled</span>
                </div>
            </div>

            <div class="admin-icon-directory__summary">
                <span><i class="ki-filled ki-check" aria-hidden="true"></i> فهرست محلی و آفلاین</span>
                <p aria-live="polite"><strong id="admin-icon-count">{{ number_format(count($icons)) }}</strong> آیکون</p>
            </div>

            <div id="admin-icon-groups" class="admin-icon-directory__groups">
                @if (count($abstractIcons) > 0)
                    <section class="admin-icon-group" data-icon-group>
                        <h2>Abstract</h2>
                        <div class="admin-icon-directory__grid">
                            @foreach ($abstractIcons as $name)
                                <button class="admin-icon-card" type="button" data-icon-name="{{ $name }}" data-icon-class="ki-filled ki-{{ $name }}" title="کپی کلاس ki-filled ki-{{ $name }}" aria-label="کپی کلاس ki-filled ki-{{ $name }}">
                                    <span class="admin-icon-card__preview"><i class="ki-filled ki-{{ $name }}" aria-hidden="true"></i><span class="admin-icon-card__copy-hint"><i class="ki-filled ki-copy" aria-hidden="true"></i> کپی</span><span class="admin-icon-card__copied-hint"><i class="ki-filled ki-check" aria-hidden="true"></i> کپی شد</span></span>
                                    <span class="admin-icon-card__name" dir="ltr">{{ $name }}</span>
                                </button>
                            @endforeach
                        </div>
                    </section>
                @endif

                @if (count($otherIcons) > 0)
                    <section class="admin-icon-group" data-icon-group>
                        <h2>سایر آیکون‌ها</h2>
                        <div class="admin-icon-directory__grid">
                            @foreach ($otherIcons as $name)
                                <button class="admin-icon-card" type="button" data-icon-name="{{ $name }}" data-icon-class="ki-filled ki-{{ $name }}" title="کپی کلاس ki-filled ki-{{ $name }}" aria-label="کپی کلاس ki-filled ki-{{ $name }}">
                                    <span class="admin-icon-card__preview"><i class="ki-filled ki-{{ $name }}" aria-hidden="true"></i><span class="admin-icon-card__copy-hint"><i class="ki-filled ki-copy" aria-hidden="true"></i> کپی</span><span class="admin-icon-card__copied-hint"><i class="ki-filled ki-check" aria-hidden="true"></i> کپی شد</span></span>
                                    <span class="admin-icon-card__name" dir="ltr">{{ $name }}</span>
                                </button>
                            @endforeach
                        </div>
                    </section>
                @endif
            </div>

            <div class="admin-icon-directory__empty" data-icon-empty hidden>
                <i class="ki-filled ki-magnifier" aria-hidden="true"></i>
                <strong>آیکونی پیدا نشد</strong>
                <span>عبارت جستجو را تغییر دهید.</span>
            </div>
        </div>
        <span id="admin-icon-copy-status" class="sr-only" role="status" aria-live="polite"></span>
    </section>

@push('scripts')
    <script>
        (() => {
            const directory = document.querySelector('[data-admin-icon-directory]');
            if (!directory) return;

            const search = document.getElementById('admin-icon-search');
            const groups = document.getElementById('admin-icon-groups');
            const count = document.getElementById('admin-icon-count');
            const copyStatus = document.getElementById('admin-icon-copy-status');
            const emptyState = directory.querySelector('[data-icon-empty]');
            const cards = Array.from(directory.querySelectorAll('[data-icon-name]'));
            const groupSections = Array.from(directory.querySelectorAll('[data-icon-group]'));
            const clearSearchButton = directory.querySelector('[data-clear-search]');
            const numberFormat = new Intl.NumberFormat('fa-IR');

            const copyText = async (value) => {
                if (navigator.clipboard?.writeText) {
                    try {
                        await navigator.clipboard.writeText(value);
                        return;
                    } catch (error) {
                        // Fall back to document selection when clipboard permission is unavailable.
                    }
                }

                const temporaryInput = document.createElement('textarea');
                temporaryInput.value = value;
                temporaryInput.setAttribute('readonly', '');
                temporaryInput.style.position = 'fixed';
                temporaryInput.style.opacity = '0';
                document.body.appendChild(temporaryInput);
                temporaryInput.select();
                const copied = document.execCommand('copy');
                temporaryInput.remove();

                if (!copied) throw new Error('Clipboard copy was rejected.');
            };

            const render = () => {
                const query = search.value.trim().toLowerCase();
                let visibleCount = 0;

                cards.forEach((card) => {
                    const name = card.dataset.iconName.toLowerCase();
                    const isVisible = name.includes(query) || `ki-filled ki-${name}`.includes(query);
                    card.hidden = !isVisible;
                    if (isVisible) visibleCount++;
                });

                count.textContent = numberFormat.format(visibleCount);
                if (emptyState) emptyState.hidden = visibleCount !== 0;
                if (clearSearchButton) clearSearchButton.hidden = query.length === 0;
                groupSections.forEach((section) => {
                    section.hidden = !section.querySelector('[data-icon-name]:not([hidden])');
                });
            };

            search.addEventListener('input', render);
            clearSearchButton?.addEventListener('click', () => {
                search.value = '';
                render();
                search.focus();
            });
            groups?.addEventListener('click', async (event) => {
                const card = event.target.closest('[data-icon-class]');
                if (!card) return;

                try {
                    await copyText(card.dataset.iconClass);
                    card.classList.add('is-copied');
                    copyStatus.textContent = `کلاس ${card.dataset.iconClass} کپی شد`;
                    window.setTimeout(() => card.classList.remove('is-copied'), 1400);
                } catch (error) {
                    copyStatus.textContent = 'کپی خودکار انجام نشد؛ نام کلاس را از عنوان آیکون بردارید.';
                }
            });

            document.addEventListener('keydown', (event) => {
                if (event.key === '/' && !['INPUT', 'TEXTAREA'].includes(document.activeElement?.tagName)) {
                    event.preventDefault();
                    search.focus();
                }

                if (event.key === 'Escape' && document.activeElement === search) {
                    search.value = '';
                    render();
                }
            });

            render();
        })();
    </script>
@endpush
</x-panel-layout>
