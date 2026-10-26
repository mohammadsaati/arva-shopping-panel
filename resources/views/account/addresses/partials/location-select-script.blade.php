@push('scripts')
    <script>
        (() => {
            document.querySelectorAll('[data-address-form]').forEach((form) => {
                const province = form.querySelector('[data-address-province]');
                const city = form.querySelector('[data-address-city]');

                if (!province || !city) return;

                const filterCities = (clearSelection = false) => {
                    const provinceName = province.selectedOptions[0]?.textContent.trim() ?? '';

                    city.querySelectorAll('optgroup').forEach((group) => {
                        const isVisible = provinceName !== '' && group.label.trim() === provinceName;
                        group.hidden = !isVisible;

                        Array.from(group.options).forEach((option) => {
                            option.disabled = !isVisible;
                        });
                    });

                    const selected = city.selectedOptions[0];
                    if (clearSelection || (selected?.value && selected.parentElement?.label !== provinceName)) {
                        city.value = '';
                    }
                };

                province.addEventListener('change', () => filterCities(true));
                filterCities();
            });
        })();
    </script>
@endpush
