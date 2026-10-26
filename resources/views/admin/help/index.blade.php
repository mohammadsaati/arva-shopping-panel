<x-panel-layout title="راهنمای پنل مدیریت" sub-title="مستندات کار با فروشگاه؛ از ثبت کالا تا رسیدگی به سفارش و انتشار محتوا.">
    @push('styles')
        <link rel="stylesheet" href="{{ versioned_assets('assets/karin/styles/admin-guide.css') }}">
    @endpush

    <x-slot:actions>
        <x-button href="{{ route('admin.help.icons') }}" variant="light" icon="ki-filled ki-category">فهرست آفلاین آیکون‌ها</x-button>
    </x-slot:actions>

    <div class="admin-guide admin-manual" dir="rtl">
        <section class="admin-guide__hero">
            <div class="admin-guide__hero-icon" aria-hidden="true"><i class="ki-filled ki-notepad-edit"></i></div>
            <div class="admin-guide__hero-copy">
                <span>مستندات پنل فروشگاه</span>
                <h2>راهنمای کامل مدیریت سایت</h2>
                <p>این راهنما ساختار پنل و ترتیب کارهای اصلی را توضیح می‌دهد. برای هر بخش، مسیر پیشنهادی، فیلدهای مهم و نکته‌های پیش از انتشار را بخوانید.</p>
            </div>
            <a href="#admin-manual-product-workflow" class="admin-guide__hero-link">
                راهنمای ساخت محصول
                <i class="ki-filled ki-arrow-down" aria-hidden="true"></i>
            </a>
        </section>

        <div class="admin-manual__layout">
            <nav class="admin-manual__toc" aria-label="فهرست راهنما">
                <strong>فهرست راهنما</strong>
                <a href="#admin-manual-start">شروع کار</a>
                <a href="#admin-manual-product-workflow">ساخت محصول، مرحله‌به‌مرحله</a>
                <a href="#admin-manual-catalog">دسته، مشخصات و برند</a>
                <a href="#admin-manual-variants">رنگ، سایز و تنوع فروش</a>
                <a href="#admin-manual-homepage">صفحه اصلی و سکشن‌ها</a>
                <a href="#admin-manual-promotions">تخفیف و پیشنهاد شگفت‌انگیز</a>
                <a href="#admin-manual-orders">سفارش و ارسال</a>
                <a href="#admin-manual-reviews">نظرهای کاربران</a>
                <a href="#admin-manual-blog">وبلاگ</a>
                <a href="#admin-manual-settings">تنظیمات، سئو و فوتر</a>
                <a href="#admin-manual-people">کاربران و مناطق ارسال</a>
                <a href="#admin-manual-faq">خطاهای رایج و راه‌حل</a>
            </nav>

            <div class="admin-manual__content">
                <section id="admin-manual-start" class="admin-manual__chapter">
                    <div class="admin-manual__chapter-heading">
                        <span>۰۱</span>
                        <div><h2>از کجا شروع کنم؟</h2><p>برای راه‌اندازی فروشگاه، اول اطلاعات پایه و بعد محتوای قابل‌فروش را آماده کنید.</p></div>
                    </div>
                    <p class="admin-manual__paragraph">داشبورد، تصویر روزانهٔ فروشگاه را نشان می‌دهد: تعداد و ارزش سفارش‌ها، کاربران، تنوع‌های فعال، محصولات کم‌موجودی، روند سفارش‌های هفت روز اخیر و آخرین سفارش‌ها. برای رسیدگی سریع به سفارش یا اصلاح موجودی از همین صفحه وارد جزئیات شوید.</p>
                    <a class="admin-manual__primary-link" href="{{ route('admin.dashboard.index') }}">رفتن به داشبورد <i class="ki-filled ki-arrow-left"></i></a>
                    <div class="admin-manual__callout admin-manual__callout--info">
                        <i class="ki-filled ki-information-2" aria-hidden="true"></i>
                        <p><strong>تفاوت آیتم و محصول:</strong> آیتم، خودِ کالا و اطلاعات مشترک آن مثل نام، دسته، توضیحات و مشخصات است. محصول، گزینهٔ قابل‌خرید همان آیتم است که قیمت، موجودی، رنگ، سایز و تصاویر خودش را دارد. هر آیتم می‌تواند یک یا چند محصول/تنوع داشته باشد.</p>
                    </div>
                    <div class="admin-manual__cards admin-manual__cards--four">
                        <article class="admin-manual__card">
                            <span class="admin-manual__card-icon admin-manual__card-icon--violet"><i class="ki-filled ki-setting-2"></i></span>
                            <h3>۱. اطلاعات فروشگاه</h3>
                            <p>نام سایت، راه‌های تماس، لوگو، سئو و محتوای فوتر را ثبت کنید.</p>
                            <a href="{{ route('admin.settings.edit') }}">تنظیمات سایت <i class="ki-filled ki-arrow-left"></i></a>
                        </article>
                        <article class="admin-manual__card">
                            <span class="admin-manual__card-icon admin-manual__card-icon--blue"><i class="ki-filled ki-category"></i></span>
                            <h3>۲. کاتالوگ</h3>
                            <p>دسته‌ها و مشخصات مربوط به هر دسته را تعریف کنید.</p>
                            <a href="{{ route('admin.categories.index') }}">دسته‌بندی‌ها <i class="ki-filled ki-arrow-left"></i></a>
                        </article>
                        <article class="admin-manual__card">
                            <span class="admin-manual__card-icon admin-manual__card-icon--green"><i class="ki-filled ki-package"></i></span>
                            <h3>۳. کالا و موجودی</h3>
                            <p>آیتم را بسازید، سپس گزینه‌های فروش و موجودی را اضافه کنید.</p>
                            <a href="{{ route('admin.items.index') }}">آیتم‌ها <i class="ki-filled ki-arrow-left"></i></a>
                        </article>
                        <article class="admin-manual__card">
                            <span class="admin-manual__card-icon admin-manual__card-icon--amber"><i class="ki-filled ki-picture"></i></span>
                            <h3>۴. محتوای ویترین</h3>
                            <p>سکشن‌ها، اسلایدرها و بنرهای صفحه‌های فروشگاه را مرتب کنید.</p>
                            <a href="{{ route('admin.sections.index') }}">سکشن‌ها <i class="ki-filled ki-arrow-left"></i></a>
                        </article>
                    </div>
                </section>

                <section id="admin-manual-product-workflow" class="admin-manual__chapter">
                    <div class="admin-manual__chapter-heading">
                        <span>۰۲</span>
                        <div><h2>ساخت محصول؛ فرایند کامل</h2><p>این مسیر را برای ثبت یک کالای تازه طی کنید. اگر کالا از قبل در پنل هست، از مرحلهٔ مربوط به محصول/تنوع شروع کنید.</p></div>
                    </div>

                    <div class="admin-manual__workflow">
                        <article class="admin-manual__workflow-step">
                            <span class="admin-manual__workflow-number">۱</span>
                            <div class="admin-manual__workflow-body">
                                <h3>دسته‌بندی کالا را آماده کنید</h3>
                                <p>دستهٔ اصلی و در صورت نیاز زیر‌دسته را ایجاد کنید. نام باید برای خریدار روشن باشد؛ برای زیر‌دسته، والد مناسب را انتخاب کنید. اولویت نمایش ترتیب را تعیین می‌کند. برای دیده‌شدن در فروشگاه وضعیت دسته را فعال بگذارید.</p>
                                <p class="admin-manual__field-note"><strong>نمونه:</strong> پوشاک ← زنانه ← مانتو. از دسته‌های هم‌معنی و تکراری پرهیز کنید.</p>
                                <a href="{{ route('admin.categories.index') }}">رفتن به دسته‌بندی‌ها <i class="ki-filled ki-arrow-left"></i></a>
                            </div>
                        </article>

                        <article class="admin-manual__workflow-step">
                            <span class="admin-manual__workflow-number">۲</span>
                            <div class="admin-manual__workflow-body">
                                <h3>مشخصات همان دسته را تعریف کنید</h3>
                                <p>در «مشخصات کالا» دسته را باز کنید، گروه‌هایی مثل «مشخصات کلی» بسازید و فیلدهای مورد نیاز را به گروه اضافه کنید. این فیلدها بعداً در فرم آیتم ظاهر می‌شوند و بعضی از آن‌ها می‌توانند در فیلتر فروشگاه استفاده شوند.</p>
                                <ul>
                                    <li><strong>کد ثابت:</strong> شناسهٔ فنی و پایدار مثل <code>material</code>؛ بعد از استفاده، بی‌دلیل عوضش نکنید.</li>
                                    <li><strong>عنوان:</strong> نامی که در فرم و صفحهٔ کالا دیده می‌شود، مثل «جنس».</li>
                                    <li><strong>نوع:</strong> متن، عدد، بله/خیر، انتخاب تکی یا انتخاب چندگانه.</li>
                                    <li><strong>گزینه‌ها:</strong> فقط برای انتخاب‌ها؛ هر خط یک گزینه با قالب <code>کد|عنوان</code>، مثلاً <code>cotton|پنبه</code>.</li>
                                    <li><strong>واحد:</strong> برای فیلد عددی، مانند گیگابایت یا سانتی‌متر.</li>
                                    <li><strong>اجباری:</strong> در زمان ثبت آیتم تکمیل آن لازم می‌شود.</li>
                                    <li><strong>برجسته:</strong> مشخصه را در ویژگی‌های برجستهٔ کالا قرار می‌دهد.</li>
                                    <li><strong>قابل فیلتر:</strong> مشخصه را برای پالایش فهرست محصولات قابل استفاده می‌کند.</li>
                                </ul>
                                <p class="admin-manual__field-note">نوع و گزینه‌ها را از ابتدا درست انتخاب کنید؛ اگر مشخصه برای آیتم‌ها مقدار گرفته باشد حذف آن ممکن نیست.</p>
                                <a href="{{ route('admin.specifications.index') }}">رفتن به مدیریت مشخصات <i class="ki-filled ki-arrow-left"></i></a>
                            </div>
                        </article>

                        <article class="admin-manual__workflow-step">
                            <span class="admin-manual__workflow-number">۳</span>
                            <div class="admin-manual__workflow-body">
                                <h3>داده‌های کمکی را ثبت کنید</h3>
                                <p>اگر کالا برند، رنگ یا سایز دارد، آن گزینه‌ها را پیش از ساخت آیتم/محصول اضافه کنید تا در فرم قابل انتخاب باشند. برند به آیتم وصل می‌شود؛ رنگ و سایز به هر محصول/تنوع وصل می‌شوند.</p>
                                <div class="admin-manual__inline-links">
                                    <a href="{{ route('admin.brands.index') }}">برندها</a>
                                    <a href="{{ route('admin.colors.index') }}">رنگ‌ها</a>
                                    <a href="{{ route('admin.sizes.index') }}">سایزها</a>
                                </div>
                            </div>
                        </article>

                        <article class="admin-manual__workflow-step">
                            <span class="admin-manual__workflow-number">۴</span>
                            <div class="admin-manual__workflow-body">
                                <h3>آیتم را بسازید</h3>
                                <p>از «آیتم‌ها» گزینهٔ افزودن را بزنید و اطلاعات پایه را وارد کنید:</p>
                                <ul>
                                    <li><strong>نام، Slug و کد:</strong> نام کاربرپسند، نشانی یکتای متنی و کد یکتای آیتم.</li>
                                    <li><strong>دسته‌بندی و برند:</strong> دستهٔ درست را انتخاب کنید؛ مشخصات فرم با انتخاب دسته بارگذاری می‌شوند.</li>
                                    <li><strong>توضیحات و تصویر:</strong> تصویر اصلی و توضیح عمومی کالا را ثبت کنید. این اطلاعات بین تنوع‌های همان آیتم مشترک‌اند.</li>
                                    <li><strong>مشخصات کالا:</strong> گروه‌ها را مرور کنید و فیلدهای لازم را پر کنید؛ برای انتخاب چندگانه از انتخابگر چندگزینه‌ای استفاده کنید.</li>
                                    <li><strong>وضعیت:</strong> برای نمایش آیتم، فعال باشد.</li>
                                    <li><strong>پیشنهاد شگفت‌انگیز:</strong> در صورت فعال‌کردن، تاریخ/زمان پایان پیشنهاد را هم تعیین کنید.</li>
                                </ul>
                                <p>فرم آیتم اجازه می‌دهد یک <strong>محصول اولیهٔ اختیاری</strong> همزمان بسازید: قیمت، رنگ، سایز، موجودی، هزینهٔ ارسال و نرخ‌های شهری را وارد کنید. اگر این بخش را خالی بگذارید، آیتم ثبت می‌شود و بعداً محصول جداگانه می‌سازید.</p>
                                <a href="{{ route('admin.items.create') }}">افزودن آیتم <i class="ki-filled ki-arrow-left"></i></a>
                            </div>
                        </article>

                        <article class="admin-manual__workflow-step">
                            <span class="admin-manual__workflow-number">۵</span>
                            <div class="admin-manual__workflow-body">
                                <h3>محصول یا تنوع‌های قابل‌فروش را اضافه کنید</h3>
                                <p>اگر در مرحلهٔ قبل محصول نساختید، از «محصولات ← افزودن» استفاده کنید. آیتم را با جست‌وجو انتخاب کنید و برای هر گزینهٔ قابل خرید یک محصول ثبت کنید.</p>
                                <ul>
                                    <li><strong>آیتم مرتبط:</strong> آیتمی که این محصول به آن تعلق دارد.</li>
                                    <li><strong>رنگ و سایز:</strong> فقط در صورت نیاز انتخاب شوند؛ نبودنشان یعنی این محصول تنوع رنگ/سایز ندارد.</li>
                                    <li><strong>قیمت، موجودی و ارسال:</strong> قیمت به تومان است؛ تعداد موجود را دقیق وارد کنید. نرخ پایهٔ ارسال را می‌توان با نرخ متفاوت برای شهرهای مشخص تکمیل کرد.</li>
                                    <li><strong>تصاویر:</strong> تصویرهای مربوط به همین تنوع را بارگذاری کنید؛ حداکثر ۱۰ تصویر و حداکثر ۵ مگابایت برای هر فایل.</li>
                                    <li><strong>وضعیت:</strong> محصول غیرفعال برای فروش/نمایش در دسترس نیست.</li>
                                </ul>
                                <p class="admin-manual__field-note">مثلاً برای یک کفش با سه سایز، یک آیتم کفش بسازید و سه محصول با سایزهای جداگانه زیر آن ثبت کنید. هر تنوع موجودی و قیمت مستقل دارد.</p>
                                <a href="{{ route('admin.products.create') }}">افزودن محصول/تنوع <i class="ki-filled ki-arrow-left"></i></a>
                            </div>
                        </article>

                        <article class="admin-manual__workflow-step">
                            <span class="admin-manual__workflow-number">۶</span>
                            <div class="admin-manual__workflow-body">
                                <h3>نمایش را بررسی کنید</h3>
                                <p>صفحهٔ آیتم را باز کنید و عنوان، تصویر، مشخصات و محصولات متصل را بررسی کنید. سپس صفحهٔ عمومی کالا و مسیر افزودن به سبد را کنترل کنید. برای خرید، آیتم و محصول باید فعال باشند و موجودی محصول کافی باشد.</p>
                                <div class="admin-manual__inline-links">
                                    <a href="{{ route('admin.items.index') }}">فهرست آیتم‌ها</a>
                                    <a href="{{ route('admin.products.index') }}">فهرست محصولات</a>
                                </div>
                            </div>
                        </article>
                    </div>

                    <div class="admin-manual__callout admin-manual__callout--warning">
                        <i class="ki-filled ki-information-2" aria-hidden="true"></i>
                        <p>در صفحهٔ فروشگاه، آیتم نمایندهٔ کالا است و تنوع‌های محصول گزینه‌های خرید آن هستند. برای رنگ یا سایز متفاوت، آیتم تکراری نسازید؛ تنوع تازه را به‌صورت محصول زیر همان آیتم اضافه کنید.</p>
                    </div>
                </section>

                <section id="admin-manual-catalog" class="admin-manual__chapter">
                    <div class="admin-manual__chapter-heading">
                        <span>۰۳</span>
                        <div><h2>دسته‌بندی، مشخصات و برند</h2><p>این بخش‌ها ساختار کاتالوگ و روش پیدا کردن کالاها را تعیین می‌کنند.</p></div>
                    </div>
                    <div class="admin-manual__doc-grid">
                        <article class="admin-manual__doc-card">
                            <h3><i class="ki-filled ki-category"></i> دسته‌بندی فروشگاه</h3>
                            <p>دسته‌ها می‌توانند والد و فرزند داشته باشند. هر دسته نام، Slug، اولویت، آیکون، تصویر و وضعیت دارد. دستهٔ والد در منوی فروشگاه و زیر‌دسته‌ها در ساختار کاتالوگ استفاده می‌شوند. پیش از تغییر ساختار، بررسی کنید آیتم‌ها به دستهٔ درست وصل باشند.</p>
                            <a href="{{ route('admin.categories.index') }}">مدیریت دسته‌ها</a>
                        </article>
                        <article class="admin-manual__doc-card">
                            <h3><i class="ki-filled ki-setting-2"></i> گروه و فیلد مشخصات</h3>
                            <p>گروه‌ها فقط برای مرتب‌کردن مشخصات هستند؛ مثل «اطلاعات عمومی» و «مشخصات فنی». مشخصات فیلترپذیر باید نوع دادهٔ مناسبی داشته باشند، عنوان واضح و برای عددها واحد داشته باشند.</p>
                            <a href="{{ route('admin.specifications.index') }}">مدیریت مشخصات کالا</a>
                        </article>
                        <article class="admin-manual__doc-card">
                            <h3><i class="ki-filled ki-medal-star"></i> برند</h3>
                            <p>نام، Slug و تصویر نشان برند را ثبت کنید. برند را به آیتم وصل می‌کنید؛ اگر برندی غیرفعال باشد، برای استفادهٔ جدید در ویترین مناسب نیست.</p>
                            <a href="{{ route('admin.brands.index') }}">مدیریت برندها</a>
                        </article>
                    </div>
                </section>

                <section id="admin-manual-variants" class="admin-manual__chapter">
                    <div class="admin-manual__chapter-heading">
                        <span>۰۴</span>
                        <div><h2>رنگ، سایز و مدیریت تنوع</h2><p>مقادیر پایه را یک‌بار ثبت کنید و در محصولات مربوط انتخاب کنید.</p></div>
                    </div>
                    <div class="admin-manual__table-wrap">
                        <table class="admin-manual__table">
                            <thead><tr><th>بخش</th><th>چه چیزی ثبت می‌شود؟</th><th>کجا استفاده می‌شود؟</th></tr></thead>
                            <tbody>
                                <tr><td>رنگ</td><td>نام نمایشی و مقدار رنگ</td><td>گزینهٔ رنگ هر محصول</td></tr>
                                <tr><td>سایز</td><td>مقداری مثل XL یا ۴۲</td><td>گزینهٔ سایز هر محصول</td></tr>
                                <tr><td>محصول</td><td>آیتم، رنگ، سایز، قیمت، موجودی، ارسال، وضعیت و تصاویر</td><td>خرید و انتخاب تنوع در صفحهٔ آیتم</td></tr>
                            </tbody>
                        </table>
                    </div>
                    <p class="admin-manual__paragraph">برای ویرایش محصول، تصویر جدید به گالری افزوده می‌شود. تصویرهای فعلی را می‌توانید در همان فرم جایگزین یا برای حذف علامت‌گذاری کنید و سپس تغییرات را ذخیره کنید. نرخ ارسال شهرها از فهرست شهرهای فعال انتخاب می‌شود.</p>
                    <div class="admin-manual__inline-links">
                        <a href="{{ route('admin.colors.index') }}">رنگ‌ها</a>
                        <a href="{{ route('admin.sizes.index') }}">سایزها</a>
                        <a href="{{ route('admin.products.index') }}">محصولات</a>
                    </div>
                </section>

                <section id="admin-manual-homepage" class="admin-manual__chapter">
                    <div class="admin-manual__chapter-heading">
                        <span>۰۵</span>
                        <div><h2>صفحهٔ اصلی، سکشن‌ها و بنرها</h2><p>محتوا را بعد از فعال‌کردن کالاها بچینید تا مقصدهای ویترین به صفحهٔ خالی نرسند.</p></div>
                    </div>
                    <div class="admin-manual__doc-grid">
                        <article class="admin-manual__doc-card">
                            <h3><i class="ki-filled ki-category"></i> سکشن فروشگاه</h3>
                            <p>برای هر بخش، نام و Slug ثبت کنید، آیتم‌های موردنظر را با انتخاب چندتایی اضافه کنید و در صورت نیاز رنگ پس‌زمینه، تصویر هدر و تصاویر گالری را تنظیم کنید. گالری تصویر جایگاه‌های جدا دارد؛ با «افزودن جایگاه تصویر» عکس‌های بیشتری اضافه کنید. سقف گالری ۱۰ تصویر است. وضعیت باید فعال باشد تا سکشن در سایت نمایش داده شود.</p>
                            <a href="{{ route('admin.sections.index') }}">مدیریت سکشن‌ها</a>
                        </article>
                        <article class="admin-manual__doc-card">
                            <h3><i class="ki-filled ki-picture"></i> اسلایدر</h3>
                            <p>عنوان و تصویر اسلاید را وارد کنید؛ سپس نوع مقصد را انتخاب کنید و فیلد مقصد متناسب را پر کنید: محصول، دسته، سکشن یا پیوند. اولویت کوچک‌تر زودتر نمایش داده می‌شود. مقصد را پس از ذخیره در سایت بازبینی کنید.</p>
                            <a href="{{ route('admin.sliders.index') }}">مدیریت اسلایدرها</a>
                        </article>
                        <article class="admin-manual__doc-card">
                            <h3><i class="ki-filled ki-picture"></i> بنر فروشگاه</h3>
                            <p>تصویر، نوع مقصد و مقصد کلیک را تعیین کنید. مقصد می‌تواند محصول، دسته یا لینک باشد. وضعیت غیرفعال یعنی بنر از ویترین کنار گذاشته می‌شود.</p>
                            <a href="{{ route('admin.banners.index') }}">مدیریت بنرها</a>
                        </article>
                    </div>
                </section>

                <section id="admin-manual-promotions" class="admin-manual__chapter">
                    <div class="admin-manual__chapter-heading">
                        <span>۰۶</span>
                        <div><h2>تخفیف و پیشنهاد شگفت‌انگیز</h2><p>این دو ابزار برای هدف‌های متفاوت هستند و از دو جای پنل مدیریت می‌شوند.</p></div>
                    </div>
                    <div class="admin-manual__doc-grid">
                        <article class="admin-manual__doc-card">
                            <h3><i class="ki-filled ki-discount"></i> پیشنهاد شگفت‌انگیز</h3>
                            <p>در فرم آیتم، پیشنهاد را فعال کنید و پایان آن را با تاریخ و ساعت مشخص کنید. این پیشنهاد به آیتم تعلق دارد. زمان پایان را دقیق بررسی کنید؛ شمارش معکوس سایت روز، ساعت و دقیقهٔ باقی‌مانده را جدا نشان می‌دهد.</p>
                            <a href="{{ route('admin.items.index') }}">رفتن به آیتم‌ها</a>
                        </article>
                        <article class="admin-manual__doc-card">
                            <h3><i class="ki-filled ki-discount"></i> کد تخفیف</h3>
                            <p>نام داخلی، کد، نوع درصدی یا مبلغ ثابت و مقدار را تعیین کنید. سپس محصولات/دسته‌های مجاز و در صورت نیاز کاربر خاص را انتخاب کنید. حداقل مبلغ سفارش، سقف تخفیف، تاریخ شروع و پایان و محدودیت استفاده کلی/هر کاربر اختیاری‌اند. برای تخفیف درصدی، مقدار بین ۱ تا ۱۰۰ است؛ کد هنگام ذخیره به حروف بزرگ تبدیل می‌شود.</p>
                            <a href="{{ route('admin.discounts.index') }}">مدیریت کدهای تخفیف</a>
                        </article>
                    </div>
                </section>

                <section id="admin-manual-orders" class="admin-manual__chapter">
                    <div class="admin-manual__chapter-heading">
                        <span>۰۷</span>
                        <div><h2>رسیدگی به سفارش و ارسال</h2><p>صفحهٔ جزئیات سفارش، اطلاعات لازم برای پیگیری و آماده‌سازی ارسال را یکجا دارد.</p></div>
                    </div>
                    <ol class="admin-manual__numbered-list">
                        <li>از فهرست سفارش‌ها، سفارش را باز کنید و مشتری، شماره تماس گیرنده، استان/شهر و نشانی کامل را بخوانید.</li>
                        <li>اقلام، رنگ و سایز، قیمت زمان ثبت و تعداد را بررسی کنید. این مقادیر به همان شکل سفارش ثبت‌شده نمایش داده می‌شوند.</li>
                        <li>وضعیت پرداخت، مبلغ ارسال، تخفیف، مبلغ نهایی و کد پیگیری را بررسی کنید.</li>
                        <li>از فرم «تغییر وضعیت سفارش» وضعیت درست را انتخاب و ذخیره کنید. وضعیت‌های موجود شامل در انتظار بررسی، در حال آماده‌سازی، متوقف‌شده، ارسال‌شده، تحویل‌شده و لغوشده است.</li>
                        <li>برای دیدن سفارش‌های یک مشتری، از جزئیات او وارد فهرست سفارش‌های همان کاربر شوید.</li>
                    </ol>
                    <div class="admin-manual__callout admin-manual__callout--warning">
                        <i class="ki-filled ki-verify" aria-hidden="true"></i>
                        <p>پیش از تغییر وضعیت، مطمئن شوید اقدام عملی همان مرحله انجام شده است؛ مثلاً وضعیت «ارسال‌شده» را بعد از تحویل سفارش به شرکت حمل ثبت کنید و کد رهگیری را هم نگه دارید.</p>
                    </div>
                    <a class="admin-manual__primary-link" href="{{ route('admin.orders.index') }}">رفتن به سفارش‌ها <i class="ki-filled ki-arrow-left"></i></a>
                </section>

                <section id="admin-manual-reviews" class="admin-manual__chapter">
                    <div class="admin-manual__chapter-heading">
                        <span>۰۸</span>
                        <div><h2>بازبینی نظرهای محصولات</h2><p>نظر کاربران بعد از تأیید مدیر در صفحهٔ عمومی کالا دیده می‌شود.</p></div>
                    </div>
                    <p class="admin-manual__paragraph">کاربر واردشده یا مهمان می‌تواند نظر ثبت کند. نظرها را از فهرست باز کنید و متن، امتیاز، نام نویسنده و نکات مثبت/منفی را بخوانید. مدیر می‌تواند نکات مثبت و منفی را ویرایش یا حذف کند؛ هر فهرست حداکثر ۱۰ مورد دارد. وضعیت نظر را تأیید یا رد کنید و تغییرات را ذخیره کنید. فقط نظر تأییدشده در صفحهٔ محصول نمایش داده می‌شود.</p>
                    <div class="admin-manual__callout admin-manual__callout--info">
                        <i class="ki-filled ki-verify" aria-hidden="true"></i>
                        <p>نشان «خریدار این محصول» براساس سفارش تحویل‌شدهٔ همان کاربر محاسبه می‌شود. این نشان را دستی برای نظر مهمان یا خریدی که هنوز تحویل نشده اضافه نکنید؛ در صفحهٔ مدیریت به‌عنوان اطلاعات خرید تأییدشده نمایش داده می‌شود.</p>
                    </div>
                    <a class="admin-manual__primary-link" href="{{ route('admin.product-reviews.index') }}">رفتن به نظرهای محصولات <i class="ki-filled ki-arrow-left"></i></a>
                </section>

                <section id="admin-manual-blog" class="admin-manual__chapter">
                    <div class="admin-manual__chapter-heading">
                        <span>۰۹</span>
                        <div><h2>مدیریت وبلاگ</h2><p>وبلاگ بخش محتوایی جدا از دسته‌بندی فروشگاه دارد.</p></div>
                    </div>
                    <div class="admin-manual__doc-grid">
                        <article class="admin-manual__doc-card">
                            <h3><i class="ki-filled ki-category"></i> دسته‌بندی وبلاگ</h3>
                            <p>نام، Slug، توضیح، تصویر، دستهٔ والد، وضعیت و اولویت را تنظیم کنید. دستهٔ وبلاگ مستقل است و در کاتالوگ کالا استفاده نمی‌شود. هنگام حذف، اگر نوشته یا زیر‌دسته وابسته باشد، پنل هشدار می‌دهد؛ ابتدا ارتباط‌ها را اصلاح کنید یا حذف را لغو کنید.</p>
                            <a href="{{ route('admin.admin-blog.categories.index') }}">دسته‌های وبلاگ</a>
                        </article>
                        <article class="admin-manual__doc-card">
                            <h3><i class="ki-filled ki-notepad-edit"></i> نوشته</h3>
                            <p>عنوان و Slug، متن در ویرایشگر، خلاصه، دسته‌بندی وبلاگ، تصویر شاخص و وضعیت را ثبت کنید. متن برای انتشار الزامی است. وضعیت پیش‌نویس برای آماده‌سازی، منتشرشده برای نمایش و بایگانی‌شده برای کنارگذاشتن مطلب است. زمان انتشار را با تاریخ‌شمار شمسی انتخاب کنید؛ پنل هنگام ذخیره آن را به میلادی تبدیل می‌کند. برای اسلایدر مطالب شاخص، گزینهٔ نمایش و ترتیب را تنظیم کنید. عنوان و توضیح SEO اختصاصی اختیاری‌اند. از فهرست نوشته‌ها می‌توانید نوشته را ویرایش یا حذف کنید.</p>
                            <a href="{{ route('admin.admin-blog.posts.index') }}">نوشته‌های وبلاگ</a>
                        </article>
                        <article class="admin-manual__doc-card">
                            <h3><i class="ki-filled ki-picture"></i> بنر وبلاگ</h3>
                            <p>عنوان، متن کوتاه، تصویر دسکتاپ و در صورت نیاز تصویر موبایل را تنظیم کنید. پیوند و متن دکمه مقصد را تعیین می‌کنند. وضعیت، ترتیب و زمان شروع/پایان نمایش را بررسی کنید. تاریخ‌ها شمسی انتخاب و هنگام ذخیره تبدیل می‌شوند؛ خالی‌گذاشتن زمان پایان یعنی محدودیت پایانی تعیین نشده است. از فهرست بنرها امکان ویرایش یا حذف هر بنر وجود دارد.</p>
                            <a href="{{ route('admin.admin-blog.banners.index') }}">بنرهای وبلاگ</a>
                        </article>
                    </div>
                    <div class="admin-manual__callout admin-manual__callout--success">
                        <i class="ki-filled ki-check-circle" aria-hidden="true"></i>
                        <p>ترتیب پیشنهادی انتشار: دستهٔ فعال بسازید، نوشته را با تصویر و خلاصهٔ مناسب آماده کنید، پیش‌نمایش را بررسی کنید و فقط در زمان آماده‌بودن وضعیت را روی «منتشرشده» قرار دهید.</p>
                    </div>
                </section>

                <section id="admin-manual-settings" class="admin-manual__chapter">
                    <div class="admin-manual__chapter-heading">
                        <span>۱۰</span>
                        <div><h2>تنظیمات سایت، سئو و فوتر</h2><p>تغییرهای این صفحه در چند جای سایت دیده می‌شوند؛ بعد از ذخیره، هدر و فوتر عمومی را کنترل کنید.</p></div>
                    </div>
                    <div class="admin-manual__doc-grid">
                        <article class="admin-manual__doc-card">
                            <h3><i class="ki-filled ki-setting-2"></i> هویت و تماس</h3>
                            <p>نام سایت، شعار، تلفن‌های پشتیبانی، ساعت پاسخ‌گویی، ایمیل و نشانی تماس را وارد کنید. لوگوی هدر، فوتر، favicon و نشان اپ را هرکدام جدا بارگذاری یا حذف کنید.</p>
                        </article>
                        <article class="admin-manual__doc-card">
                            <h3><i class="ki-filled ki-search-list"></i> سئوی پیش‌فرض</h3>
                            <p>عنوان و توضیح متا برای صفحاتی استفاده می‌شوند که مقدار اختصاصی ندارند. تصویر OG پیش‌نمایش اشتراک‌گذاری است. گزینه‌های index و follow را آگاهانه تنظیم کنید؛ کد تأیید گوگل/بینگ فقط مقدار content است، نه کل تگ HTML.</p>
                        </article>
                        <article class="admin-manual__doc-card">
                            <h3><i class="ki-filled ki-menu"></i> متن فوتر</h3>
                            <p>معرفی فروشگاه، متن کپی‌رایت، عنوان شبکه‌های اجتماعی، متن خبرنامه و معرفی اپ را در تنظیمات سایت بنویسید. آیتم‌های تکرارشوندهٔ فوتر از مدیریت جداگانهٔ فوتر اضافه می‌شوند.</p>
                        </article>
                    </div>
                    <h3 class="admin-manual__subheading">آیتم‌های تکرارشوندهٔ فوتر</h3>
                    <p class="admin-manual__paragraph">در مدیریت فوتر، نوع محتوا را انتخاب کنید: مزیت‌ها، ستون‌های لینک، لینک‌های هر ستون، شبکه‌های اجتماعی یا لینک اپلیکیشن. هر آیتم عنوان/برچسب، نشانی، آیکون یا تصویر، بازشدن در تب جدید، ترتیب و وضعیت دارد. برای لینک‌ها اول ستون مقصد را بسازید؛ سپس لینک‌ها را به همان ستون وصل کنید. آیتم غیرفعال در فوتر نمایش داده نمی‌شود.</p>
                    <h3 class="admin-manual__subheading">اتصال تلگرام ادمین</h3>
                    <p class="admin-manual__paragraph">هر مدیر می‌تواند از صفحهٔ اعلان‌های تلگرام لینک اتصال اختصاصی بگیرد، ربات را در گفت‌وگوی خصوصی Start کند، پیام آزمایشی بفرستد یا اتصال خود را قطع کند. لینک اتصال یک‌بارمصرف است و ۱۰ دقیقه اعتبار دارد.</p>
                    <div class="admin-manual__inline-links">
                        <a href="{{ route('admin.settings.edit') }}">تنظیمات سایت</a>
                        <a href="{{ route('admin.telegram.index') }}">اعلان‌های تلگرام</a>
                        <a href="{{ route('admin.footer.index', ['kind' => 'features']) }}">محتوای فوتر</a>
                        <a href="{{ route('admin.newsletter-subscribers.index') }}">مشترکان خبرنامه</a>
                    </div>
                </section>

                <section id="admin-manual-people" class="admin-manual__chapter">
                    <div class="admin-manual__chapter-heading">
                        <span>۱۱</span>
                        <div><h2>کاربران و مناطق ارسال</h2><p>اطلاعات حساب‌ها و فهرست مناطق برای عملیات سفارش و هزینهٔ ارسال به کار می‌روند.</p></div>
                    </div>
                    <div class="admin-manual__doc-grid">
                        <article class="admin-manual__doc-card">
                            <h3><i class="ki-filled ki-users"></i> کاربران</h3>
                            <p>فهرست کاربران را جست‌وجو و مشاهده کنید، جزئیات حساب و پروفایل را ببینید، نوع/وضعیت را در صورت نیاز ویرایش کنید و سفارش‌های همان کاربر را جداگانه باز کنید. هنگام ایجاد حساب، نام، نام خانوادگی، تلفن، نوع حساب و رمز عبور لازم است.</p>
                            <a href="{{ route('admin.users.index') }}">مدیریت کاربران</a>
                        </article>
                        <article class="admin-manual__doc-card">
                            <h3><i class="ki-filled ki-map"></i> استان و شهر</h3>
                            <p>ابتدا استان را بسازید، سپس شهر را به استان وصل کنید. شهرهای فعال در نرخ‌های ارسال محصول قابل انتخاب‌اند. برای هزینهٔ متفاوت در شهر خاص، نرخ را در فرم محصول وارد کنید؛ در غیر این صورت هزینهٔ پایهٔ ارسال محصول استفاده می‌شود.</p>
                            <div class="admin-manual__inline-links">
                                <a href="{{ route('admin.provinces.index') }}">استان‌ها</a>
                                <a href="{{ route('admin.cities.index') }}">شهرها</a>
                            </div>
                        </article>
                    </div>
                </section>

                <section id="admin-manual-faq" class="admin-manual__chapter">
                    <div class="admin-manual__chapter-heading">
                        <span>۱۲</span>
                        <div><h2>خطاهای رایج و راه‌حل</h2><p>قبل از ثبت تیکت یا تغییر داده، این موارد را بررسی کنید.</p></div>
                    </div>
                    <div class="admin-manual__faq-list">
                        <details>
                            <summary>چرا فیلدهای مشخصات آیتم نمایش داده نمی‌شوند؟</summary>
                            <p>ابتدا دسته‌بندی را انتخاب کنید. اگر همچنان فیلدی نیست، در مدیریت مشخصات همان دسته گروه و مشخصه تعریف کنید و صفحهٔ فرم آیتم را دوباره باز کنید.</p>
                        </details>
                        <details>
                            <summary>چرا کالا در سایت دیده نمی‌شود؟</summary>
                            <p>وضعیت آیتم، محصول، دسته و سکشن مرتبط را بررسی کنید. برای امکان خرید، محصول باید موجودی کافی داشته باشد. همچنین مطمئن شوید آیتم به سکشن موردنظر افزوده شده یا در مسیر کاتالوگ عمومی قابل دسترس است.</p>
                        </details>
                        <details>
                            <summary>چرا Select2 گزینه را پیدا نمی‌کند یا ادامهٔ نتایج نمی‌آید؟</summary>
                            <p>عبارت کوتاه‌تری مثل بخشی از نام یا کد را جست‌وجو کنید و چند لحظه برای دریافت نتیجه صبر کنید. فهرست‌های بزرگ از جست‌وجوی سروری و صفحه‌بندی هنگام اسکرول استفاده می‌کنند. اگر آیتمی در انتخابگر پیدا نمی‌شود، فعال‌بودن آیتم را بررسی کنید.</p>
                        </details>
                        <details>
                            <summary>چرا تصویر پس از بارگذاری در سایت نیست؟</summary>
                            <p>بعد از انتخاب فایل، دکمهٔ ذخیره فرم را بزنید. حجم فایل و نوع تصویر را رعایت کنید؛ در ویرایش، حذف تصویر فعلی را علامت نزنید مگر اینکه واقعاً بخواهید آن حذف شود.</p>
                        </details>
                        <details>
                            <summary>تاریخ شمسی را چگونه وارد کنم؟</summary>
                            <p>از تاریخ‌شمار همان فرم استفاده کنید. در بخش‌هایی مثل انتشار وبلاگ، بنر وبلاگ، کد تخفیف و پیشنهاد شگفت‌انگیز، تاریخ انتخابی هنگام ذخیره به قالب میلادی پایگاه داده تبدیل می‌شود.</p>
                        </details>
                        <details>
                            <summary>چرا نظر مشتری در صفحهٔ کالا دیده نمی‌شود؟</summary>
                            <p>نظر باید از بخش بازبینی باز و وضعیت آن روی تأییدشده قرار داده شود. وضعیت رد یا در انتظار تأیید در صفحهٔ عمومی نمایش داده نمی‌شود.</p>
                        </details>
                        <details>
                            <summary>آیکون دسته یا فوتر را از کجا پیدا کنم؟</summary>
                            <p>صفحهٔ آفلاین آیکون‌ها را از بالای این راهنما باز کنید، نام آیکون را جست‌وجو و کلاس آن را کپی کنید؛ سپس کلاس را در فیلد آیکون قرار دهید.</p>
                        </details>
                    </div>
                    <div class="admin-manual__callout admin-manual__callout--info">
                        <i class="ki-filled ki-information-2" aria-hidden="true"></i>
                        <p><strong>قاعدهٔ عمومی:</strong> در فهرست‌ها، فیلتر و جست‌وجو را به‌کار ببرید؛ قبل از غیرفعال‌کردن یا حذف داده، موارد وابسته را بررسی کنید؛ پس از ذخیرهٔ محتوای عمومی، همان صفحه را در سایت باز کنید و نتیجه را ببینید.</p>
                    </div>
                    <a class="admin-manual__primary-link" href="{{ route('admin.help.icons') }}">بازکردن فهرست آفلاین آیکون‌ها <i class="ki-filled ki-arrow-left"></i></a>
                </section>
            </div>
        </div>
    </div>
</x-panel-layout>
