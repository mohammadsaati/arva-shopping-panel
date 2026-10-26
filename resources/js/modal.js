function resolveModal(raw) {
    if (!raw) return null;
    const id = raw.startsWith('#') ? raw.slice(1) : raw;
    return document.getElementById(id);
}

// Modals are often authored inside table cells / cards that have
// `overflow-x-auto`, which clips `position: fixed` descendants in every
// browser. Move every `.app-modal` to be a direct child of <body> so it can
// never end up clipped by an ancestor's overflow, no matter where it's
// rendered from (including rows injected later by the AJAX DataTable).
function portalModal(modal) {
    if (modal && modal.parentElement !== document.body) {
        document.body.appendChild(modal);
    }
}

function portalModalsWithin(root) {
    if (!root.querySelectorAll) return;
    root.querySelectorAll('.app-modal').forEach(portalModal);
}

document.addEventListener('DOMContentLoaded', function () {
    portalModalsWithin(document);
});

new MutationObserver(function (mutations) {
    mutations.forEach(function (mutation) {
        mutation.addedNodes.forEach(function (node) {
            if (node.nodeType !== 1) return;
            if (node.classList && node.classList.contains('app-modal')) {
                portalModal(node);
            } else {
                portalModalsWithin(node);
            }
        });
    });
}).observe(document.documentElement, { childList: true, subtree: true });

const TRANSITION_MS = 300;
const returnFocus = new WeakMap();
const closingModals = new WeakSet();
let bodyWasLocked = false;

function activeModal() {
    return [...document.querySelectorAll('.app-modal.flex')].filter(modal => !closingModals.has(modal)).at(-1);
}

function focusableElements(modal) {
    return [...modal.querySelectorAll('a[href], button, input, select, textarea, [tabindex]')]
        .filter(element => element.tabIndex >= 0 && !element.disabled && element.getClientRects().length);
}

function openModal(modal) {
    if (!modal || !modal.classList.contains('hidden')) return;
    const dialog = modal.querySelector(':scope > .app-modal-dialog');
    returnFocus.set(modal, document.activeElement);
    if (!activeModal()) bodyWasLocked = document.body.classList.contains('overflow-hidden');

    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.classList.add('overflow-hidden');

    // Two rAFs: the first lets the browser apply `flex` (from `hidden`), the
    // second flips the transition target so it actually animates instead of
    // jumping straight to its end state.
    requestAnimationFrame(() => {
        requestAnimationFrame(() => {
            if (closingModals.has(modal) || modal.classList.contains('hidden')) return;
            modal.classList.remove('opacity-0');
            if (dialog) dialog.classList.remove('opacity-0', 'scale-95', 'translate-y-3');
            (focusableElements(modal)[0] || modal).focus({ preventScroll: true });
        });
    });
}

function closeModal(modal) {
    if (!modal || modal.classList.contains('hidden') || closingModals.has(modal)) return;
    closingModals.add(modal);
    const dialog = modal.querySelector(':scope > .app-modal-dialog');

    modal.classList.add('opacity-0');
    if (dialog) dialog.classList.add('opacity-0', 'scale-95', 'translate-y-3');

    window.setTimeout(() => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        closingModals.delete(modal);
        const remaining = activeModal();
        if (!remaining && !bodyWasLocked) document.body.classList.remove('overflow-hidden');
        const previous = returnFocus.get(modal);
        if (remaining) (focusableElements(remaining)[0] || remaining).focus({ preventScroll: true });
        else if (previous?.isConnected) previous.focus({ preventScroll: true });
    }, window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 0 : TRANSITION_MS);
}

document.addEventListener('click', function (event) {
    const toggleBtn = event.target.closest('[data-app-modal-toggle]');
    if (toggleBtn) {
        openModal(resolveModal(toggleBtn.getAttribute('data-app-modal-toggle')));
        return;
    }

    const dismissBtn = event.target.closest('[data-app-modal-dismiss]');
    if (dismissBtn) {
        const modal = resolveModal(dismissBtn.getAttribute('data-app-modal-dismiss')) || dismissBtn.closest('.app-modal');
        closeModal(modal);
        return;
    }

    const openBackdrop = event.target.closest('.app-modal.flex');
    if (openBackdrop && event.target === openBackdrop) {
        closeModal(openBackdrop);
    }
});

document.addEventListener('keydown', function (event) {
    const modal = activeModal();
    if (!modal) return;
    if (event.key === 'Escape') {
        event.preventDefault();
        closeModal(modal);
    }
    if (event.key === 'Tab') {
        const elements = focusableElements(modal);
        const first = elements[0];
        const last = elements.at(-1);
        if (!first) {
            event.preventDefault();
            modal.focus();
        } else if (!modal.contains(document.activeElement) || document.activeElement === modal) {
            event.preventDefault();
            (event.shiftKey ? last : first).focus();
        } else if ((event.shiftKey && document.activeElement === first) || (!event.shiftKey && document.activeElement === last)) {
            event.preventDefault();
            (event.shiftKey ? last : first).focus();
        }
    }
});
