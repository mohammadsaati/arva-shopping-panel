document.addEventListener('DOMContentLoaded', function ()
{
    const messageTextarea = document.getElementById('message');
    if (!messageTextarea)
        return;

    const LAGV_TEXT = 'لغو11';
    const LAGV_WITH_NEWLINE = '\n' + LAGV_TEXT;
    const LAGV_CHAR_COUNT = 6; // \n + لغو11

    let selectedHeadNumberId = null;

    // GSM 7-bit character set
    const GSM7_CHARS = new Set(
        '@£$¥èéùìòÇ\nØø\rÅåΔ_ΦΓΛΩΠΨΣΘΞ ÆæßÉ' +
        ' !"#¤%&\'()*+,-./0123456789:;<=>?' +
        '¡ABCDEFGHIJKLMNOPQRSTUVWXYZ' +
        'ÄÖÑÜ§¿abcdefghijklmnopqrstuvwxyz' +
        'äöñüà'
    );

    // GSM7 extended chars (each counts as 2)
    const GSM7_EXTENDED = new Set(['{', '}', '[', ']', '~', '\\', '^', '|', '€']);

    // --- Wrap textarea ---
    const wrapper = document.createElement('div');

    wrapper.className = 'relative w-full';
    messageTextarea.parentNode.insertBefore(wrapper, messageTextarea);
    wrapper.appendChild(messageTextarea);
    messageTextarea.style.setProperty('padding-bottom', '2.5rem', 'important');
    messageTextarea.style.scrollPaddingBottom = '2.5rem';

    // --- لغو11 overlay ---
    const lagvOverlay = document.createElement('div');

    lagvOverlay.className = 'pointer-events-none text-gray-400 text-sm';
    Object.assign(lagvOverlay.style, {
        position: 'absolute',
        bottom: '8px',
        right: '12px',
        left: '12px',
        textAlign: 'right',
        direction: 'rtl',
    });
    lagvOverlay.textContent = LAGV_TEXT;
    wrapper.appendChild(lagvOverlay);

    // --- Info bar ---
    const infoBar = document.createElement('div');
    infoBar.className = 'flex flex-wrap gap-3 items-center mt-2 text-xs text-gray-500';
    infoBar.innerHTML =
        `
                <span class="inline-flex items-center gap-1">✏️ طول پیام : <b class="text-gray-800" id="sms-char-count">0</b> کاراکتر</span>
                <span class="inline-flex items-center gap-1">|</span>
                <span class="inline-flex items-center gap-1">📄 <b class="text-gray-800" id="sms-page-count">0</b> پیامک</span>
                <span class="inline-flex items-center gap-1">|</span>
                <span class="inline-flex items-center gap-1">⏳ <b class="text-gray-800" id="sms-remaining-chars">0</b> کاراکتر باقی مانده تا پیام بعدی</span>
                <span class="inline-flex items-center gap-1 hidden" id="sms-lagv-note">|</span>
                <span class="inline-flex items-center gap-1 text-red-500 font-semibold hidden" id="sms-lagv-note-text"></span>
                <span class="inline-flex items-center gap-1 text-red-500 font-semibold hidden" id="sms-error-msg"></span>
        `;
    wrapper.parentNode.insertBefore(infoBar, wrapper.nextSibling);

    const charCountEl = document.getElementById('sms-char-count');
    const pageCountEl = document.getElementById('sms-page-count');
    const remainingEl = document.getElementById('sms-remaining-chars');
    const lagvNote = document.getElementById('sms-lagv-note');
    const lagvNoteText = document.getElementById('sms-lagv-note-text');
    const errorMsg = document.getElementById('sms-error-msg');

    function isGSM7(text)
    {
        for (const ch of text)
        {
            if (!GSM7_CHARS.has(ch) && !GSM7_EXTENDED.has(ch))
            {
                return false;
            }
        }

        return true;
    }

    function getGSM7Length(text)
    {
        let len = 0;
        for (const ch of text)
        {
            if (GSM7_EXTENDED.has(ch))
            {
                len += 2;
            }
            else
            {
                len += 1;
            }
        }

        return len;
    }

    // Matches PHP MessageVolume rules exactly: ceil(length / volume)
    const SMS_RULES =
    {
        persian:
        {
            rules: [{ max: 70, vol: 70 }, { max: 128, vol: 64 }, { max: 134, vol: 67 }],
            defaultVol: 67
        },
        persian5000:
        {
            rules: [{ max: 70, vol: 70 }, { max: 124, vol: 62 }, { max: 132, vol: 66 }],
            defaultVol: 66
        },
        latin:
        {
            rules: [{ max: 160, vol: 160 }, { max: 292, vol: 146 }, { max: 306, vol: 153 }],
            defaultVol: 153
        }
    };

    function calculateSmsPages(text, is5000)
    {
        const isLatin = isGSM7(text);
        const totalChars = isLatin ? getGSM7Length(text) : text.length;

        if (totalChars === 0)
        {
            return { pages: 0, remaining: 0, isLatin, totalChars };
        }

        const ruleSet = isLatin ? SMS_RULES.latin : (is5000 ? SMS_RULES.persian5000 : SMS_RULES.persian);

        let vol = ruleSet.defaultVol;
        for (const rule of ruleSet.rules)
        {
            if (totalChars <= rule.max)
            {
                vol = rule.vol;
                break;
            }
        }

        const pages = Math.ceil(totalChars / vol);
        const remaining = (pages * vol) - totalChars;

        return { pages, remaining, isLatin, totalChars };
    }

    function stripLagv(text)
    {
        return text.replace(/\n?لغو\s*[۰-۹0-9]+\s*$/u, '').replace(/لغو\s*[۰-۹0-9]+\s*$/u, '');
    }

    function updateInfo()
    {
        const userText = messageTextarea.value;
        const cleanUserText = stripLagv(userText).replace(/^[\r\n]+/, '');
        const fullMessage = cleanUserText.length > 0 ? cleanUserText + LAGV_WITH_NEWLINE : '';

        const is5000 = selectedHeadNumberId !== null && HEAD_NUMBER_5000_IDS.includes(String(selectedHeadNumberId));

        const result = calculateSmsPages(fullMessage, is5000);

        charCountEl.textContent = result.totalChars;
        pageCountEl.textContent = result.pages;
        remainingEl.textContent = result.remaining;

        // لغو11 note
        if (cleanUserText.trim().length > 0)
        {
            lagvNote.classList.remove('hidden');
            lagvNoteText.classList.remove('hidden');
            lagvNoteText.textContent = '⚠️ ' + LAGV_CHAR_COUNT + ' کاراکتر متن اجباری لغو11 از کاراکترهای پیام کم شده است';
        }
        else
        {
            lagvNote.classList.add('hidden');
            lagvNoteText.classList.add('hidden');
        }

        // Error: 8+ pages
        if (result.pages >= 8)
        {
            errorMsg.classList.remove('hidden');
            errorMsg.textContent = '🚫 خطا: پیامک‌های ۸ صفحه و بیشتر به احتمال زیاد خطای مخابراتی دارند. لطفاً متن را به ۷ پیامک کاهش دهید.';
        }
        else
        {
            errorMsg.classList.add('hidden');
        }
    }

    // Prevent user from removing لغو11 overlay; the overlay is purely visual.
    // The actual لغو11 is appended server-side AND in JS before send.
    messageTextarea.addEventListener('input', updateInfo);
    messageTextarea.addEventListener('keyup', updateInfo);

    // Before form submits, ensure لغو11 is in the message value
    const form = messageTextarea.closest('form');
    if (form)
    {
        form.addEventListener('submit', function ()
        {
            const cleanText = stripLagv(messageTextarea.value).replace(/^[\r\n]+/, '');
            if (cleanText.length === 0)
            {
                // Will be caught by server validation
                return;
            }

            messageTextarea.value = cleanText + LAGV_WITH_NEWLINE;
        });
    }

    // Also intercept the send-sms-modal-btn to validate before price calc
    const sendSmsButtonElement = document.getElementById('send-sms-modal-btn');
    if (sendSmsButtonElement)
    {
        sendSmsButtonElement.addEventListener('click', function ()
        {
            const cleanText = stripLagv(messageTextarea.value).replace(/^[\r\n]+/, '');
            if (cleanText.length > 0)
            {
                messageTextarea.value = cleanText + LAGV_WITH_NEWLINE;
            }
        }, true); // capture phase, runs before the existing handler
    }

    // Listen for head_number_id selection from Livewire Select2
    Livewire.on('selected:head_number_id', (data) =>
    {
        const payload = data[0] ?? data;
        const values = payload.value ?? [];

        selectedHeadNumberId = values.length > 0 ? values[0] : null;
        updateInfo();
    });

    // Initial calculation
    updateInfo();
});
