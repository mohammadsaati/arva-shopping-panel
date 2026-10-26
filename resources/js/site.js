import './select2';
import './product-reviews';
import '../css/product-reviews.css';

/**
 * HeroSlider — carousel for [data-hero-slider] with controls, touch swipe,
 * and autoplay that pauses for focus, hover, reduced motion, or a hidden tab.
 */
class HeroSlider {
    constructor(el) {
        this.el     = el;
        this.track  = el.querySelector('[data-slider-track]');
        this.slides = Array.from(el.querySelectorAll('[data-slide]'));
        this.dots   = Array.from(el.querySelectorAll('[data-slide-dot]'));
        this.status = el.querySelector('[data-slide-status]');
        this.pauseBtn = el.querySelector('[data-slide-pause]');
        this.index  = 0;
        this.paused = false;
        this.touchStartX = null;

        if (!this.track || !this.slides.length) return;

        el.querySelector('[data-slide-prev]')?.addEventListener('click', () => this.go(this.index - 1));
        el.querySelector('[data-slide-next]')?.addEventListener('click', () => this.go(this.index + 1));
        this.dots.forEach((dot) => dot.addEventListener('click', () => this.go(parseInt(dot.dataset.index, 10))));
        this.pauseBtn?.addEventListener('click', () => this.togglePause());

        el.addEventListener('touchstart', (e) => { this.touchStartX = e.touches[0].clientX; }, { passive: true });
        el.addEventListener('touchend', (e) => {
            if (this.touchStartX === null) return;
            const delta = e.changedTouches[0].clientX - this.touchStartX;
            if (Math.abs(delta) > 40) this.go(this.index + (delta > 0 ? 1 : -1));
            this.touchStartX = null;
        }, { passive: true });

        el.addEventListener('keydown', (event) => {
            if (event.key === 'ArrowLeft') {
                event.preventDefault();
                this.go(this.index + 1);
            }
            if (event.key === 'ArrowRight') {
                event.preventDefault();
                this.go(this.index - 1);
            }
        });

        this.render();
        this.startAutoplay();
        el.addEventListener('mouseenter', () => this.stopAutoplay());
        el.addEventListener('mouseleave', () => this.startAutoplay());
        el.addEventListener('focusin', () => this.stopAutoplay());
        el.addEventListener('focusout', () => this.startAutoplay());
        document.addEventListener('visibilitychange', () => document.hidden ? this.stopAutoplay() : this.startAutoplay());
    }

    togglePause() {
        this.paused = !this.paused;
        this.pauseBtn?.setAttribute('aria-pressed', String(this.paused));
        this.pauseBtn?.setAttribute('aria-label', this.paused ? 'ادامه پخش خودکار' : 'توقف پخش خودکار');
        this.paused ? this.stopAutoplay() : this.startAutoplay();
    }

