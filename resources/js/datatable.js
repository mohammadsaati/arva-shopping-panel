/**
 * DataTable — dynamic AJAX table component.
 *
 * Column config shape:
 * {
 *   key:         string,          // JSON key (dot-notation supported: "user.name")
 *   label:       string,          // header text
 *   sortable:    boolean,         // click-to-sort header
 *   html:        boolean,         // render cell value as raw HTML (for pre-rendered action buttons)
 *   class:       string,          // static <td> class
 *   headerClass: string,          // <th> class
 *   format:      string|null,     // 'date' | 'datetime' | 'number' | 'currency' | 'boolean'
 *   prefix:      string|null,     // prepend to displayed value
 *   suffix:      string|null,     // append to displayed value
 *   empty:       string,          // shown when value is null / ""  (default: '-')
 *   badge:       object|null,     // { value: 'badge-class', ... } — wraps cell in <span class="badge ...">
 *   conditions:  array,           // conditional TD classes — evaluated in order, first match wins
 *     [{ field, op, value, class }]
 *     ops: eq | neq | gt | gte | lt | lte | contains | starts | ends | truthy | falsy
 * }
 *
 * Expected API response (either format works):
 * { data: [...], total, per_page, current_page, last_page, from, to }
 * { data: [...], meta: { total, per_page, current_page, last_page, from, to } }
 */

class DataTable {
    constructor(el) {
        this.el          = el;
        this.route       = el.dataset.route;
        this.columns     = JSON.parse(el.dataset.columns || '[]');
        this.perPage     = parseInt(el.dataset.perPage || 15, 10);
        this.page        = 1;
        this.sortBy      = null;
        this.sortDir     = 'asc';
        this.searchQuery = '';
        this.busy        = false;

        this.$tbody   = el.querySelector('[data-tbody]');
        this.$search  = el.querySelector('[data-search]');
        this.$perPage = el.querySelector('[data-per-page]');
        this.$pager   = el.querySelector('[data-pagination]');
        this.$info    = el.querySelector('[data-info]');
        this.$loader  = el.querySelector('[data-loading]');

        this._bindEvents();
        this.reload();
    }

    // ─── Events ────────────────────────────────────────────────────────────

    _bindEvents() {
        // Search (debounced)
        if (this.$search) {
            let timer;
            this.$search.addEventListener('input', (e) => {
                clearTimeout(timer);
                timer = setTimeout(() => {
                    this.searchQuery = e.target.value.trim();
                    this.page = 1;
                    this.reload();
                }, 350);
            });
        }

        // Per-page selector
        if (this.$perPage) {
            this.$perPage.addEventListener('change', (e) => {
                this.perPage = parseInt(e.target.value, 10);
                this.page = 1;
                this.reload();
            });
        }

        // Sort headers (delegated)
        this.el.addEventListener('click', (e) => {
            const th = e.target.closest('[data-sort]');
            if (!th) return;
            const key = th.dataset.sort;
            this.sortDir = this.sortBy === key && this.sortDir === 'asc' ? 'desc' : 'asc';
            this.sortBy  = key;
            this._updateSortIcons();
            this.page = 1;
            this.reload();
        });
    }

    // ─── Fetch ─────────────────────────────────────────────────────────────

