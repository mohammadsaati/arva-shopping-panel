/**
 * File input with image preview + lightbox + drag & drop.
 * Auto-initializes every [data-file-input] element.
 */
const DRAGOVER_CLASSES = ['border-primary', 'bg-gray-50', 'dark:bg-gray-700/40'];

class FileInput {
    constructor(el) {
        this.el          = el;
        this.input       = el.querySelector('[data-file-input-field]');
        this.dropLabel   = el.querySelector('[data-file-input-label]');
        this.previewImg  = el.querySelector('[data-file-input-preview-img]');
        this.previewBtn  = el.querySelector('[data-file-input-preview-btn]');
        this.placeholder = el.querySelector('[data-file-input-placeholder]');
        this.clearBtn    = el.querySelector('[data-file-input-clear]');
        this.fileName    = el.querySelector('[data-file-input-filename]');
        this.hint        = el.querySelector('[data-file-input-hint]');
        this.lightbox    = el.querySelector('[data-file-input-lightbox]');
        this.lightboxImg = el.querySelector('[data-file-input-lightbox-img]');

        this.currentImage = this.input?.dataset.currentImage || '';
        this.multiple      = this.input?.multiple ?? false;
        this.selectedFiles = [];
        this.dragCounter   = 0;

        this.bind();
    }

    bind() {
        this.input?.addEventListener('change', (e) => this.onChange(e));
        this.clearBtn?.addEventListener('click', (e) => {
            e.preventDefault();
            this.clear();
        });
        this.previewBtn?.addEventListener('click', () => this.openLightbox());

        if (this.lightbox) {
            this.lightbox.querySelectorAll('[data-file-input-lightbox-close]').forEach((btn) => {
                btn.addEventListener('click', () => this.closeLightbox());
            });
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') this.closeLightbox();
            });
        }

        this.bindDragAndDrop();
    }

    bindDragAndDrop() {
        if (!this.dropLabel || !this.input) return;

        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach((evt) => {
            this.dropLabel.addEventListener(evt, (e) => {
                e.preventDefault();
                e.stopPropagation();
            });
        });

        this.dropLabel.addEventListener('dragenter', () => {
            this.dragCounter++;
            this.setDragOver(true);
        });

        this.dropLabel.addEventListener('dragleave', () => {
            this.dragCounter = Math.max(0, this.dragCounter - 1);
            if (this.dragCounter === 0) this.setDragOver(false);
        });

        this.dropLabel.addEventListener('drop', (e) => {
            this.dragCounter = 0;
            this.setDragOver(false);

            const files = e.dataTransfer?.files;
            if (!files || files.length === 0 || this.input.disabled) return;

            this.input.files = files;
            this.input.dispatchEvent(new Event('change', { bubbles: true }));
        });
    }

    setDragOver(on) {
        DRAGOVER_CLASSES.forEach((cls) => this.dropLabel.classList.toggle(cls, on));
    }

    onChange(e) {
        let files = Array.from(e.target.files ?? []);

        if (files.length === 0) {
            this.setPreview(this.currentImage);
            this.setFileName(null);
            this.toggleClear(false);
            return;
        }

        if (this.multiple) {
            const mergedFiles = this.mergeFiles(this.selectedFiles, files);

            if (this.syncFiles(mergedFiles)) {
                this.selectedFiles = mergedFiles;
            } else {
                // Keep the native multi-select behavior if DataTransfer is unavailable.
                this.selectedFiles = files;
            }

            files = this.selectedFiles;
        }

        const file = files[0];

        this.toggleClear(true);
        this.setFileName(
            files.length > 1
                ? `${files.length} فایل انتخاب شد`
                : file.name
        );

        if (!this.multiple) {
            this.setPreview(URL.createObjectURL(file));
        }
    }

    mergeFiles(existingFiles, incomingFiles) {
        return [...existingFiles, ...incomingFiles];
    }

    syncFiles(files) {
        if (!this.input || typeof DataTransfer === 'undefined') return false;

        try {
            const transfer = new DataTransfer();
            files.forEach((file) => transfer.items.add(file));
            this.input.files = transfer.files;

            return true;
        } catch {
            return false;
        }
    }

    clear() {
        this.selectedFiles = [];
        if (this.input) this.input.value = '';
        this.setPreview(this.currentImage);
        this.setFileName(null);
        this.toggleClear(false);
    }

    setPreview(url) {
        if (!this.previewImg || !this.previewBtn || !this.placeholder) return;

        if (url) {
            this.previewImg.src = url;
            if (this.lightboxImg) this.lightboxImg.src = url;
            this.previewBtn.classList.remove('hidden');
            this.placeholder.classList.add('hidden');
        } else {
            this.previewImg.removeAttribute('src');
            this.previewBtn.classList.add('hidden');
            this.placeholder.classList.remove('hidden');
        }
    }

    setFileName(name) {
        if (!this.fileName) return;
        if (name) {
            this.fileName.textContent = name;
            this.fileName.classList.remove('hidden');
            this.hint?.classList.add('hidden');
        } else {
            this.fileName.textContent = '';
            this.fileName.classList.add('hidden');
            this.hint?.classList.remove('hidden');
        }
    }

    toggleClear(show) {
        this.clearBtn?.classList.toggle('hidden', !show);
    }

    openLightbox() {
        this.lightbox?.classList.remove('hidden');
    }

    closeLightbox() {
        this.lightbox?.classList.add('hidden');
    }
}

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-file-input]').forEach((el) => new FileInput(el));
});

export default FileInput;