    startAutoplay() {
        this.stopAutoplay();
        if (this.paused || this.slides.length < 2 || window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
        this.autoplay = window.setInterval(() => {
            if (!this.el.isConnected) {
                this.stopAutoplay();
                return;
            }
            if (!document.hidden) this.go(this.index + 1);
        }, 6000);
    }

    stopAutoplay() {
        if (this.autoplay) window.clearInterval(this.autoplay);
        this.autoplay = null;
    }

    go(index) {
        const count = this.slides.length;
        this.index = (index + count) % count;
        this.render();
    }

    render() {
        const count = this.slides.length;
        this.track.style.transform = `translateX(${this.index * 100}%)`;
        this.slides.forEach((slide, i) => {
            slide.setAttribute('aria-hidden', String(i !== this.index));
            slide.inert = i !== this.index;
        });
        this.dots.forEach((dot, i) => dot.setAttribute('aria-current', String(i === this.index)));
        const activeThumb = this.dots[this.index];
        const rail = activeThumb?.closest('.store-hero-carousel__thumbs');
        if (activeThumb && rail) {
            const railBox = rail.getBoundingClientRect();
            const thumbBox = activeThumb.getBoundingClientRect();
            rail.scrollBy({
                left: thumbBox.left - railBox.left - ((railBox.width - thumbBox.width) / 2),
                behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'instant' : 'smooth',
            });
        }
        if (this.status) {
            this.status.textContent = `${String(this.index + 1).padStart(2, '0')} / ${String(count).padStart(2, '0')}`;
        }
    }

}

const amazingCountdownNumberFormatter = new Intl.NumberFormat('fa-IR');

function updateAmazingCountdowns() {

    document.querySelectorAll('[data-amazing-countdown]').forEach((element) => {
        const endTime = Number(element.dataset.amazingEnd);
        const display = element.querySelector('[data-amazing-time]');
        const unitNodes = element.querySelectorAll('[data-deal-unit]');
        if ((!display && unitNodes.length === 0) || !Number.isFinite(endTime) || endTime <= 0) return;

        const secondsLeft = Math.max(0, Math.ceil((endTime - Date.now()) / 1000));
        const days = Math.floor(secondsLeft / 86400);
        const hours = Math.floor((secondsLeft % 86400) / 3600);
        const minutes = Math.floor((secondsLeft % 3600) / 60);
        const seconds = secondsLeft % 60;
        const unitMap = { days, hours, minutes, seconds };

        unitNodes.forEach((node) => {
            const key = node.dataset.dealUnit;
            if (key in unitMap) {
                node.textContent = amazingCountdownNumberFormatter.format(unitMap[key]);
            }
        });

        if (secondsLeft === 0) {
            if (display) display.textContent = 'پایان یافته';
            return;
        }

        if (!display) return;

        display.textContent = [
            [days, 'روز'],
            [hours, 'ساعت'],
            [minutes, 'دقیقه'],
            [seconds, 'ثانیه'],
        ]
            .filter(([value, label]) => value > 0 || label === 'ثانیه')
            .map(([value, label]) => `${amazingCountdownNumberFormatter.format(value)} ${label}`)
            .join(' و ');
    });
}

/**
 * Carousel — horizontal scroll row driven by [data-carousel-prev]/[data-carousel-next]
 * buttons that reference a track via a shared id. Native scroll + scroll-snap does the
 * rest (touch swipe works for free); this just adds arrow-button paging and disables
 * arrows at the edges.
 */
class Carousel {
    constructor(id) {
        this.track   = document.querySelector(`[data-carousel="${id}"] [data-carousel-track]`);
        this.prevBtn = document.querySelector(`[data-carousel-prev="${id}"]`);
        this.nextBtn = document.querySelector(`[data-carousel-next="${id}"]`);
        if (!this.track) return;

        this.prevBtn?.addEventListener('click', () => this.scroll(1));
        this.nextBtn?.addEventListener('click', () => this.scroll(-1));
        this.track.addEventListener('scroll', () => this.updateButtons(), { passive: true });
        window.addEventListener('resize', () => this.updateButtons());

        this.updateButtons();
    }

    scroll(sign) {
        this.track.scrollBy({ left: sign * this.track.clientWidth * 0.85, behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'instant' : 'smooth' });
    }

    updateButtons() {
        const maxScroll = this.track.scrollWidth - this.track.clientWidth;
        const pos = Math.abs(this.track.scrollLeft);

        if (this.prevBtn) this.prevBtn.disabled = pos < 4;
        if (this.nextBtn) this.nextBtn.disabled = maxScroll <= 4 || pos > maxScroll - 4;
    }
}

/**
 * BookingWidget — drives the service-page reservation form [data-booking-widget].
 * On staff/date change it fetches open slots from [data-slots-url] and renders them
 * as buttons; picking one fills [data-booking-start-time] and enables submit.
 */
class BookingWidget {
    constructor(el) {
        this.el = el;
        this.url = el.dataset.slotsUrl;
        this.request = null;
        this.staffSelect = el.querySelector('[data-booking-staff]');
        this.dateInput = el.querySelector('[data-booking-date]');
        this.slotsBox = el.querySelector('[data-booking-slots]');
        this.startTimeInput = el.querySelector('[data-booking-start-time]');
        this.submitBtn = el.querySelector('[data-booking-submit]');
        if (!this.slotsBox || !this.startTimeInput) return;

        this.staffSelect?.addEventListener('change', () => this.fetchSlots());
        this.dateInput?.addEventListener('change', () => this.fetchSlots());
        if (this.staffSelect?.value && this.dateInput?.value) this.fetchSlots();
    }