    reload() {
        if (this.busy) return;
        this.busy = true;
        this._setLoading(true);

        const params = new URLSearchParams({ page: this.page, per_page: this.perPage });
        if (this.searchQuery)  params.set('search',   this.searchQuery);
        if (this.sortBy)       params.set('sort_by',  this.sortBy);
        if (this.sortBy)       params.set('sort_dir', this.sortDir);

        fetch(`${this.route}?${params}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept':           'application/json',
                'X-CSRF-TOKEN':     document.querySelector('meta[name="csrf-token"]')?.content ?? '',
            },
        })
            .then((res) => {
                if (!res.ok) throw new Error(`Server error ${res.status}`);
                return res.json();
            })
            .then((json) => this._render(json))
            .catch((err) => this._renderError(err.message))
            .finally(() => {
                this.busy = false;
                this._setLoading(false);
            });
    }

    // ─── Render ────────────────────────────────────────────────────────────

    _render(json) {
        const rows = Array.isArray(json.data) ? json.data : [];
        // Support both flat Laravel pagination and {data, meta} wrapper
        const meta = json.meta ?? json;

        this.$tbody.innerHTML = rows.length
            ? rows.map((row) => this._renderRow(row)).join('')
            : this._emptyRow();

        this._renderPager(meta);
        this._renderInfo(meta);
    }

    _renderRow(row) {
        const cells = this.columns.map((col) => this._renderCell(row, col)).join('');
        return `<tr class="border-b border-gray-100 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">${cells}</tr>`;
    }

    _renderCell(row, col) {
        const raw     = this._dig(row, col.key);
        const tdClass = this._cellClass(row, col);

        let content;

        if (col.html) {
            // Pre-rendered HTML (action buttons, custom markup from server)
            content = raw ?? '';
        } else {
            const display = this._format(raw, col);

            if (col.badge && raw !== null && raw !== undefined) {
                const badgeClass = col.badge[String(raw)] ?? col.badge['_default'] ?? '';
                content = `<span class="badge ${badgeClass}">${this._esc(display)}</span>`;
            } else {
                content = this._esc(display);
            }
        }

        return `<td class="${tdClass}">${content}</td>`;
    }

    _emptyRow() {
        return `<tr>
            <td colspan="${this.columns.length}" class="px-5 py-12 text-center text-gray-400">
                <div class="flex flex-col items-center gap-2">
                    <i class="ki-filled ki-data text-4xl opacity-40"></i>
                    <p class="text-sm">No records found</p>
                </div>
            </td>
        </tr>`;
    }

    _renderError(msg) {
        this.$tbody.innerHTML = `<tr>
            <td colspan="${this.columns.length}" class="px-5 py-8 text-center text-danger text-sm">
                <i class="ki-filled ki-warning-2 me-1"></i>${this._esc(msg)}
            </td>
        </tr>`;
    }

    // ─── Pagination ────────────────────────────────────────────────────────

    _renderPager(meta) {
        if (!this.$pager) return;

        const last    = parseInt(meta.last_page ?? 1, 10);
        const current = parseInt(meta.current_page ?? 1, 10);
        this.page     = current;

        if (last <= 1) { this.$pager.innerHTML = ''; return; }

        const pages = this._pageRange(current, last);

        const btn = (page, icon, disabled) =>
            `<button data-page="${page}"
                class="btn btn-sm btn-icon btn-light ${disabled ? 'opacity-40 cursor-not-allowed' : ''}"
                ${disabled ? 'disabled' : ''}>
                <i class="${icon} text-xs"></i>
            </button>`;

        const pageBtn = (p) =>
            p === '…'
                ? `<span class="px-1 text-gray-400">…</span>`
                : `<button data-page="${p}"
                    class="btn btn-sm btn-icon ${p === current ? 'btn-primary' : 'btn-light'}">
                    ${p}
                   </button>`;

        this.$pager.innerHTML =
            btn(current - 1, 'ki-filled ki-arrow-left',  current === 1) +
            pages.map(pageBtn).join('') +
            btn(current + 1, 'ki-filled ki-arrow-right', current === last);

        // Bind clicks
        this.$pager.querySelectorAll('[data-page]').forEach((b) => {
            b.addEventListener('click', () => {
                const p = parseInt(b.dataset.page, 10);
                if (p < 1 || p > last || p === this.page) return;
                this.page = p;
                this.reload();
            });
        });
    }

    _pageRange(cur, last) {
        if (last <= 7) return Array.from({ length: last }, (_, i) => i + 1);

        if (cur <= 4)        return [1, 2, 3, 4, 5, '…', last];
        if (cur >= last - 3) return [1, '…', last-4, last-3, last-2, last-1, last];
        return [1, '…', cur-1, cur, cur+1, '…', last];
    }

    _renderInfo(meta) {
        if (!this.$info) return;
        const { from = 0, to = 0, total = 0 } = meta;
        this.$info.textContent = total > 0
            ? `Showing ${from}–${to} of ${total.toLocaleString()} results`
            : '';
    }

    // ─── Helpers ───────────────────────────────────────────────────────────

    /** Build final TD class string (static + conditional). */
    _cellClass(row, col) {
        const classes = ['px-5 py-3 text-sm text-gray-700'];
        if (col.class) classes.push(col.class);

        for (const cond of (col.conditions ?? [])) {
            if (this._evalCond(row, cond)) {
                classes.push(cond.class);
                break; // first match wins
            }
        }

        return classes.join(' ');
    }

    /** Evaluate one condition object against a row. */
    _evalCond(row, cond) {
        const actual = String(this._dig(row, cond.field ?? cond.key) ?? '');
        const expect = String(cond.value ?? '');

        switch (cond.op ?? 'eq') {
            case 'eq':       return actual === expect;
            case 'neq':      return actual !== expect;
            case 'gt':       return parseFloat(actual) >  parseFloat(expect);
            case 'gte':      return parseFloat(actual) >= parseFloat(expect);
            case 'lt':       return parseFloat(actual) <  parseFloat(expect);
            case 'lte':      return parseFloat(actual) <= parseFloat(expect);
            case 'contains': return actual.includes(expect);
            case 'starts':   return actual.startsWith(expect);
            case 'ends':     return actual.endsWith(expect);
            case 'truthy':   return !!this._dig(row, cond.field ?? cond.key);
            case 'falsy':    return !this._dig(row, cond.field ?? cond.key);
            default:         return false;
        }
    }

    /** Format a raw value according to col.format. */
    _format(value, col) {
        if (value === null || value === undefined || value === '') {
            return col.empty ?? '-';
        }

        let out = value;

        switch (col.format) {
            case 'date':
                out = new Date(value).toLocaleDateString();
                break;
            case 'datetime':
                out = new Date(value).toLocaleString();
                break;
            case 'number':
                out = Number(value).toLocaleString();
                break;
            case 'currency':
                out = Number(value).toLocaleString(undefined, {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2,
                });
                break;
            case 'boolean':
                out = value ? 'Yes' : 'No';
                break;
        }

        if (col.prefix) out = col.prefix + out;
        if (col.suffix) out = out + col.suffix;

        return String(out);
    }

    /** Read a (possibly dot-notation) key from an object. */
    _dig(obj, key) {
        return String(key).split('.').reduce((acc, k) => acc?.[k], obj);
    }

    /** HTML-escape a string. */
    _esc(str) {
        const d = document.createElement('div');
        d.textContent = String(str ?? '');
        return d.innerHTML;
    }

    _setLoading(on) {
        this.$loader?.classList.toggle('hidden', !on);
    }

    _updateSortIcons() {
        this.el.querySelectorAll('[data-sort]').forEach((th) => {
            const icon = th.querySelector('[data-sort-icon]');
            if (!icon) return;
            if (th.dataset.sort === this.sortBy) {
                icon.className = `ki-filled ki-arrow-${this.sortDir === 'asc' ? 'up' : 'down'} text-xs text-primary`;
            } else {
                icon.className = 'ki-filled ki-arrows-circle text-xs text-gray-300';
            }
        });
    }
}

// Auto-initialize every [data-datatable] on the page
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-datatable]').forEach((el) => new DataTable(el));
});

export default DataTable;
