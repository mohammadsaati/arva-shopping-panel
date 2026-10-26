<x-panel-layout title="اعلان‌های تلگرام" sub-title="اتصال حساب تلگرام شما به پنل مدیریت و بررسی پیام‌های ربات.">
    @if (session('success'))
        <x-alert type="success">{{ session('success') }}</x-alert>
    @endif
    @if (session('error'))
        <x-alert type="danger">{{ session('error') }}</x-alert>
    @endif
    @if (session('telegram_webhook_result'))
        @php($webhookResult = session('telegram_webhook_result'))
        <x-alert :type="$webhookResult['success'] ? 'success' : 'danger'" :title="$webhookResult['success'] ? 'وب‌هوک ثبت شد' : 'ثبت وب‌هوک انجام نشد'">
            <span dir="ltr" class="inline-block text-left">{{ $webhookResult['message'] }}</span>
            @if ($webhookResult['errorCode'])
                <span class="ms-2">(کد خطا: {{ $webhookResult['errorCode'] }})</span>
            @endif
        </x-alert>
    @endif
    @if (! $connection['configured'])
        <x-alert type="warning">تنظیمات ربات روی سرور کامل نیست. مقدارهای لازم را طبق راهنمای پایین صفحه ثبت کنید.</x-alert>
    @endif

    @if (session('telegram_link'))
        <x-card title="لینک اتصال شما" subtitle="این لینک یک‌بارمصرف است و تا ۱۰ دقیقه اعتبار دارد.">
            <div class="grid gap-4">
                <p class="text-sm leading-7 text-gray-600 dark:text-gray-300">لینک را با حساب تلگرام خود باز کنید و دکمهٔ Start ربات را بزنید. سپس این صفحه را تازه کنید تا وضعیت اتصال نمایش داده شود.</p>
                <div class="flex flex-wrap items-center gap-3">
                    <x-button href="{{ session('telegram_link') }}" target="_blank" rel="noopener noreferrer" icon="ki-filled ki-sms">باز کردن ربات در تلگرام</x-button>
                    <span class="break-all text-xs text-gray-500" dir="ltr">{{ session('telegram_link') }}</span>
                </div>
            </div>
        </x-card>
    @endif

    <x-card.form
        action="{{ route('admin.telegram.webhook.register') }}"
        method="POST"
        title="ثبت وب‌هوک ربات"
        :submit="['title' => 'ثبت وب‌هوک در تلگرام', 'type' => 'primary', 'icon' => 'ki-filled ki-check']"
    >
        <p class="text-sm leading-7 text-gray-600 dark:text-gray-300">بعد از تنظیم ربات و آدرس عمومی HTTPS سایت، با این دکمه وب‌هوک در تلگرام ثبت می‌شود. برای تست محلی می‌توانید آدرس یک تونل HTTPS را در <code dir="ltr" class="font-mono">TELEGRAM_WEBHOOK_BASE_URL</code> قرار دهید. نتیجه یا خطای برگشتی تلگرام بالای صفحه نمایش داده خواهد شد.</p>
        @if ($connection['webhookUrl'])
            <div class="grid gap-2">
                <span class="text-xs font-semibold text-gray-500 dark:text-gray-400">آدرسی که ثبت می‌شود</span>
                <code dir="ltr" class="block break-all rounded-xl bg-gray-100 p-3 text-left text-sm dark:bg-gray-800">{{ $connection['webhookUrl'] }}</code>
            </div>
        @else
            <p class="text-sm text-warning">برای ثبت خودکار، مقدار APP_URL یا TELEGRAM_WEBHOOK_BASE_URL را روی دامنهٔ عمومی HTTPS سایت قرار دهید.</p>
        @endif
        <p class="text-xs leading-6 text-gray-500 dark:text-gray-400">تلگرام برای وب‌هوک فقط درگاه‌های ۸۰، ۸۸، ۴۴۳ و ۸۴۴۳ را می‌پذیرد. ثبت موفق به معنی دریافت قطعی پیام‌ها نیست؛ دریافت پیام تأیید پس از Start، مسیر ورودی را بررسی می‌کند.</p>
    </x-card.form>

    <div class="grid gap-5 lg:grid-cols-3">
        <div class="lg:col-span-2">
            <x-card.form
                action="{{ route('admin.telegram.link') }}"
                method="POST"
                class="admin-editor-form"
                :submit="['title' => $connection['connected'] ? 'ساخت لینک اتصال مجدد' : 'ساخت لینک اتصال', 'type' => 'primary', 'icon' => 'ki-filled ki-sms', 'disabled' => ! $connection['configured']]"
            >
                <x-admin.form-intro title="اتصال حساب تلگرام" description="با لینک اختصاصی، ربات را به حساب همین مدیر وصل کنید." icon="ki-filled ki-message-text-2" />
                <x-admin.form-section title="مراحل اتصال" description="شناسهٔ تلگرام را لازم نیست دستی پیدا یا وارد کنید." icon="ki-filled ki-information-2">
                    <ol class="list-decimal space-y-3 ps-5 text-sm leading-7 text-gray-600 dark:text-gray-300">
                        <li>روی «ساخت لینک اتصال» بزنید.</li>
                        <li>لینک یک‌بارمصرف را در حساب تلگرام خود باز کنید و Start ربات را بزنید.</li>
                        <li>پس از دریافت پیام تأیید ربات، این صفحه را تازه کنید.</li>
                    </ol>
                </x-admin.form-section>
            </x-card.form>
        </div>

        <aside class="grid content-start gap-5" aria-label="وضعیت اتصال تلگرام">
            <x-card title="وضعیت اتصال">
                <div class="grid gap-4 text-sm">
                    @if ($connection['connected'])
                        <x-badge variant="success" size="sm" dot class="w-fit">متصل</x-badge>
                        <p class="leading-7 text-gray-600 dark:text-gray-300">این حساب آمادهٔ دریافت پیام‌های ربات است.</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">زمان اتصال: {{ $connection['connectedAt'] ? jalali_date($connection['connectedAt'], 'Y/m/d H:i') : '—' }}</p>
                    @else
                        <x-badge variant="warning" size="sm" dot class="w-fit">متصل نشده</x-badge>
                        <p class="leading-7 text-gray-600 dark:text-gray-300">پس از اتصال ربات، پیام‌ها به گفت‌وگوی خصوصی شما ارسال می‌شوند.</p>
                    @endif
                    @if ($connection['botUsername'])
                        <p class="text-xs text-gray-500 dark:text-gray-400">ربات: <span dir="ltr" class="inline-block">&#64;{{ $connection['botUsername'] }}</span></p>
                    @endif
                </div>
            </x-card>

            @if ($connection['connected'])
                <x-card.form
                    action="{{ route('admin.telegram.test') }}"
                    method="POST"
                    title="پیام آزمایشی"
                    :submit="['title' => 'ارسال پیام آزمایشی', 'type' => 'light', 'icon' => 'ki-filled ki-sms', 'disabled' => ! $connection['configured']]"
                >
                    <p class="text-sm leading-7 text-gray-600 dark:text-gray-300">برای اطمینان از فعال‌بودن ربات، یک پیام کوتاه به تلگرام خود بفرستید.</p>
                </x-card.form>

                <x-card.form
                    action="{{ route('admin.telegram.disconnect') }}"
                    method="DELETE"
                    title="قطع اتصال"
                    :submit="['title' => 'قطع اتصال تلگرام', 'type' => 'light', 'icon' => 'ki-filled ki-cross']"
                    data-confirm-open="telegram-disconnect-confirm"
                    data-confirm-title="قطع اتصال تلگرام"
                    data-confirm-message="اتصال این حساب به ربات تلگرام قطع شود؟"
                    data-confirm-text="قطع اتصال"
                >
                    <p class="text-sm leading-7 text-gray-600 dark:text-gray-300">بعد از قطع اتصال، پیام‌های ربات به این حساب ارسال نمی‌شوند. هر زمان می‌توانید دوباره متصل شوید.</p>
                </x-card.form>
                <x-confirm-dialog id="telegram-disconnect-confirm" variant="danger" confirm-text="قطع اتصال" />
            @endif
        </aside>
    </div>

    <x-card title="راهنمای راه‌اندازی ربات" subtitle="این تنظیمات یک‌بار توسط مدیر سرور انجام می‌شوند؛ سپس هر مدیر می‌تواند حساب تلگرام خودش را متصل کند.">
        <div class="grid gap-6 lg:grid-cols-2">
            <section class="grid content-start gap-4" aria-labelledby="telegram-setup-server">
                <div class="flex flex-wrap items-center gap-3">
                    <h3 id="telegram-setup-server" class="text-base font-semibold">۱. تنظیمات سرور</h3>
                    <x-badge :variant="$connection['configured'] ? 'success' : 'warning'" size="sm" dot>
                        {{ $connection['configured'] ? 'مقدارهای ربات ثبت شده‌اند' : 'مقدارهای ربات ناقص‌اند' }}
                    </x-badge>
                </div>
                <p class="text-sm leading-7 text-gray-600 dark:text-gray-300">در BotFather یک ربات بسازید و این سه مقدار را فقط در فایل <code dir="ltr" class="font-mono">.env</code> سرور قرار دهید:</p>
                <dl class="grid gap-3 text-sm">
                    <div class="rounded-xl border border-gray-200 p-3 dark:border-gray-700">
                        <dt dir="ltr" class="font-mono font-semibold">TELEGRAM_BOT_TOKEN</dt>
                        <dd class="mt-1 leading-6 text-gray-600 dark:text-gray-300">توکن محرمانه‌ای که BotFather برای ربات می‌دهد.</dd>
                    </div>
                    <div class="rounded-xl border border-gray-200 p-3 dark:border-gray-700">
                        <dt dir="ltr" class="font-mono font-semibold">TELEGRAM_BOT_USERNAME</dt>
                        <dd class="mt-1 leading-6 text-gray-600 dark:text-gray-300">نام کاربری ربات، با یا بدون علامت &#64;.</dd>
                    </div>
                    <div class="rounded-xl border border-gray-200 p-3 dark:border-gray-700">
                        <dt dir="ltr" class="font-mono font-semibold">TELEGRAM_WEBHOOK_SECRET</dt>
                        <dd class="mt-1 leading-6 text-gray-600 dark:text-gray-300">یک رشتهٔ تصادفی محرمانه از حروف، عدد، خط تیره یا زیرخط؛ همین مقدار باید هنگام ثبت وب‌هوک هم ارسال شود.</dd>
                    </div>
                </dl>
                <p class="text-xs leading-6 text-gray-500 dark:text-gray-400">پس از تغییر فایل محیطی، تنظیمات کش‌شدهٔ لاراول را در سرور به‌روز و migrationهای پروژه را اجرا کنید. توکن و رمز وب‌هوک را در پنل یا پیام‌رسان منتشر نکنید.</p>
            </section>

            <section class="grid content-start gap-4" aria-labelledby="telegram-setup-webhook">
                <h3 id="telegram-setup-webhook" class="text-base font-semibold">۲. ثبت وب‌هوک و اتصال مدیر</h3>
                <ol class="list-decimal space-y-3 ps-5 text-sm leading-7 text-gray-600 dark:text-gray-300">
                    <li>سایت باید آدرس عمومی <span dir="ltr" class="inline-block font-mono">HTTPS</span> داشته باشد.</li>
                    <li>دکمهٔ «ثبت وب‌هوک در تلگرام» را بزنید. سایت، آدرس وب‌هوک را همراه با رمز <code dir="ltr" class="font-mono">TELEGRAM_WEBHOOK_SECRET</code> و نوع پیام <code dir="ltr" class="font-mono">["message"]</code> برای تلگرام ارسال می‌کند.</li>
                </ol>
                <ol start="3" class="list-decimal space-y-3 ps-5 text-sm leading-7 text-gray-600 dark:text-gray-300">
                    <li>از فرم بالای همین صفحه لینک اتصال بسازید، آن را با حساب تلگرام خود باز کنید و Start ربات را بزنید. لینک ۱۰ دقیقه اعتبار دارد و یک‌بارمصرف است.</li>
                    <li>بعد از پیام تأیید ربات، صفحه را تازه کنید و «ارسال پیام آزمایشی» را بزنید.</li>
                </ol>
                <p class="text-xs leading-6 text-gray-500 dark:text-gray-400">نشان «مقدارهای ربات ثبت شده‌اند» فقط تنظیمات محیطی را بررسی می‌کند. پس از ثبت وب‌هوک، دریافت پیام تأیید ربات را هم بررسی کنید.</p>
            </section>

            <section class="grid gap-3 rounded-xl border border-gray-200 p-4 dark:border-gray-700 lg:col-span-2" aria-labelledby="telegram-local-testing">
                <h3 id="telegram-local-testing" class="text-base font-semibold">تست روی سیستم محلی با تونل HTTPS</h3>
                <ol class="list-decimal space-y-2 ps-5 text-sm leading-7 text-gray-600 dark:text-gray-300">
                    <li>پروژه را با Docker اجرا کنید و یک تونل موقت به پورت Nginx محلی باز کنید. پورت پیش‌فرض این پروژه ۸۰۰ است:</li>
                </ol>
                <code dir="ltr" class="block overflow-x-auto rounded-xl bg-gray-100 p-3 text-left text-sm dark:bg-gray-800">cloudflared tunnel --url http://localhost:800</code>
                <ol start="2" class="list-decimal space-y-2 ps-5 text-sm leading-7 text-gray-600 dark:text-gray-300">
                    <li>آدرس HTTPS خروجی، مانند <code dir="ltr" class="font-mono">https://name.trycloudflare.com</code>، را در <code dir="ltr" class="font-mono">TELEGRAM_WEBHOOK_BASE_URL</code> فایل <code dir="ltr" class="font-mono">.env</code> قرار دهید. <code dir="ltr" class="font-mono">APP_URL</code> می‌تواند همان آدرس لوکال بماند.</li>
                    <li>اگر تنظیمات لاراول کش شده‌اند، کش تنظیمات را پاک کنید. سپس همین صفحه را تازه کنید، آدرس نمایش‌داده‌شده را بررسی کنید و دکمهٔ ثبت وب‌هوک را بزنید.</li>
                </ol>
                <p class="text-xs leading-6 text-gray-500 dark:text-gray-400">اگر پورت Nginx را با NGINX_PORT تغییر داده‌اید، همان پورت را در فرمان تونل بگذارید. آدرس تونل با اجرای مجدد آن عوض می‌شود؛ هر بار وب‌هوک را دوباره ثبت کنید.</p>
            </section>
        </div>
    </x-card>
</x-panel-layout>