    fetchSlots() {
        this.request?.abort();
        this.slotsBox.setAttribute('aria-busy', 'false');
        const staffId = this.staffSelect?.value;
        const date = this.dateInput?.value;

        this.startTimeInput.value = '';
        if (this.submitBtn) this.submitBtn.disabled = true;

        if (!staffId || !date) {
            this.slotsBox.innerHTML = '<p class="text-xs text-site-muted col-span-full">ابتدا متخصص و تاریخ را انتخاب کنید</p>';
            return;
        }

        this.slotsBox.innerHTML = '<p class="text-xs text-site-muted col-span-full">در حال بارگذاری…</p>';

        this.request = new AbortController();
        this.slotsBox.setAttribute('aria-busy', 'true');
        fetch(`${this.url}?staff_id=${encodeURIComponent(staffId)}&date=${encodeURIComponent(date)}`, {
            headers: { Accept: 'application/json' },
            signal: this.request.signal,
        })
            .then((res) => {
                if (!res.ok) throw new Error('Unable to load slots');
                return res.json();
            })
            .then((data) => this.renderSlots(data.slots || []))
            .catch((error) => {
                if (error.name === 'AbortError') return;
                this.slotsBox.setAttribute('aria-busy', 'false');
                this.slotsBox.innerHTML = '<p class="text-xs text-red-500 col-span-full">خطا در دریافت ساعت‌های خالی</p>';
            });
    }

    renderSlots(slots) {
        this.slotsBox.setAttribute('aria-busy', 'false');
        if (!slots.length) {
            this.slotsBox.innerHTML = '<p class="text-xs text-site-muted col-span-full">ساعت خالی برای این روز وجود ندارد.</p>';
            return;
        }

        this.slotsBox.innerHTML = '';
        slots.forEach((slot) => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.textContent = slot;
            btn.className = 'booking-slot';
            btn.setAttribute('aria-pressed', 'false');
            btn.addEventListener('click', () => this.selectSlot(slot, btn));
            this.slotsBox.appendChild(btn);
        });
    }

    selectSlot(slot, btn) {
        this.startTimeInput.value = slot;
        if (this.submitBtn) this.submitBtn.disabled = false;

        this.slotsBox.querySelectorAll('button').forEach((b) => {
            b.setAttribute('aria-pressed', 'false');
        });
        btn.setAttribute('aria-pressed', 'true');
    }
}

