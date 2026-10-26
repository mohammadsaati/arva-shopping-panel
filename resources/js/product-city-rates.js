function initProductCityRates() {
    document.querySelectorAll('[data-product-city-rates]').forEach((section) => {
        const list = section.querySelector('[data-city-rate-list]');
        const template = section.querySelector('[data-city-rate-template]');
        let nextIndex = Number(section.dataset.nextIndex || 0);

        section.querySelector('[data-add-city-rate]')?.addEventListener('click', () => {
            if (list.querySelectorAll('[data-city-rate-row]').length >= 100) return;

            const row = document.createElement('div');
            row.innerHTML = template.innerHTML.replaceAll('__index__', String(nextIndex++));
            const fields = row.firstElementChild;
            list.appendChild(fields);
            fields.querySelector('select')?.focus();
        });

        list.addEventListener('click', (event) => {
            event.target.closest('[data-remove-city-rate]')?.closest('[data-city-rate-row]')?.remove();
        });
    });
}

document.addEventListener('DOMContentLoaded', initProductCityRates);
