class ImageGalleryPicker {
    constructor(el) {
        this.el = el;
        this.slots = [...el.querySelectorAll('[data-image-gallery-slot]')];
        this.addButton = el.querySelector('[data-image-gallery-add]');
        this.counter = el.querySelector('[data-image-gallery-counter]');
        this.limitMessage = el.querySelector('[data-image-gallery-limit-message]');
        this.maxFiles = Number(el.dataset.maxFiles || 10);
        this.existingCount = Number(el.dataset.existingCount || 0);
        this.initialSlots = Number(el.dataset.initialSlots || 3);
        this.form = el.closest('form');
        this.manualVisibleCount = this.visibleCount();

        this.bind();
        this.refresh();
    }

    bind() {
        this.addButton?.addEventListener('click', () => this.addSlot());

        this.slots.forEach((slot) => {
            slot.querySelector('input[type="file"]')?.addEventListener('change', () => this.refresh());
            slot.querySelector('[data-file-input-clear]')?.addEventListener('click', () => this.refresh());
        });

        this.form?.querySelectorAll('input[name^="delete_image_ids"]').forEach((input) => {
            input.addEventListener('change', () => this.refresh());
        });

        this.form?.addEventListener('submit', (event) => {
            if (this.selectedCount() > this.capacity()) {
                event.preventDefault();
                this.showLimitMessage();
            }
        });
    }

    capacity() {
        const deletedCount = this.form
            ? this.form.querySelectorAll('input[name^="delete_image_ids"]:checked').length
            : 0;

        return Math.max(0, Math.min(this.maxFiles, this.maxFiles - this.existingCount + deletedCount));
    }

    selectedCount() {
        return this.slots.reduce((count, slot) => {
            const input = slot.querySelector('input[type="file"]');
            return count + (input?.files?.length ? 1 : 0);
        }, 0);
    }

    visibleCount() {
        return this.slots.filter((slot) => !slot.hidden).length;
    }

    refresh() {
        const capacity = this.capacity();
        const filledSlots = this.slots
            .filter((slot) => slot.querySelector('input[type="file"]')?.files?.length)
            .map((slot) => Number(slot.dataset.slotIndex));
        const highestFilledSlot = filledSlots.length ? Math.max(...filledSlots) + 1 : 0;
        const visibleTarget = Math.min(
            capacity,
            Math.max(this.initialSlots, this.manualVisibleCount, highestFilledSlot)
        );

        this.slots.forEach((slot, index) => {
            const hasFile = Boolean(slot.querySelector('input[type="file"]')?.files?.length);
            const visible = index < visibleTarget || hasFile;
            slot.hidden = !visible;
            slot.classList.toggle('hidden', !visible);
        });

        const selected = this.selectedCount();
        const number = new Intl.NumberFormat('fa-IR');
        if (this.counter) {
            this.counter.textContent = `${number.format(selected)} تصویر انتخاب شده از ${number.format(capacity)} جایگاه مجاز`;
        }

        const canAddSlot = this.visibleCount() < capacity;
        if (this.addButton) {
            this.addButton.disabled = !canAddSlot;
            this.addButton.classList.toggle('opacity-50', !canAddSlot);
            this.addButton.classList.toggle('cursor-not-allowed', !canAddSlot);
            this.addButton.setAttribute('aria-disabled', String(!canAddSlot));
        }

        if (selected <= capacity) {
            this.hideLimitMessage();
        } else {
            this.showLimitMessage();
        }
    }

    addSlot() {
        if (this.visibleCount() >= this.capacity()) return;

        const slot = this.slots.find((candidate) => candidate.hidden);
        if (!slot) return;

        this.manualVisibleCount = Math.max(this.manualVisibleCount, Number(slot.dataset.slotIndex) + 1);
        slot.hidden = false;
        slot.classList.remove('hidden');
        this.refresh();
        slot.querySelector('input[type="file"]')?.click();
    }

    showLimitMessage() {
        if (!this.limitMessage) return;
        this.limitMessage.textContent = 'تعداد عکس‌های انتخاب‌شده از ظرفیت گالری بیشتر است؛ برای افزودن عکس، تصاویر فعلی بیشتری را برای حذف انتخاب کنید.';
        this.limitMessage.classList.remove('hidden');
    }

    hideLimitMessage() {
        if (!this.limitMessage) return;
        this.limitMessage.textContent = '';
        this.limitMessage.classList.add('hidden');
    }
}

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-image-gallery-picker]').forEach((el) => new ImageGalleryPicker(el));
});

export default ImageGalleryPicker;