/** Mobile off-canvas navigation with category and subcategory slides. */
function initMobileMenu() {
    const menu = document.querySelector('[data-mobile-menu]');
    const openButtons = Array.from(document.querySelectorAll('[data-mobile-menu-open], [data-mobile-menu-open-view]'));
    if (!menu || !openButtons.length) return;

    const drawer = menu.querySelector('.user-mobile-menu__drawer');
    const views = Array.from(menu.querySelectorAll('[data-mobile-menu-view]'));
    const viewByName = new Map(views.map((view) => [view.dataset.mobileMenuView, view]));
    const focusableSelector = 'a[href], button:not([disabled]), [tabindex]:not([tabindex="-1"])';
    let viewStack = ['main'];
    let restoreFocus = null;

    const syncViews = () => {
        const activeViewName = viewStack[viewStack.length - 1];
        const activeDepth = viewStack.length - 1;

        views.forEach((view) => {
            const name = view.dataset.mobileMenuView;
            const viewDepth = name === 'main' ? 0 : (name === 'categories' ? 1 : 2);
            const isActive = name === activeViewName;
            const isBefore = !isActive && viewStack.includes(name) && viewDepth < activeDepth;

            view.classList.toggle('is-active', isActive);
            view.classList.toggle('is-before', isBefore);
            view.setAttribute('aria-hidden', String(!isActive));
            view.inert = !isActive;
        });
    };

    const setOpen = (open, initialView = 'main') => {
        menu.classList.toggle('is-open', open);
        menu.setAttribute('aria-hidden', String(!open));
        menu.inert = !open;
        document.body.classList.toggle('user-mobile-menu-open', open);
        openButtons.forEach((button) => button.setAttribute('aria-expanded', String(open)));

        if (open) {
            restoreFocus = document.activeElement;
            viewStack = initialView === 'categories' && viewByName.has(initialView)
                ? ['main', 'categories']
                : ['main'];
            syncViews();
            viewByName.get(viewStack[viewStack.length - 1])?.querySelector('button, a[href]')?.focus();
            return;
        }

        viewStack = ['main'];
        syncViews();
        restoreFocus?.focus?.();
        restoreFocus = null;
    };

    openButtons.forEach((button) => button.addEventListener('click', () => setOpen(true, button.dataset.mobileMenuOpenView)));

    menu.querySelectorAll('[data-mobile-menu-close]').forEach((button) => {
        button.addEventListener('click', () => setOpen(false));
    });

    menu.querySelectorAll('[data-mobile-menu-view-open]').forEach((button) => {
        button.addEventListener('click', () => {
            const nextView = button.dataset.mobileMenuViewOpen;
            if (!viewByName.has(nextView)) return;

            viewStack.push(nextView);
            syncViews();
            viewByName.get(nextView).querySelector('button, a[href]')?.focus();
        });
    });

    menu.querySelectorAll('[data-mobile-menu-back]').forEach((button) => {
        button.addEventListener('click', () => {
            const targetView = button.dataset.mobileMenuBack;
            const targetIndex = viewStack.lastIndexOf(targetView);
            viewStack = targetIndex >= 0 ? viewStack.slice(0, targetIndex + 1) : viewStack.slice(0, -1);
            syncViews();
            viewByName.get(viewStack[viewStack.length - 1])?.querySelector('button, a[href]')?.focus();
        });
    });

    menu.addEventListener('click', (event) => {
        if (event.target.closest('a[href]')) setOpen(false);
    });

    document.addEventListener('keydown', (event) => {
        if (!menu.classList.contains('is-open')) return;

        if (event.key === 'Escape') {
            event.preventDefault();
            if (viewStack.length > 1) {
                viewStack.pop();
                syncViews();
                viewByName.get(viewStack[viewStack.length - 1])?.querySelector('button, a[href]')?.focus();
            } else {
                setOpen(false);
            }
            return;
        }

        if (event.key !== 'Tab' || !drawer) return;
        const activeView = viewByName.get(viewStack[viewStack.length - 1]);
        const focusable = Array.from(activeView?.querySelectorAll(focusableSelector) || [])
            .filter((element) => !element.disabled && element.getAttribute('aria-hidden') !== 'true');
        if (!focusable.length) return;

        const first = focusable[0];
        const last = focusable[focusable.length - 1];
        if (event.shiftKey && (document.activeElement === first || !activeView.contains(document.activeElement))) {
            event.preventDefault();
            last.focus();
        } else if (!event.shiftKey && (document.activeElement === last || !activeView.contains(document.activeElement))) {
            event.preventDefault();
            first.focus();
        }
    });

    const desktopBreakpoint = window.matchMedia('(min-width: 1024px)');
    desktopBreakpoint.addEventListener('change', (event) => {
        if (event.matches && menu.classList.contains('is-open')) setOpen(false);
    });

    syncViews();
}

