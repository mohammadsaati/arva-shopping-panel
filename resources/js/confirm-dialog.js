const pendingConfirmations = new WeakMap();
const defaultContent = new WeakMap();
const confirmedForms = new WeakSet();

function openConfirmation(trigger) {
    const dialog = document.getElementById(trigger.dataset.confirmOpen);
    if (!dialog?.matches('[data-confirm-dialog]') || dialog.open) return;

    const isSelect = trigger instanceof HTMLSelectElement;
    const isForm = trigger instanceof HTMLFormElement;
    const formSelect = isForm ? trigger.elements.namedItem(trigger.dataset.confirmSelectionField ?? 'status') : null;
    const selectedControl = isSelect ? trigger : formSelect instanceof HTMLSelectElement ? formSelect : null;
    const value = selectedControl?.value ?? trigger.dataset.confirmValue;
    const selection = selectedControl?.selectedOptions[0]?.textContent?.trim() ?? trigger.dataset.confirmSelection ?? '';

    if (isSelect && value === trigger.dataset.confirmCurrent) return;
    if (isSelect) trigger.value = trigger.dataset.confirmCurrent ?? '';

    const title = dialog.querySelector('[data-confirm-title]');
    const message = dialog.querySelector('[data-confirm-message]');
    const accept = dialog.querySelector('[data-confirm-accept]');
    const cancel = dialog.querySelector('[data-confirm-cancel]');
    const error = dialog.querySelector('[data-confirm-error]');
    const blocked = trigger.dataset.confirmBlock === 'true';

    if (!defaultContent.has(dialog)) {
        defaultContent.set(dialog, {
            title: title.textContent,
            message: message.textContent,
            confirmText: accept.textContent,
            cancelText: cancel.textContent,
        });
    }

    const defaults = defaultContent.get(dialog);

    title.textContent = trigger.dataset.confirmTitle ?? defaults.title;
    message.textContent = (trigger.dataset.confirmMessage ?? defaults.message).replaceAll(':selection', selection);
    accept.textContent = trigger.dataset.confirmText ?? defaults.confirmText;
    cancel.textContent = trigger.dataset.confirmCancelText ?? (blocked ? 'متوجه شدم' : (trigger.dataset.cancelText ?? defaults.cancelText));
    accept.style.display = blocked ? 'none' : '';
    error.textContent = '';
    error.classList.add('hidden');

    pendingConfirmations.set(dialog, { trigger, value, returnFocus: document.activeElement });
    dialog.showModal();
    cancel.focus();
}

document.addEventListener('change', (event) => {
    const trigger = event.target.closest('select[data-confirm-open]');
    if (trigger) openConfirmation(trigger);
});

document.addEventListener('submit', (event) => {
    const form = event.target.closest('form[data-confirm-open]');
    if (!form || confirmedForms.delete(form)) return;

    event.preventDefault();
    openConfirmation(form);
});

document.addEventListener('click', async (event) => {
    const trigger = event.target.closest('button[data-confirm-open], a[data-confirm-open]');
    if (trigger) {
        event.preventDefault();
        openConfirmation(trigger);
        return;
    }

    const cancel = event.target.closest('[data-confirm-cancel]');
    if (cancel) {
        cancel.closest('[data-confirm-dialog]')?.close();
        return;
    }

    const accept = event.target.closest('[data-confirm-accept]');
    if (!accept) return;

    const dialog = accept.closest('[data-confirm-dialog]');
    const pending = pendingConfirmations.get(dialog);
    if (!pending || accept.disabled) return;

    const { trigger: actionTrigger, value } = pending;
    accept.disabled = true;

    try {
        if (actionTrigger.dataset.confirmLivewire) {
            const wireId = actionTrigger.dataset.confirmWireId
                ?? actionTrigger.closest('[wire\\:id]')?.getAttribute('wire:id');
            const component = wireId && window.Livewire?.find(wireId);
            if (!component) throw new Error('Livewire component is unavailable.');

            const args = JSON.parse(actionTrigger.dataset.confirmArgs ?? '[]');
            if (!Array.isArray(args)) throw new Error('Confirmation arguments must be an array.');
            if (value !== undefined) args.push(value);

            await component.call(actionTrigger.dataset.confirmLivewire, ...args);
        } else if (actionTrigger instanceof HTMLFormElement) {
            confirmedForms.add(actionTrigger);
            actionTrigger.requestSubmit();
            queueMicrotask(() => confirmedForms.delete(actionTrigger));
        } else if (actionTrigger.dataset.confirmForm) {
            const form = document.getElementById(actionTrigger.dataset.confirmForm);
            if (!(form instanceof HTMLFormElement)) throw new Error('Confirmation form is unavailable.');
            confirmedForms.add(form);
            form.requestSubmit();
            queueMicrotask(() => confirmedForms.delete(form));
        } else {
            dialog.dispatchEvent(new CustomEvent('confirm-dialog:accepted', {
                bubbles: true,
                detail: { trigger: actionTrigger, value },
            }));
        }

        dialog.close();
    } catch (error) {
        const errorMessage = dialog.querySelector('[data-confirm-error]');
        errorMessage.textContent = actionTrigger.dataset.confirmError ?? errorMessage.dataset.defaultError;
        errorMessage.classList.remove('hidden');
    } finally {
        accept.disabled = false;
    }
}, true);

document.addEventListener('click', (event) => {
    if (event.target.matches('[data-confirm-dialog][open]')) event.target.close();
});

document.addEventListener('close', (event) => {
    if (!event.target.matches('[data-confirm-dialog]')) return;
    const returnFocus = pendingConfirmations.get(event.target)?.returnFocus;
    pendingConfirmations.delete(event.target);
    if (returnFocus?.isConnected) returnFocus.focus();
}, true);
