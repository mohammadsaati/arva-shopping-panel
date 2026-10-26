import { Editor } from '@tiptap/core';
import StarterKit from '@tiptap/starter-kit';
import { Markdown } from '@tiptap/markdown';

const activeActions = {
    paragraph: (editor) => editor.isActive('paragraph'),
    heading2: (editor) => editor.isActive('heading', { level: 2 }),
    heading3: (editor) => editor.isActive('heading', { level: 3 }),
    bold: (editor) => editor.isActive('bold'),
    italic: (editor) => editor.isActive('italic'),
    link: (editor) => editor.isActive('link'),
    bulletList: (editor) => editor.isActive('bulletList'),
    orderedList: (editor) => editor.isActive('orderedList'),
    blockquote: (editor) => editor.isActive('blockquote'),
};

function initializeEditor(root) {
    const textarea = root.querySelector('[data-blog-editor-input]');
    const canvas = root.querySelector('[data-blog-editor-canvas]');
    const toolbar = root.querySelector('[data-blog-editor-toolbar]');
    const label = root.querySelector('[data-blog-editor-label]');
    const wordCount = root.querySelector('[data-blog-editor-count]');
    const linkPanel = root.querySelector('[data-blog-editor-link-panel]');
    const linkInput = root.querySelector('[data-blog-editor-link-input]');
    const linkError = root.querySelector('[data-blog-editor-link-error]');

    if (!textarea || !canvas || !toolbar) return;

    let editor;

    try {
        editor = new Editor({
            element: canvas,
            extensions: [
                StarterKit.configure({
                    heading: { levels: [2, 3] },
                    link: { openOnClick: false, autolink: false, defaultProtocol: 'https' },
                }),
                Markdown,
            ],
            content: textarea.value || '',
            contentType: 'markdown',
            editorProps: {
                attributes: {
                    id: `${textarea.id}_visual`,
                    dir: 'rtl',
                    'aria-label': label?.textContent.trim() || 'متن نوشته',
                    'aria-invalid': textarea.getAttribute('aria-invalid') || 'false',
                    'aria-describedby': textarea.getAttribute('aria-describedby') || '',
                },
            },
        });
    } catch (error) {
        console.error('Blog editor could not start; the text field remains available.', error);
        return;
    }

    const buttons = [...toolbar.querySelectorAll('[data-editor-action]')];
    const closeLinkPanel = () => {
        linkPanel.classList.add('hidden');
        linkError.classList.add('hidden');
        linkInput.setAttribute('aria-invalid', 'false');
    };

    const refreshToolbar = () => {
        buttons.forEach((button) => {
            const action = button.dataset.editorAction;
            const active = activeActions[action]?.(editor) ?? false;
            button.setAttribute('aria-pressed', String(active));
            if (action === 'undo' || action === 'redo') {
                const canRun = editor.can()[action];
                button.disabled = typeof canRun === 'function' ? !canRun() : false;
            }
        });
    };

    const refreshState = () => {
        const text = editor.getText().trim();
        const total = text === '' ? 0 : text.split(/\s+/u).length;
        wordCount.textContent = `${new Intl.NumberFormat('fa-IR').format(total)} واژه`;
        refreshToolbar();
    };

    const commands = {
        paragraph: () => editor.chain().focus().setParagraph().run(),
        heading2: () => editor.chain().focus().toggleHeading({ level: 2 }).run(),
        heading3: () => editor.chain().focus().toggleHeading({ level: 3 }).run(),
        bold: () => editor.chain().focus().toggleBold().run(),
        italic: () => editor.chain().focus().toggleItalic().run(),
        bulletList: () => editor.chain().focus().toggleBulletList().run(),
        orderedList: () => editor.chain().focus().toggleOrderedList().run(),
        blockquote: () => editor.chain().focus().toggleBlockquote().run(),
        horizontalRule: () => editor.chain().focus().setHorizontalRule().run(),
        undo: () => editor.chain().focus().undo().run(),
        redo: () => editor.chain().focus().redo().run(),
    };

    buttons.forEach((button) => {
        button.addEventListener('click', () => {
            const action = button.dataset.editorAction;

            if (action === 'link') {
                linkInput.value = editor.getAttributes('link').href || '';
                linkPanel.classList.toggle('hidden');
                linkError.classList.add('hidden');
                if (!linkPanel.classList.contains('hidden')) linkInput.focus();
                return;
            }

            closeLinkPanel();
            commands[action]?.();
            refreshToolbar();
        });
    });

    root.querySelector('[data-blog-editor-link-apply]')?.addEventListener('click', () => {
        const href = linkInput.value.trim();
        const valid = /^(https:\/\/[^\s]+|\/(?!\/)[^\s]*|#[\p{L}\p{N}_-]+)$/iu.test(href);

        if (!valid || (editor.state.selection.empty && !editor.isActive('link'))) {
            linkError.textContent = valid ? 'ابتدا متنی را در ویرایشگر انتخاب کنید.' : 'نشانی باید با https://، / یا # شروع شود.';
            linkError.classList.remove('hidden');
            linkInput.setAttribute('aria-invalid', String(!valid));
            return;
        }

        editor.chain().focus().extendMarkRange('link').setLink({ href }).run();
        closeLinkPanel();
        refreshToolbar();
    });

    root.querySelector('[data-blog-editor-link-remove]')?.addEventListener('click', () => {
        editor.chain().focus().extendMarkRange('link').unsetLink().run();
        closeLinkPanel();
        refreshToolbar();
    });

    root.querySelector('[data-blog-editor-link-close]')?.addEventListener('click', closeLinkPanel);
    linkInput?.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') closeLinkPanel();
        if (event.key === 'Enter') {
            event.preventDefault();
            root.querySelector('[data-blog-editor-link-apply]')?.click();
        }
    });

    textarea.form?.addEventListener('submit', () => {
        textarea.value = editor.getMarkdown();
    });

    editor.on('update', () => {
        textarea.value = editor.getMarkdown();
        refreshState();
    });
    editor.on('selectionUpdate', refreshToolbar);

    label?.setAttribute('for', `${textarea.id}_visual`);
    textarea.classList.add('hidden');
    canvas.classList.remove('hidden');
    toolbar.classList.remove('hidden');
    wordCount.classList.remove('hidden');
    refreshState();
}

function initializeSlugSuggestion(form) {
    if (form.dataset.blogPostEditing === '1') return;

    const title = form.querySelector('[data-blog-title]');
    const slug = form.querySelector('[data-blog-slug]');
    if (!title || !slug) return;

    let lastSuggestion = '';
    title.addEventListener('input', () => {
        if (slug.value !== lastSuggestion) return;

        lastSuggestion = title.value
            .normalize('NFKC')
            .toLocaleLowerCase()
            .replace(/[^\p{L}\p{N}\s_-]/gu, '')
            .trim()
            .replace(/[\s_]+/gu, '-')
            .replace(/-+/g, '-');

        slug.value = lastSuggestion;
    });
}

function initializeBlogPostForm() {
    document.querySelectorAll('.blog-post-form').forEach((form) => {
        form.querySelectorAll('[data-blog-editor]').forEach(initializeEditor);
        initializeSlugSuggestion(form);
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeBlogPostForm, { once: true });
} else {
    initializeBlogPostForm();
}