/** Typeahead for header/mobile search: debounce, min 2 chars, abort stale requests. */
function initItemSearch() {
    document.querySelectorAll('[data-item-search]').forEach((form) => {
        const input = form.querySelector('input[type="search"]');
        const endpoint = form.dataset.itemSearch;
        const panel = form.querySelector('[data-item-search-panel]')
            || form.closest('[data-mobile-search]')?.querySelector('[data-item-search-panel]');
        const hint = form.closest('[data-mobile-search]')?.querySelector('[data-item-search-hint]');
        if (!(form instanceof HTMLFormElement) || !input || !endpoint || !panel) return;

        const priceFormat = new Intl.NumberFormat('fa-IR');
        let timer = null;
        let controller = null;
        let items = [];
        let activeIndex = -1;
        const listId = `item-search-${Math.random().toString(36).slice(2, 8)}`;

        const escapeHtml = (value) => String(value)
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;');

        const setOpen = (open) => {
            panel.hidden = !open;
            input.setAttribute('aria-expanded', String(open));
            if (hint) hint.hidden = open;
            if (!open) activeIndex = -1;
        };

        const paintActive = () => {
            panel.querySelectorAll('[data-item-search-option]').forEach((option, index) => {
                option.classList.toggle('is-active', index === activeIndex);
            });
        };

        const render = (term) => {
            if (!items.length) {
                panel.innerHTML = `<p class="user-search__status">برای «${escapeHtml(term)}» چیزی پیدا نشد.</p>`;
                setOpen(true);
                return;
            }

            const options = items.map((item, index) => {
                const meta = [item.brand, item.category].filter(Boolean).join(' · ');
                const price = Number.isFinite(item.price)
                    ? `<b>${priceFormat.format(item.price)} تومان</b>`
                    : '';

                return `<a class="user-search__item" href="${escapeHtml(item.url)}" data-item-search-option data-index="${index}">
                    <img src="${escapeHtml(item.image)}" alt="" width="48" height="48">
                    <span><strong>${escapeHtml(item.name)}</strong>${meta ? `<em>${escapeHtml(meta)}</em>` : ''}</span>
                    ${price}
                </a>`;
            }).join('');

            panel.innerHTML = `<div class="user-search__list" id="${listId}" role="listbox">${options}</div>
                <button type="submit" class="user-search__more">مشاهده همه نتایج</button>`;
            setOpen(true);
            paintActive();
        };

        const fetchItems = (term) => {
            controller?.abort();
            controller = new AbortController();
            panel.innerHTML = '<p class="user-search__status">در حال جستجو...</p>';
            setOpen(true);

            const url = new URL(endpoint, window.location.origin);
            url.searchParams.set('q', term);

            fetch(url, { headers: { Accept: 'application/json' }, signal: controller.signal })
                .then((response) => {
                    if (!response.ok) throw new Error('suggest failed');
                    return response.json();
                })
                .then((payload) => {
                    items = Array.isArray(payload.items) ? payload.items : [];
                    activeIndex = -1;
                    render(term);
                })
                .catch((error) => {
                    if (error.name === 'AbortError') return;
                    panel.innerHTML = '<p class="user-search__status">جستجو الان ممکن نیست. دوباره بنویسید.</p>';
                    setOpen(true);
                });
        };

        const schedule = () => {
            const term = input.value.trim();
            window.clearTimeout(timer);
            controller?.abort();
            items = [];
            activeIndex = -1;

            if (term.length < 2) {
                setOpen(false);
                return;
            }

            timer = window.setTimeout(() => fetchItems(term), 400);
        };

        input.setAttribute('role', 'combobox');
        input.setAttribute('aria-autocomplete', 'list');
        input.setAttribute('aria-expanded', 'false');
        input.setAttribute('aria-controls', listId);
        input.addEventListener('input', schedule);
        input.addEventListener('focus', () => {
            if (!panel.hidden && items.length) setOpen(true);
        });

        input.addEventListener('keydown', (event) => {
            if (panel.hidden || !items.length) return;

            if (event.key === 'ArrowDown') {
                event.preventDefault();
                activeIndex = (activeIndex + 1) % items.length;
                paintActive();
            }

            if (event.key === 'ArrowUp') {
                event.preventDefault();
                activeIndex = activeIndex <= 0 ? items.length - 1 : activeIndex - 1;
                paintActive();
            }

            if (event.key === 'Enter' && activeIndex >= 0 && items[activeIndex]?.url) {
                event.preventDefault();
                window.location.href = items[activeIndex].url;
            }

            if (event.key === 'Escape') {
                event.preventDefault();
                setOpen(false);
            }
        });

        form.addEventListener('submit', () => setOpen(false));
        document.addEventListener('pointerdown', (event) => {
            if (form.contains(event.target) || panel.contains(event.target)) return;
            setOpen(false);
        });
    });
}

/** Full-screen mobile search, using the regular storefront search endpoint. */
function initMobileSearch() {
    const panel = document.querySelector('[data-mobile-search]');
    const triggers = Array.from(document.querySelectorAll('[data-mobile-search-open]'));
    if (!panel || !triggers.length) return;

    const input = panel.querySelector('[data-mobile-search-input]');
    const closeButton = panel.querySelector('[data-mobile-search-close]');
    let restoreFocus = null;

    const setOpen = (open) => {
        panel.classList.toggle('is-open', open);
        panel.setAttribute('aria-hidden', String(!open));
        panel.inert = !open;
        document.body.classList.toggle('user-mobile-search-open', open);
        triggers.forEach((trigger) => trigger.setAttribute('aria-expanded', String(open)));

        if (open) {
            restoreFocus = document.activeElement;
            input?.focus();
        } else {
            restoreFocus?.focus?.();
            restoreFocus = null;
        }
    };

    triggers.forEach((trigger) => trigger.addEventListener('click', () => setOpen(true)));
    closeButton?.addEventListener('click', () => setOpen(false));

    document.addEventListener('keydown', (event) => {
        if (!panel.classList.contains('is-open')) return;
        if (event.key === 'Escape') {
            event.preventDefault();
            setOpen(false);
            return;
        }
        if (event.key !== 'Tab') return;

        const focusable = Array.from(panel.querySelectorAll('button:not([disabled]), input:not([disabled])'));
        const first = focusable[0];
        const last = focusable[focusable.length - 1];
        if (!first || !last) return;

        if (event.shiftKey && document.activeElement === first) {
            event.preventDefault();
            last.focus();
        } else if (!event.shiftKey && document.activeElement === last) {
            event.preventDefault();
            first.focus();
        }
    });

    window.matchMedia('(min-width: 1024px)').addEventListener('change', (event) => {
        if (event.matches && panel.classList.contains('is-open')) setOpen(false);
    });
}

/** Match the Karin mobile quick nav behavior while scrolling. */
function initMobileBottomNav() {
    const nav = document.querySelector('.user-mobile-bottom-nav');
    if (!nav) return;

    let previousScroll = window.scrollY;
    window.addEventListener('scroll', () => {
        const currentScroll = window.scrollY;
        if (Math.abs(currentScroll - previousScroll) < 6) return;
        nav.classList.toggle('is-hidden', currentScroll > previousScroll && currentScroll > 120);
        previousScroll = currentScroll;
    }, { passive: true });
}

/** Accessible disclosure behavior for the storefront category mega menu. */
function initMegaMenu() {
    const menus = Array.from(document.querySelectorAll('[data-mega-menu]'));
    if (!menus.length) return;

    menus.forEach((menu) => {
        const triggers = Array.from(menu.querySelectorAll('[data-category-mega-trigger]'));
        const panels = Array.from(menu.querySelectorAll('[data-category-mega-panel]'));
        if (!triggers.length || !panels.length) return;

        const activatePanel = (id) => {
            triggers.forEach((trigger) => {
                const active = trigger.dataset.categoryMegaTrigger === id;
                trigger.classList.toggle('is-active', active);
                trigger.setAttribute('aria-expanded', String(active));
            });
            panels.forEach((panel) => {
                panel.hidden = panel.id !== id;
            });
        };

        triggers.forEach((trigger) => {
            const activate = () => activatePanel(trigger.dataset.categoryMegaTrigger);
            trigger.addEventListener('pointerenter', activate);
            trigger.addEventListener('focus', activate);
            trigger.addEventListener('click', activate);
        });

        const initialTrigger = triggers.find((trigger) => trigger.getAttribute('aria-expanded') === 'true') || triggers[0];
        activatePanel(initialTrigger.dataset.categoryMegaTrigger);
    });

    document.addEventListener('pointerdown', (event) => {
        menus.forEach((menu) => {
            if (menu.contains(event.target)) return;
            menu.open = false;
            if (menu.contains(document.activeElement)) menu.querySelector('summary')?.blur();
        });
    });

    document.addEventListener('keydown', (event) => {
        if (event.key !== 'Escape') return;

        menus.forEach((menu) => {
            menu.open = false;
            if (menu.contains(document.activeElement)) menu.querySelector('summary')?.blur();
        });
    });

    menus.forEach((menu) => {
        menu.addEventListener('click', (event) => {
            if (event.target.closest('a')) menu.open = false;
        });
    });
}

/** Theme toggle for the public-facing storefront. */
function initThemeToggle() {
    const toggles = Array.from(document.querySelectorAll('[data-theme-toggle]'));
    const syncPressedState = () => {
        const pressed = String(document.documentElement.classList.contains('dark'));
        toggles.forEach((toggle) => toggle.setAttribute('aria-pressed', pressed));
    };

    syncPressedState();
    toggles.forEach((toggle) => {
        toggle.addEventListener('click', () => {
            const isDark = document.documentElement.classList.toggle('dark');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
            syncPressedState();
        });
    });
}

function initPasswordVisibilityToggles() {
    document.addEventListener('click', (event) => {
        if (!(event.target instanceof Element)) return;
        const toggle = event.target.closest('[data-password-visibility-toggle]');
        if (!toggle) return;

        const input = document.getElementById(toggle.getAttribute('aria-controls'));
        if (!input) return;

        const visible = input.type === 'password';
        input.type = visible ? 'text' : 'password';
        toggle.textContent = visible ? 'پنهان' : 'نمایش';
        toggle.setAttribute('aria-label', visible ? 'پنهان کردن رمز عبور' : 'نمایش رمز عبور');
        toggle.setAttribute('aria-pressed', String(visible));
    });
}

function initOwnerLogin() {
    const form = document.querySelector('[data-owner-login]');
    if (!form) return;
    const toggle = form.querySelector('[data-password-toggle]');
    const password = form.querySelector('#password');
    const submit = form.querySelector('[data-login-submit]');
    toggle?.addEventListener('click', () => {
        const visible = password.type === 'password';
        password.type = visible ? 'text' : 'password';
        toggle.setAttribute('aria-pressed', String(visible));
        toggle.setAttribute('aria-label', visible ? 'پنهان کردن رمز عبور' : 'نمایش رمز عبور');
        toggle.textContent = visible ? 'پنهان' : 'نمایش';
    });
    const setLoading = (loading) => {
        submit.disabled = loading;
        submit.setAttribute('aria-busy', String(loading));
        form.querySelector('[data-login-label]').textContent = loading ? 'در حال ورود…' : 'ورود به پنل سالن';
        form.querySelector('[data-login-spinner]').classList.toggle('hidden', !loading);
        form.querySelector('[data-login-arrow]').classList.toggle('hidden', loading);
    };
    form.addEventListener('submit', () => setLoading(true));
    window.addEventListener('pageshow', () => setLoading(false));
}

function initBookingCancellation() {
    document.querySelectorAll('[data-cancel-dialog]').forEach((dialog) => {
        const trigger = document.querySelector(`[data-cancel-open="${dialog.id}"]`);
        const form = dialog.querySelector('[data-cancel-form]');
        const submit = form?.querySelector('[data-cancel-submit]');
        const label = form?.querySelector('[data-cancel-submit-label]');
        const spinner = form?.querySelector('[data-cancel-spinner]');
        if (!trigger || !form || !submit || !label) return;

        trigger.classList.replace('hidden', 'inline-flex');
        trigger.addEventListener('click', () => {
            if (typeof dialog.showModal !== 'function') {
                if (window.confirm('این رزرو لغو شود؟')) form.requestSubmit();
                return;
            }
            dialog.showModal();
            document.body.classList.add('overflow-hidden');
        });

        dialog.querySelectorAll('[data-cancel-close]').forEach((button) => {
            button.addEventListener('click', () => dialog.close());
        });
        dialog.addEventListener('click', (event) => {
            const bounds = dialog.getBoundingClientRect();
            if (event.target === dialog && (event.clientX < bounds.left || event.clientX > bounds.right || event.clientY < bounds.top || event.clientY > bounds.bottom)) {
                dialog.close();
            }
        });
        dialog.addEventListener('close', () => {
            document.body.classList.remove('overflow-hidden');
            trigger.focus();
        });

        form.addEventListener('submit', (event) => {
            if (submit.disabled) {
                event.preventDefault();
                return;
            }
            submit.disabled = true;
            form.setAttribute('aria-busy', 'true');
            label.textContent = 'در حال لغو رزرو…';
            spinner?.classList.remove('hidden');
        });
        window.addEventListener('pageshow', () => {
            submit.disabled = false;
            form.setAttribute('aria-busy', 'false');
            label.textContent = 'بله، رزرو لغو شود';
            spinner?.classList.add('hidden');
            if (dialog.open) dialog.close();
        });
    });
}

function initCheckoutShipping() {
    if (window.__storeCheckoutTotalsReady) return;
    window.__storeCheckoutTotalsReady = true;

    const summary = document.querySelector('[data-checkout-summary]');
    if (!summary) return;

    const radios = document.querySelectorAll('input[name="address_id"][data-shipping-amount]');
    const shippingLabels = document.querySelectorAll('[data-checkout-shipping]');
    const totalLabels = document.querySelectorAll('[data-checkout-total]');
    const subtotal = Number(summary.dataset.subtotal);
    const discount = Number(summary.dataset.discount || 0);
    const format = (amount) => `${amount.toLocaleString('en-US')} تومان`;

    const update = () => {
        const selected = [...radios].find((radio) => radio.checked);
        if (!selected) return;

        const shipping = Number(selected.dataset.shippingAmount);
        const total = format(Math.max(0, subtotal - discount) + shipping);
        const shippingText = shipping > 0 ? format(shipping) : 'رایگان';

        shippingLabels.forEach((label) => {
            label.textContent = shippingText;
        });
        totalLabels.forEach((label) => {
            label.textContent = total;
        });
    };

    radios.forEach((radio) => radio.addEventListener('change', update));
    update();
}

function initProductGallery() {
    document.querySelectorAll('[data-product-gallery]').forEach((gallery) => {
        const mainImage = gallery.querySelector('[data-gallery-main]');
        const thumbnails = gallery.querySelectorAll('[data-gallery-thumb]');

        thumbnails.forEach((thumbnail) => {
            thumbnail.addEventListener('click', () => {
                mainImage.src = thumbnail.dataset.imageSrc;
                mainImage.alt = thumbnail.dataset.imageAlt;

                thumbnails.forEach((button) => {
                    button.setAttribute('aria-pressed', button === thumbnail ? 'true' : 'false');
                });
            });
        });
    });
}

function initProductInfoTabs() {
    const roots = Array.from(document.querySelectorAll('[data-product-info-tabs]'));

    roots.forEach((root) => {
        const tabs = Array.from(root.querySelectorAll('[data-product-info-tab]'));
        const panels = Array.from(root.querySelectorAll('[data-product-info-panel]'));
        if (tabs.length < 2 || panels.length < 2) return;

        const activate = (tab, moveFocus = false) => {
            tabs.forEach((candidate) => {
                const selected = candidate === tab;
                candidate.setAttribute('aria-selected', String(selected));
                candidate.tabIndex = selected ? 0 : -1;
            });
            panels.forEach((panel) => {
                panel.hidden = panel.dataset.productInfoPanel !== tab.dataset.productInfoTab;
            });
            if (moveFocus) tab.focus();
        };

        tabs.forEach((tab) => {
            tab.addEventListener('click', () => activate(tab));
            tab.addEventListener('keydown', (event) => {
                if (!['ArrowLeft', 'ArrowRight', 'Home', 'End'].includes(event.key)) return;
                event.preventDefault();

                let nextIndex;
                if (event.key === 'Home') {
                    nextIndex = 0;
                } else if (event.key === 'End') {
                    nextIndex = tabs.length - 1;
                } else {
                    const direction = getComputedStyle(root).direction === 'rtl'
                        ? (event.key === 'ArrowLeft' ? 1 : -1)
                        : (event.key === 'ArrowRight' ? 1 : -1);
                    nextIndex = (tabs.indexOf(tab) + direction + tabs.length) % tabs.length;
                }

                activate(tabs[nextIndex], true);
            });
        });
    });

    document.addEventListener('click', (event) => {
        if (!(event.target instanceof Element)) return;
        const link = event.target.closest('[data-product-info-tab-link]');
        if (!link) return;

        const root = document.getElementById('product-specifications');
        const tab = Array.from(root?.querySelectorAll('[data-product-info-tab]') ?? [])
            .find((candidate) => candidate.dataset.productInfoTab === link.dataset.productInfoTabLink);
        if (!root || !tab) return;

        tab.click();
    });
}

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-hero-slider]').forEach((el) => new HeroSlider(el));
    updateAmazingCountdowns();
    window.setInterval(updateAmazingCountdowns, 1000);
    document.querySelectorAll('[data-carousel]').forEach((el) => new Carousel(el.dataset.carousel));
    document.querySelectorAll('[data-booking-widget]').forEach((el) => new BookingWidget(el));
    initMobileMenu();
    initMobileSearch();
    initItemSearch();
    initMobileBottomNav();
    initMegaMenu();
    initThemeToggle();
    initPasswordVisibilityToggles();
    initOwnerLogin();
    initBookingCancellation();
    initProductGallery();
    initCheckoutShipping();
    initProductInfoTabs();
    initStoreCartQty();
});

function initStoreCartQty() {
    if (window.__storeCartQtyReady) return;
    window.__storeCartQtyReady = true;

    const form = document.querySelector('[data-cart-form]');
    if (!form) return;

    const submitQuantities = () => {
        const updater = form.querySelector('[data-cart-update]');
        if (updater instanceof HTMLButtonElement) {
            updater.click();
            return;
        }

        form.submit();
    };

    form.querySelectorAll('[data-cart-qty]').forEach((button) => {
        button.addEventListener('click', () => {
            const field = button.closest('.store-cart-qty')?.querySelector('input[type="number"]');
            if (!field) return;

            const step = Number(button.getAttribute('data-cart-qty') || '0');
            const min = Number(field.min || '1');
            const max = Number(field.max || '999');
            const next = Math.min(max, Math.max(min, Number(field.value || min) + step));

            if (next === Number(field.value)) return;
            field.value = String(next);
            submitQuantities();
        });
    });

    form.querySelectorAll('.store-cart-qty input[type="number"]').forEach((field) => {
        field.addEventListener('change', submitQuantities);
    });
}
