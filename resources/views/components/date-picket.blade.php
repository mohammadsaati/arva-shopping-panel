@props([
    'label'         => '',
    'id'            => '',
    'name'          => '',
    'value'         => '',
    'placeholder'   => 'انتخاب تاریخ',
    'form'          => '',
    'classes'       => 'w-full min-w-0 text-right',
    'canSelectTime' => true,
    'initTime'      => '',
    'minDate'       => '',
    'maxDate'       => '',
    'disabled'      => false,
    'showHolidays'  => 'persian,islamic',
    'autoClose'     => true,
    'isRequired'    => false,
])
@php
    $inputId  = $id ?: 'input_' . str_replace(['.', '[', ']'], '_', $name);
    $hasError = $errors->has($name);
    $errorMsg = $hasError ? $errors->first($name) : null;
    $onlyDate = $canSelectTime ? 'false' : 'true';
    $autoCloseAttr = $autoClose ? 'true' : 'false';
@endphp

<div class="flex flex-col gap-1">
    @if($label)
        <label for="{{ $inputId }}" class="form-label">
            {{ $label }}
            @if($isRequired) <span class="text-danger">*</span> @endif
        </label>
    @endif

    <div class="input {{ $hasError ? 'border-danger' : '' }}">
        <input
            type="text"
            data-pdp
            id="{{ $inputId }}"
            name="{{ $name }}"
            value="{{ $value }}"
            placeholder="{{ $placeholder }}"
            class="grow bg-transparent outline-none w-full cursor-pointer text-right"
            style="unicode-bidi: plaintext; direction: ltr; text-align: right;"
            readonly
            autocomplete="off"
            data-pdp-only-date="{{ $onlyDate }}"
            data-pdp-auto-close="{{ $autoCloseAttr }}"
            @if($initTime) data-pdp-init-time="{{ $initTime }}" @endif
            @if($minDate) data-pdp-min-date="{{ $minDate }}" @endif
            @if($maxDate) data-pdp-max-date="{{ $maxDate }}" @endif
            @if($showHolidays) data-pdp-holidays="{{ $showHolidays }}" @endif
            @if($form) form="{{ $form }}" @endif
            @if($disabled) disabled @endif
            {{ $attributes->only('wire:model') }}
        />
    </div>

    @if($errorMsg)
        <p class="text-danger text-xs mt-0.5">{{ $errorMsg }}</p>
    @endif
</div>


@pushonce('scripts')
    <script>
        (function() {
            'use strict';

            var MONTHS = ['فروردین','اردیبهشت','خرداد','تیر','مرداد','شهریور','مهر','آبان','آذر','دی','بهمن','اسفند'];
            var DAYS = ['ش','ی','د','س','چ','پ','ج'];

            function g2j(gy, gm, gd) {
                var m = [0,31,59,90,120,151,181,212,243,273,304,334];
                var g2 = (gm > 2) ? (gy + 1) : gy;
                var d = 355666 + (365*gy) + ~~((g2+3)/4) - ~~((g2+99)/100) + ~~((g2+399)/400) + gd + m[gm-1];
                var jy = -1595 + (33 * ~~(d/12053)); d %= 12053;
                jy += 4 * ~~(d/1461); d %= 1461;
                if (d > 365) { jy += ~~((d-1)/365); d = (d-1) % 365; }
                var jm = (d < 186) ? 1 + ~~(d/31) : 7 + ~~((d-186)/30);
                var jd = 1 + ((d < 186) ? (d % 31) : ((d-186) % 30));
                return {y: jy, m: jm, d: jd};
            }

            function j2g(jy, jm, jd) {
                var gy = (jy <= 979) ? 621 : 1600; jy -= (jy <= 979) ? 0 : 979;
                var d = (365*jy) + ~~(jy/33)*8 + ~~((jy%33+3)/4) + 78 + jd + ((jm<7) ? (jm-1)*31 : (jm-7)*30+186);
                gy += 400 * ~~(d/146097); d %= 146097;
                if (d > 36524) { gy += 100 * ~~(--d/36524); d %= 36524; if (d >= 365) d++; }
                gy += 4 * ~~(d/1461); d %= 1461;
                if (d > 365) { gy += ~~((d-1)/365); d = (d-1) % 365; }
                var gd = d + 1, gm, t = [0,31,59,90,120,151,181,212,243,273,304,334];
                if (gy%4===0 && (gy%100!==0 || gy%400===0)) { if (gd > 60) gd--; else if (gd === 60) return {y:gy, m:2, d:29}; }
                for (gm = 0; gm < 12 && t[gm] < gd; gm++) {}
                gd -= t[gm-1];
                return {y: gy, m: gm, d: gd};
            }

            function mLen(jy, jm) {
                if (jm <= 6) return 31;
                if (jm <= 11) return 30;
                return (((jy%33)%4) === 1) ? 30 : 29;
            }

            function wd1(jy, jm) {
                var g = j2g(jy, jm, 1);
                return (new Date(g.y, g.m-1, g.d).getDay() + 1) % 7;
            }

            function today() { var n = new Date(); return g2j(n.getFullYear(), n.getMonth()+1, n.getDate()); }
            function parse(val) {
                if (!val || !val.trim()) return null;
                var p = val.trim().split(/[\s/\-]+/);
                if (p.length < 3) return null;
                var y = +p[0], m = +p[1], d = +p[2];
                return (isNaN(y)||isNaN(m)||isNaN(d)) ? null : {y:y, m:m, d:d};
            }
            function parseTime(val) {
                if (!val) return {h:0,m:0,s:0};
                var p = val.trim().split(/\s+/);
                if (p.length < 2) return {h:0,m:0,s:0};
                var t = p[1].split(':');
                return {h: +t[0]||0, m: +t[1]||0, s: +t[2]||0};
            }
            function fmt(j, time) {
                var s = j.y + '/' + String(j.m).padStart(2,'0') + '/' + String(j.d).padStart(2,'0');
                if (time) s += ' ' + String(time.h||0).padStart(2,'0') + ':' + String(time.m||0).padStart(2,'0') + ':' + String(time.s||0).padStart(2,'0');
                return s;
            }
            function cmp(a, b) { return (a.y - b.y) || (a.m - b.m) || (a.d - b.d); }
            function pad(n) { return String(n).padStart(2, '0'); }
            function el(tag, cls) { var e = document.createElement(tag); if (cls) e.className = cls; return e; }
            function css(e, s) { Object.assign(e.style, s); }
            function hoverable(e, hoverBg) {
                var orig = e.style.backgroundColor || '';
                e.addEventListener('mouseenter', function() { e.style.backgroundColor = hoverBg; });
                e.addEventListener('mouseleave', function() { e.style.backgroundColor = orig; });
            }

            /* ── Hijri conversion ── */
            var HMD = {
                sy: 1427, sj: 2192399, ey: 1464, ej: 2195868,
                d: {
                    1427:[355,30,29,29,30,29,30,30,30,30,29,29,30],1428:[354,29,30,29,29,29,30,30,29,30,30,30,29],
                    1429:[354,30,29,30,29,29,29,30,30,29,30,30,29],1430:[354,30,30,29,29,30,29,30,29,29,30,30,29],
                    1431:[354,30,30,29,30,29,30,29,30,29,29,30,29],1432:[355,30,30,29,30,30,30,29,29,30,29,30,29],
                    1433:[355,29,30,29,30,30,30,29,30,29,30,29,30],1434:[354,29,29,30,29,30,30,29,30,30,29,30,29],
                    1435:[355,29,30,29,30,29,30,29,30,30,30,29,30],1436:[354,29,30,29,29,30,29,30,29,30,29,30,30],
                    1437:[354,29,30,30,29,30,29,29,30,29,29,30,30],1438:[354,29,30,30,30,29,30,29,29,30,29,29,30],
                    1439:[354,29,30,30,30,30,29,30,29,29,30,29,29],1440:[355,30,29,30,30,30,29,30,30,29,29,30,29],
                    1441:[355,29,30,29,30,30,29,30,30,29,30,29,30],1442:[354,29,29,30,29,30,29,30,30,29,30,30,29],
                    1443:[354,29,30,30,29,29,30,29,30,30,29,30,29],1444:[354,30,30,29,30,29,29,30,29,30,29,30,29],
                    1445:[354,30,30,30,29,30,29,29,30,29,30,29,29],1446:[355,30,30,30,29,30,30,29,30,29,29,30,29],
                    1447:[355,29,30,29,30,30,30,29,30,30,29,29,30],1448:[354,29,29,30,29,30,30,29,30,30,30,29,29],
                    1449:[355,30,29,29,30,29,30,29,30,30,30,29,30],1450:[354,29,30,29,29,30,29,30,29,30,30,30,29],
                    1451:[354,30,29,30,29,29,30,29,30,29,30,30,29],1452:[354,30,30,29,30,29,29,30,29,30,29,30,29],
                    1453:[355,30,30,29,30,29,30,30,29,29,30,29,30],1454:[354,29,30,29,30,30,29,30,30,29,30,29,29],
                    1455:[355,30,29,30,29,30,29,30,30,29,30,30,29],1456:[355,29,30,29,29,30,29,30,30,29,30,30,30],
                    1457:[354,29,29,30,29,29,30,29,30,29,30,30,30],1458:[354,30,29,29,30,29,29,30,29,30,29,30,30],
                    1459:[354,30,29,30,29,30,29,29,30,29,30,29,30],1460:[354,30,29,30,30,29,30,29,29,30,29,30,29],
                    1461:[355,30,29,30,30,29,30,30,29,29,30,29,30],1462:[354,29,30,29,30,29,30,30,29,30,29,30,29],
                    1463:[355,30,29,30,29,30,29,30,29,30,30,29,30],1464:[354,30,29,29,30,29,29,30,29,30,30,29,30]
                }
            };

            function j2h(jy, jm, jd) {
                jy += 1595;
                var jdn = 1365392 + (365*jy) + (~~(jy/33)*8) + ~~((jy%33+3)/4) + jd + ((jm<7) ? (jm-1)*31 : (jm-7)*30+186) - 0.5;
                jy -= 1595;
                if (jdn >= HMD.sj && jdn <= HMD.ej) {
                    var rem = Math.floor(jdn - HMD.sj + 1);
                    for (var y = HMD.sy; y <= HMD.ey; y++) {
                        var yd = HMD.d[y];
                        if (rem > yd[0]) { rem -= yd[0]; } else {
                            var mo = 1;
                            while (mo < 13 && rem > yd[mo]) { rem -= yd[mo]; mo++; }
                            return {y: y, m: mo, d: rem};
                        }
                    }
                }
                var hy = Math.floor(((30*(jdn-1948439.5))+10646)/10631);
                var hm = Math.floor(((jdn - (1948439.5+((hy-1)*354)+Math.floor((3+(11*hy))/30)))-29)/29.5)+1.99;
                hm = Math.min(12, Math.floor(hm));
                var hd = Math.floor(1+jdn-(1948439.5+((hy-1)*354)+Math.floor((3+(11*hy))/30))-Math.floor((29.5*(hm-1))+0.5));
                hy -= 990;
                return {y: hy, m: hm, d: hd};
            }

            /* ── Event data ── */
            var PE = [
                [1,1,"عید نوروز",1],[1,2,"عید نوروز",1],[1,3,"عید نوروز",1],[1,4,"عید نوروز",1],
                [1,6,"ولادت زرتشت",0],[1,7,"روز هنرهای نمایشی",0],[1,12,"روز جمهوری اسلامی",1],
                [1,13,"روز طبیعت",1],[1,18,"روز سلامتی",0],[1,20,"روز ملی فناوری هسته‌ای",0],
                [1,25,"روز بزرگداشت عطار نیشابوری",0],[1,29,"روز ارتش و نیروی زمینی",0],
                [2,1,"روز بزرگداشت سعدی",0],[2,3,"روز بزرگداشت شیخ بهایی / روز معماری",0],
                [2,7,"روز ایمنی حمل و نقل",0],[2,9,"روز شوراها",0],[2,10,"روز ملی خلیج فارس",0],
                [2,15,"روز بزرگداشت شیخ صدوق",0],[2,25,"روز پاسداشت زبان فارسی و بزرگداشت فردوسی",0],
                [2,28,"روز بزرگداشت حکیم عمر خیام",0],[2,30,"روز ملی جمعیت",0],[2,31,"روز اهدای عضو",0],
                [3,1,"روز بهره‌وری / بزرگداشت ملاصدرا",0],[3,14,"رحلت امام خمینی",1],
                [3,15,"قیام خونین 15 خرداد",1],[3,29,"درگذشت دکتر علی شریعتی",0],
                [3,31,"شهادت دکتر چمران / روز بسیج استادان",0],[4,1,"روز اصناف",0],
                [4,7,"روز قوه قضاییه",0],[4,8,"روز مبارزه با سلاح‌های شیمیایی",0],
                [4,10,"روز صنعت و معدن",0],[4,14,"روز قلم",0],[4,25,"روز بهزیستی و تامین اجتماعی",0],
                [5,9,"روز اهدای خون",0],[5,14,"روز خانواده و تکریم بازنشستگان",0],
                [5,17,"روز خبرنگار",0],[5,23,"روز مقاومت اسلامی",0],
                [5,30,"بزرگداشت علامه مجلسی / روز جهانی مسجد",0],
                [6,1,"بزرگداشت ابوعلی سینا / روز پزشک",0],[6,4,"روز کارمند",0],
                [6,5,"بزرگداشت رازی / روز داروسازی",0],[6,8,"روز مبارزه با تروریسم",0],
                [6,13,"بزرگداشت ابوریحان بیرونی / روز تعاون",0],[6,21,"روز سینما",0],
                [6,27,"بزرگداشت شهریار / روز شعر و ادب فارسی",0],
                [7,7,"روز آتش‌نشانی / بزرگداشت شمس",0],[7,8,"روز بزرگداشت مولوی",0],
                [7,13,"روز نیروی انتظامی",0],[7,20,"روز بزرگداشت حافظ",0],
                [7,26,"روز تربیت بدنی و ورزش",0],[7,29,"روز صادرات",0],
                [8,8,"روز نوجوان و بسیج دانشجویی",0],[8,13,"روز دانش‌آموز",0],
                [8,24,"روز کتاب و کتابدار",0],[9,7,"روز نیروی دریایی",0],
                [9,16,"روز دانشجو",0],[9,25,"روز پژوهش",0],[9,30,"شب یلدا",0],
                [10,5,"روز ایمنی در برابر زلزله",0],[11,14,"روز فناوری فضایی",0],
                [11,19,"روز نیروی هوایی",0],[11,22,"پیروزی انقلاب اسلامی",1],
                [12,5,"بزرگداشت خواجه نصیر / روز مهندسی",0],[12,14,"روز احسان و نیکوکاری",0],
                [12,15,"روز درختکاری",0],[12,20,"روز راهیان نور",0],
                [12,25,"بزرگداشت پروین اعتصامی",0],[12,29,"روز ملی شدن صنعت نفت",1]
            ];

            var IE = [
                [1,1,"آغاز سال نو هجری قمری",0],[1,9,"تاسوعای حسینی",1],[1,10,"عاشورای حسینی",1],
                [1,12,"شهادت امام سجاد",0],[2,20,"اربعین حسینی",1],
                [2,28,"رحلت حضرت رسول / شهادت امام حسن مجتبی",1],[2,30,"شهادت امام رضا",1],
                [3,1,"هجرت حضرت رسول از مکه به مدینه",0],[3,8,"شهادت امام حسن عسکری",1],
                [3,17,"ولادت حضرت رسول اکرم",1],[4,8,"ولادت امام حسن عسکری",0],
                [4,10,"وفات حضرت معصومه",0],[5,5,"ولادت حضرت زینب",0],
                [6,3,"شهادت حضرت فاطمه",1],[6,13,"وفات حضرت ام‌البنین",0],
                [6,20,"ولادت حضرت فاطمه / روز زن",0],[7,1,"ولادت امام محمد باقر",0],
                [7,3,"شهادت امام علی نقی",0],[7,10,"ولادت امام محمد تقی",0],
                [7,13,"ولادت امام علی",1],[7,15,"ارتحال حضرت زینب",0],
                [7,25,"شهادت امام موسی کاظم",0],[7,27,"مبعث حضرت رسول اکرم",1],
                [8,3,"ولادت امام حسین",0],[8,4,"ولادت ابوالفضل عباس",0],
                [8,5,"ولادت امام سجاد",0],[8,11,"ولادت علی اکبر",0],
                [8,15,"ولادت حضرت قائم",1],[9,15,"ولادت امام حسن مجتبی",0],
                [9,18,"شب قدر",0],[9,19,"ضربت خوردن امام علی",0],
                [9,20,"شب قدر",0],[9,21,"شهادت حضرت علی",1],[9,22,"شب قدر",0],
                [10,1,"عید فطر",1],[10,2,"تعطیلات عید فطر",1],
                [10,25,"شهادت امام جعفر صادق",1],[11,1,"ولادت حضرت معصومه",0],
                [11,11,"ولادت امام رضا",0],[11,30,"شهادت امام محمد تقی",0],
                [12,1,"ازدواج امام علی و حضرت فاطمه",0],[12,7,"شهادت امام محمد باقر",0],
                [12,9,"روز عرفه",0],[12,10,"عید قربان",1],
                [12,15,"ولادت امام علی نقی",0],[12,18,"عید غدیر خم",1],
                [12,20,"ولادت امام موسی کاظم",0]
            ];

            var WOE = [
                [1,1,"جشن آغاز سال نو میلادی",0],[1,24,"روز جهانی آموزش",0],
                [1,27,"روز جهانی یادبود هولوکاست",0],[2,11,"روز جهانی زنان و دختران در علم",0],
                [2,14,"جشن ولنتاین",0],[2,20,"روز جهانی عدالت اجتماعی",0],
                [2,21,"روز جهانی زبان مادری",0],[3,8,"روز جهانی زن",0],
                [3,14,"روز جهانی ریاضیات",0],[3,20,"روز جهانی شادی",0],
                [3,21,"روز جهانی نوروز / روز شعر",0],[3,22,"روز جهانی آب",0],
                [3,27,"روز جهانی تئاتر",0],[4,7,"روز جهانی بهداشت",0],
                [4,22,"روز زمین",0],[4,23,"روز جهانی کتاب",0],
                [5,1,"روز جهانی کارگر",0],[5,3,"روز جهانی آزادی مطبوعات",0],
                [5,8,"روز جهانی صلیب سرخ و هلال احمر",0],[5,15,"روز جهانی خانواده",0],
                [5,17,"روز جهانی ارتباطات",0],[5,18,"روز جهانی موزه",0],
                [5,31,"روز جهانی بدون دخانیات",0],[6,5,"روز جهانی محیط زیست",0],
                [6,8,"روز جهانی اقیانوس‌ها",0],[6,12,"روز مبارزه با کار کودکان",0],
                [6,14,"روز جهانی اهدای خون",0],[6,20,"روز جهانی پناهندگان",0],
                [7,11,"روز جهانی جمعیت",0],[7,18,"روز جهانی نلسون ماندلا",0],
                [8,9,"روز جهانی بومیان",0],[8,12,"روز جهانی جوانان",0],
                [9,8,"روز جهانی سوادآموزی",0],[9,21,"روز جهانی صلح",0],
                [9,27,"روز جهانی جهان‌گردی",0],[10,1,"روز جهانی سالمندان",0],
                [10,5,"روز جهانی آموزگار",0],[10,9,"روز جهانی پست",0],
                [10,10,"روز جهانی بهداشت روان",0],[10,16,"روز جهانی غذا",0],
                [10,24,"روز جهانی سازمان ملل",0],[11,14,"روز جهانی دیابت",0],
                [11,25,"روز مبارزه با خشونت علیه زنان",0],[12,1,"روز جهانی ایدز",0],
                [12,3,"روز جهانی افراد دارای معلولیت",0],[12,10,"روز جهانی حقوق بشر",0],
                [12,25,"جشن کریسمس",0]
            ];

            var WUE = [
                [7,1,"روز جهانی بال مرغ",0],[7,7,"روز جهانی شکلات",0],
                [7,30,"روز جهانی دوستی",0],[8,12,"روز جهانی فیل",0],
                [8,19,"روز جهانی عکاسی",0],[9,13,"روز جهانی برنامه‌نویسان",0],
                [9,29,"روز جهانی نجوم",0]
            ];

            function findEvents(arr, m, d) {
                var r = [];
                for (var i = 0; i < arr.length; i++) { if (arr[i][0] === m && arr[i][1] === d) r.push(arr[i]); }
                return r;
            }

            function getEvents(jy, jm, jd, types) {
                var evts = [];
                if (types.persian) {
                    var pe = findEvents(PE, jm, jd);
                    for (var i = 0; i < pe.length; i++) evts.push({t: pe[i][2], h: !!pe[i][3], c: 'persian'});
                }
                if (types.islamic) {
                    var hd = j2h(jy, jm, jd);
                    var ie = findEvents(IE, hd.m, hd.d);
                    for (var i = 0; i < ie.length; i++) evts.push({t: ie[i][2], h: !!ie[i][3], c: 'islamic'});
                }
                if (types.worldOfficial) {
                    var gd = j2g(jy, jm, jd);
                    var we = findEvents(WOE, gd.m, gd.d);
                    for (var i = 0; i < we.length; i++) evts.push({t: we[i][2], h: !!we[i][3], c: 'worldOfficial'});
                }
                if (types.worldUnofficial) {
                    var gd2 = j2g(jy, jm, jd);
                    var wu = findEvents(WUE, gd2.m, gd2.d);
                    for (var i = 0; i < wu.length; i++) evts.push({t: wu[i][2], h: !!wu[i][3], c: 'worldUnofficial'});
                }
                return evts;
            }

            function parseHolidayTypes(str) {
                var types = {persian: false, islamic: false, worldOfficial: false, worldUnofficial: false};
                if (!str) return types;
                var parts = str.split(',');
                for (var i = 0; i < parts.length; i++) {
                    var p = parts[i].trim();
                    if (p === 'persian') types.persian = true;
                    else if (p === 'islamic') types.islamic = true;
                    else if (p === 'worldOfficial') types.worldOfficial = true;
                    else if (p === 'worldUnofficial') types.worldUnofficial = true;
                    else if (p === 'all') { types.persian = true; types.islamic = true; types.worldOfficial = true; types.worldUnofficial = true; }
                }
                return types;
            }

            /* ── Tooltip ── */
            var tooltip = null;
            function showTooltip(anchor, lines, dk) {
                hideTooltip();
                tooltip = el('div', 'fixed rounded-md py-1.5 px-2.5 text-xs leading-5 ss01 max-w-[220px] pointer-events-none');
                css(tooltip, {
                    zIndex: '10001',
                    backgroundColor: dk ? 'var(--tw-gray-300)' : 'var(--tw-gray-900)',
                    color: dk ? 'var(--tw-gray-900)' : '#fff',
                    boxShadow: '0 2px 8px rgba(0,0,0,.25)',
                    direction: 'rtl',
                    whiteSpace: 'pre-wrap'
                });
                tooltip.textContent = lines.join('\n');
                document.body.appendChild(tooltip);
                var r = anchor.getBoundingClientRect();
                tooltip.style.left = (r.left + r.width/2 - tooltip.offsetWidth/2) + 'px';
                tooltip.style.top = (r.top - tooltip.offsetHeight - 4) + 'px';
                if (parseFloat(tooltip.style.top) < 4) tooltip.style.top = (r.bottom + 4) + 'px';
            }
            function hideTooltip() {
                if (tooltip && tooltip.parentNode) tooltip.parentNode.removeChild(tooltip);
                tooltip = null;
            }

            /* ── Event dot color ── */
            function dotColor(evts) {
                for (var i = 0; i < evts.length; i++) { if (evts[i].h) return 'var(--tw-danger)'; }
                for (var i = 0; i < evts.length; i++) { if (evts[i].c === 'islamic') return 'var(--tw-success)'; }
                return 'var(--tw-primary)';
            }

            /* ── Popup ── */
            function createPopup(input) {
                var onlyDate = input.getAttribute('data-pdp-only-date') === 'true';
                var autoClose = input.getAttribute('data-pdp-auto-close') !== 'false';
                var initTime = input.getAttribute('data-pdp-init-time') || '';
                var minStr = input.getAttribute('data-pdp-min-date') || '';
                var maxStr = input.getAttribute('data-pdp-max-date') || '';
                if (minStr.toLowerCase() === 'today') minStr = fmt(today(), null);
                if (maxStr.toLowerCase() === 'today') maxStr = fmt(today(), null);
                var minJ = minStr ? parse(minStr) : null;
                var maxJ = maxStr ? parse(maxStr) : null;
                var holidayTypes = parseHolidayTypes(input.getAttribute('data-pdp-holidays') || '');
                var hasHolidays = holidayTypes.persian || holidayTypes.islamic || holidayTypes.worldOfficial || holidayTypes.worldUnofficial;

                var dk = document.documentElement.classList.contains('dark');
                var cur = parse(input.value) || today();
                var sel = parse(input.value);
                var time = parseTime(input.value);
                if (initTime && !input.value) {
                    var tp = initTime.split(':');
                    time = {h: +tp[0]||0, m: +tp[1]||0, s: +tp[2]||0};
                }
                var td = today();
                if (!sel && minJ && cmp(minJ, td) === 0) {
                    sel = {y: td.y, m: td.m, d: td.d};
                }

                var viewMode = 'days';

                var popup = el('div', 'fixed rtl min-w-[280px] rounded-lg shadow-lg p-3 ss01');
                popup.setAttribute('data-pdp-input-id', input.id || input.name || '');
                css(popup, {
                    backgroundColor: dk ? 'var(--tw-gray-200)' : '#fff',
                    border: '1px solid var(--tw-gray-300)',
                    color: 'var(--tw-gray-900)'
                });

                var header = el('div', 'flex items-center justify-between mb-3');

                function makeNavBtn(html) {
                    var b = el('button', 'w-8 h-8 rounded-md cursor-pointer text-lg flex items-center justify-center p-0');
                    b.type = 'button'; b.innerHTML = html;
                    css(b, {
                        backgroundColor: dk ? 'var(--tw-gray-300)' : '#fff',
                        border: '1px solid var(--tw-gray-300)',
                        color: 'var(--tw-gray-900)'
                    });
                    hoverable(b, dk ? 'var(--tw-gray-400)' : 'var(--tw-gray-200)');
                    return b;
                }
                var prevBtn = makeNavBtn('&#x2039;');
                var nextBtn = makeNavBtn('&#x203A;');

                var titleWrap = el('div', 'font-semibold flex items-center gap-1.5');
                css(titleWrap, { color: 'var(--tw-gray-900)' });

                var monthLbl = el('span', 'cursor-pointer px-1.5 py-0.5 rounded transition-colors');
                var yearLbl = el('span', 'cursor-pointer px-1.5 py-0.5 rounded transition-colors');
                hoverable(monthLbl, dk ? 'var(--tw-gray-400)' : 'var(--tw-gray-200)');
                hoverable(yearLbl, dk ? 'var(--tw-gray-400)' : 'var(--tw-gray-200)');

                titleWrap.appendChild(monthLbl);
                titleWrap.appendChild(document.createTextNode(' '));
                titleWrap.appendChild(yearLbl);
                header.appendChild(prevBtn);
                header.appendChild(titleWrap);
                header.appendChild(nextBtn);

                var dayNamesRow = el('div', 'grid grid-cols-7 gap-0.5 mb-2');
                DAYS.forEach(function(n) {
                    var c = el('span', 'text-center text-xs font-semibold');
                    css(c, { color: 'var(--tw-gray-500)' });
                    c.textContent = n;
                    dayNamesRow.appendChild(c);
                });

                var grid = el('div', 'grid grid-cols-7 gap-0.5 mb-3');
                var yearPage = cur.y - ((cur.y - 1) % 9);

                function setActive(lbl) {
                    css(lbl, { backgroundColor: 'var(--tw-primary)', color: '#fff' });
                    lbl._active = true;
                }
                function clearActive(lbl) {
                    css(lbl, { backgroundColor: '', color: '' });
                    lbl._active = false;
                }

                function isYearDis(y) {
                    if (minJ && y < minJ.y) return true;
                    if (maxJ && y > maxJ.y) return true;
                    return false;
                }
                function isMonthDis(m) {
                    if (minJ && cur.y === minJ.y && m < minJ.m) return true;
                    if (maxJ && cur.y === maxJ.y && m > maxJ.m) return true;
                    if (minJ && cur.y < minJ.y) return true;
                    if (maxJ && cur.y > maxJ.y) return true;
                    return false;
                }

                function showDays() {
                    viewMode = 'days';
                    dayNamesRow.style.display = '';
                    grid.className = 'grid grid-cols-7 gap-0.5 mb-3';
                    clearActive(yearLbl); clearActive(monthLbl);
                    renderDays();
                }

                function showYears() {
                    viewMode = 'years';
                    dayNamesRow.style.display = 'none';
                    grid.className = 'grid grid-cols-3 gap-2 min-h-[180px] mb-3';
                    setActive(yearLbl); clearActive(monthLbl);
                    yearPage = cur.y - ((cur.y - 1) % 9);
                    renderYears();
                }

                function showMonths() {
                    viewMode = 'months';
                    dayNamesRow.style.display = 'none';
                    grid.className = 'grid grid-cols-3 gap-2 min-h-[180px] mb-3';
                    setActive(monthLbl); clearActive(yearLbl);
                    renderMonths();
                }

                yearLbl.addEventListener('click', function(e) { e.stopPropagation(); viewMode === 'years' ? showDays() : showYears(); });
                monthLbl.addEventListener('click', function(e) { e.stopPropagation(); viewMode === 'months' ? showDays() : showMonths(); });

                function makeGridCell(text, state) {
                    var c = el('button', 'flex items-center justify-center border-none rounded-md cursor-pointer text-sm transition-colors');
                    c.type = 'button'; c.textContent = text;
                    if (state === 'selected') {
                        css(c, { backgroundColor: 'var(--tw-primary)', color: '#fff', fontWeight: '600' });
                    } else if (state === 'today') {
                        css(c, { backgroundColor: 'var(--tw-primary-light)', color: 'var(--tw-primary)', fontWeight: '600' });
                        hoverable(c, dk ? 'var(--tw-gray-400)' : 'var(--tw-gray-200)');
                    } else if (state === 'current') {
                        css(c, { border: '2px solid var(--tw-primary)', fontWeight: '600', color: 'var(--tw-gray-900)' });
                        hoverable(c, dk ? 'var(--tw-gray-400)' : 'var(--tw-gray-200)');
                    } else if (state === 'disabled') {
                        css(c, { opacity: '0.35', cursor: 'not-allowed', pointerEvents: 'none', color: 'var(--tw-gray-500)' });
                    } else {
                        css(c, { color: 'var(--tw-gray-900)' });
                        hoverable(c, dk ? 'var(--tw-gray-400)' : 'var(--tw-gray-200)');
                    }
                    return c;
                }

                function renderYears() {
                    monthLbl.textContent = MONTHS[cur.m - 1];
                    yearLbl.textContent = yearPage + ' \u2013 ' + (yearPage + 8);
                    grid.innerHTML = '';
                    var td = today();
                    for (var i = 0; i < 9; i++) {
                        var y = yearPage + i;
                        var dis = isYearDis(y);
                        var st = dis ? 'disabled' : (sel && y === sel.y) ? 'selected' : (y === td.y) ? 'today' : (y === cur.y) ? 'current' : '';
                        var c = makeGridCell(y, st);
                        css(c, { padding: '0.6rem 0.25rem' });
                        if (!dis) (function(yr) { c.addEventListener('click', function() { cur.y = yr; showDays(); }); })(y);
                        grid.appendChild(c);
                    }
                }

                function renderMonths() {
                    monthLbl.textContent = MONTHS[cur.m - 1];
                    yearLbl.textContent = cur.y;
                    grid.innerHTML = '';
                    var td = today();
                    for (var i = 1; i <= 12; i++) {
                        var dis = isMonthDis(i);
                        var st = dis ? 'disabled' : (sel && sel.y === cur.y && sel.m === i) ? 'selected' : (td.y === cur.y && td.m === i) ? 'today' : (i === cur.m) ? 'current' : '';
                        var c = makeGridCell(MONTHS[i - 1], st);
                        css(c, { padding: '0.5rem 0.25rem' });
                        if (!dis) (function(mo) { c.addEventListener('click', function() { cur.m = mo; showDays(); }); })(i);
                        grid.appendChild(c);
                    }
                }

                function renderDays() {
                    monthLbl.textContent = MONTHS[cur.m - 1];
                    yearLbl.textContent = cur.y;
                    grid.innerHTML = '';
                    var fw = wd1(cur.y, cur.m);
                    var len = mLen(cur.y, cur.m);
                    var td = today();
                    for (var i = 0; i < fw; i++) grid.appendChild(el('span', 'aspect-square'));
                    for (var d = 1; d <= len; d++) {
                        var j = {y: cur.y, m: cur.m, d: d};
                        var isSel = sel && sel.y === j.y && sel.m === j.m && sel.d === j.d;
                        var isTd = td.y === j.y && td.m === j.m && td.d === j.d;
                        var isFri = (fw + d - 1) % 7 === 6;
                        var isDis = (minJ && cmp(j, minJ) < 0) || (maxJ && cmp(j, maxJ) > 0);

                        var evts = hasHolidays ? getEvents(j.y, j.m, j.d, holidayTypes) : [];
                        var isHoliday = false;
                        for (var ei = 0; ei < evts.length; ei++) { if (evts[ei].h) { isHoliday = true; break; } }

                        var wrap = el('div', 'relative flex flex-col items-center');
                        var c = el('button', 'w-full aspect-square flex items-center justify-center border-none rounded-md cursor-pointer text-sm');
                        c.type = 'button'; c.textContent = d;

                        if (isDis) {
                            css(c, { opacity: '0.4', cursor: 'not-allowed', pointerEvents: 'none', color: 'var(--tw-gray-500)' });
                            c.disabled = true;
                        } else if (isSel) {
                            css(c, { backgroundColor: 'var(--tw-primary)', color: '#fff', fontWeight: '600' });
                        } else if (isTd) {
                            css(c, { backgroundColor: 'var(--tw-primary-light)', color: (isFri || isHoliday) ? 'var(--tw-danger)' : 'var(--tw-primary)', fontWeight: '600' });
                            hoverable(c, dk ? 'var(--tw-gray-400)' : 'var(--tw-gray-200)');
                        } else if (isFri || isHoliday) {
                            css(c, { color: 'var(--tw-danger)' });
                            hoverable(c, dk ? 'var(--tw-gray-400)' : 'var(--tw-gray-200)');
                        } else {
                            css(c, { color: 'var(--tw-gray-900)' });
                            hoverable(c, dk ? 'var(--tw-gray-400)' : 'var(--tw-gray-200)');
                        }

                        if (!isDis) {
                            (function(yr, mo, dy) {
                                c.addEventListener('click', function() {
                                    sel = {y: yr, m: mo, d: dy};
                                    if (autoClose) {
                                        commitSelection();
                                        closePopup();
                                    } else {
                                        if (onlyDate) commitSelection();
                                        renderDays();
                                        if (!onlyDate) renderTime();
                                    }
                                });
                            })(cur.y, cur.m, d);
                        }

                        wrap.appendChild(c);

                        if (evts.length > 0 && !isDis) {
                            var dot = el('span', 'absolute rounded-full');
                            css(dot, { width: '4px', height: '4px', bottom: '1px', backgroundColor: dotColor(evts) });
                            wrap.appendChild(dot);

                            (function(ev, w) {
                                w.addEventListener('mouseenter', function() {
                                    var titles = [];
                                    for (var k = 0; k < ev.length; k++) titles.push(ev[k].t);
                                    showTooltip(w, titles, dk);
                                });
                                w.addEventListener('mouseleave', hideTooltip);
                            })(evts, wrap);
                        }

                        grid.appendChild(wrap);
                    }
                }

                /* ── Time enforcement ── */
                function toSec(h, m, s) { return h * 3600 + m * 60 + s; }
                function fromSec(t) { return {h: ~~(t/3600), m: ~~((t%3600)/60), s: t%60}; }

                function getTimeFloor() {
                    if (!minJ || !sel) return null;
                    if (cmp(sel, minJ) < 0) return null;
                    if (cmp(sel, minJ) > 0) return null;
                    var td = today();
                    if (cmp(minJ, td) === 0) {
                        var now = new Date();
                        return toSec(now.getHours(), now.getMinutes(), now.getSeconds());
                    }
                    var mt = parseTime(minStr);
                    return (mt.h || mt.m || mt.s) ? toSec(mt.h, mt.m, mt.s) : null;
                }

                function getTimeCeil() {
                    if (!maxJ || !sel || cmp(sel, maxJ) !== 0) return null;
                    var mt = parseTime(maxStr);
                    return (mt.h || mt.m || mt.s) ? toSec(mt.h, mt.m, mt.s) : null;
                }

                function enforceTimeBounds() {
                    if (!timeRow || onlyDate || !sel) return;
                    time.h = Math.max(0, Math.min(23, parseInt(timeRow._h.value, 10) || 0));
                    time.m = Math.max(0, Math.min(59, parseInt(timeRow._m.value, 10) || 0));
                    time.s = Math.max(0, Math.min(59, parseInt(timeRow._s.value, 10) || 0));

                    var cur = toSec(time.h, time.m, time.s);
                    var floor = getTimeFloor();
                    var ceil = getTimeCeil();
                    if (floor !== null && cur < floor) cur = floor;
                    if (ceil !== null && cur > ceil) cur = ceil;

                    var r = fromSec(cur);
                    time.h = r.h; time.m = r.m; time.s = r.s;
                    timeRow._h.value = pad(time.h);
                    timeRow._m.value = pad(time.m);
                    timeRow._s.value = pad(time.s);
                }

                /* ── Time row ── */
                var timeRow = null;
                function renderTime() {
                    if (onlyDate) return;
                    if (!timeRow) {
                        timeRow = el('div', 'flex items-center justify-center gap-1.5 my-3 pt-3 flex-wrap');
                        css(timeRow, { borderTop: '1px solid var(--tw-gray-300)', direction: 'ltr' });

                        function makeTimeInput(val) {
                            var inp = el('input', 'w-10 text-center rounded-md py-1 text-sm outline-none ss01');
                            inp.type = 'text'; inp.maxLength = 2; inp.inputMode = 'numeric'; inp.value = val;
                            css(inp, {
                                backgroundColor: dk ? 'var(--tw-gray-300)' : '#fff',
                                border: '1px solid var(--tw-gray-300)',
                                color: 'var(--tw-gray-900)'
                            });
                            inp.addEventListener('focus', function() { inp.style.borderColor = 'var(--tw-primary)'; inp.style.boxShadow = '0 0 0 2px var(--tw-primary-clarity)'; });
                            inp.addEventListener('blur', function() { inp.style.borderColor = 'var(--tw-gray-300)'; inp.style.boxShadow = ''; });
                            return inp;
                        }

                        var sep = function() { var s = el('span', ''); css(s, { color: 'var(--tw-gray-500)' }); s.textContent = ':'; return s; };
                        var hI = makeTimeInput(pad(time.h));
                        var mI = makeTimeInput(pad(time.m));
                        var sI = makeTimeInput(pad(time.s));

                        timeRow.appendChild(hI); timeRow.appendChild(sep());
                        timeRow.appendChild(mI); timeRow.appendChild(sep());
                        timeRow.appendChild(sI);
                        timeRow._h = hI; timeRow._m = mI; timeRow._s = sI;

                        function bindTimeInput(inp, field) {
                            inp.addEventListener('input', function() { this.value = this.value.replace(/\D/g, '').slice(0, 2); });
                            inp.addEventListener('blur', function() {
                                var v = parseInt(this.value, 10) || 0;
                                var absMax = (field === 'h') ? 23 : 59;
                                this.value = pad(Math.max(0, Math.min(absMax, v)));
                                enforceTimeBounds();
                            });
                            inp.addEventListener('keydown', function(e) { if (e.key === 'Enter') this.blur(); });
                            inp.addEventListener('wheel', function(e) {
                                e.preventDefault();
                                var v = parseInt(this.value, 10) || 0;
                                var absMax = (field === 'h') ? 23 : 59;
                                v += (e.deltaY < 0) ? 1 : -1;
                                if (v > absMax) v = 0;
                                if (v < 0) v = absMax;
                                this.value = pad(v);
                                enforceTimeBounds();
                            });
                        }
                        bindTimeInput(hI, 'h');
                        bindTimeInput(mI, 'm');
                        bindTimeInput(sI, 's');

                        popup.insertBefore(timeRow, footer);
                    } else {
                        timeRow._h.value = pad(time.h);
                        timeRow._m.value = pad(time.m);
                        timeRow._s.value = pad(time.s);
                    }
                    enforceTimeBounds();
                }

                function readTime() {
                    if (!timeRow) return;
                    time.h = parseInt(timeRow._h.value, 10) || 0;
                    time.m = parseInt(timeRow._m.value, 10) || 0;
                    time.s = parseInt(timeRow._s.value, 10) || 0;
                }

                function clampTime() {
                    if (!timeRow || onlyDate || !sel) return;
                    readTime();
                    enforceTimeBounds();
                }

                function commitSelection() {
                    if (!sel) return;
                    if (onlyDate) {
                        input.value = fmt(sel, null);
                    } else {
                        readTime();
                        clampTime();
                        input.value = fmt(sel, time);
                    }
                    input.dispatchEvent(new Event('input', {bubbles:true}));
                    input.dispatchEvent(new Event('change', {bubbles:true}));
                }

                prevBtn.addEventListener('click', function() {
                    if (viewMode === 'years') { yearPage -= 9; renderYears(); }
                    else if (viewMode === 'months') { cur.y--; renderMonths(); }
                    else { if (cur.m === 1) { cur.m = 12; cur.y--; } else cur.m--; renderDays(); }
                });
                nextBtn.addEventListener('click', function() {
                    if (viewMode === 'years') { yearPage += 9; renderYears(); }
                    else if (viewMode === 'months') { cur.y++; renderMonths(); }
                    else { if (cur.m === 12) { cur.m = 1; cur.y++; } else cur.m++; renderDays(); }
                });

                var footer = el('div', 'flex gap-2 justify-center flex-wrap pt-2');
                css(footer, { borderTop: '1px solid var(--tw-gray-300)' });

                function makeFooterBtn(text, isPrimary) {
                    var b = el('button', 'py-1.5 px-3 rounded-md cursor-pointer text-sm ss01');
                    b.type = 'button'; b.textContent = text;
                    if (isPrimary) {
                        css(b, { backgroundColor: 'var(--tw-primary)', color: '#fff', border: '1px solid var(--tw-primary)' });
                        b.addEventListener('mouseenter', function() { b.style.backgroundColor = 'var(--tw-primary-active)'; });
                        b.addEventListener('mouseleave', function() { b.style.backgroundColor = 'var(--tw-primary)'; });
                    } else {
                        css(b, {
                            backgroundColor: dk ? 'var(--tw-gray-300)' : 'var(--tw-gray-100)',
                            border: '1px solid var(--tw-gray-300)',
                            color: 'var(--tw-gray-900)'
                        });
                        hoverable(b, dk ? 'var(--tw-gray-400)' : 'var(--tw-gray-200)');
                    }
                    return b;
                }

                var todayBtn = makeFooterBtn('امروز', true);
                var clearBtn = makeFooterBtn('پاک کردن', false);
                var closeBtn = makeFooterBtn('بستن', false);

                todayBtn.addEventListener('click', function() {
                    var t = today(), now = new Date();
                    cur = {y: t.y, m: t.m, d: t.d};
                    sel = {y: t.y, m: t.m, d: t.d};
                    time = {h: now.getHours(), m: now.getMinutes(), s: now.getSeconds()};
                    if (onlyDate) { commitSelection(); closePopup(); }
                    else { showDays(); renderTime(); }
                });
                clearBtn.addEventListener('click', function() { input.value = ''; input.dispatchEvent(new Event('input', {bubbles:true})); input.dispatchEvent(new Event('change', {bubbles:true})); closePopup(); });
                closeBtn.addEventListener('click', function() {
                    if (sel) commitSelection();
                    closePopup();
                });

                footer.appendChild(todayBtn);
                footer.appendChild(clearBtn);
                footer.appendChild(closeBtn);

                popup.appendChild(header);
                popup.appendChild(dayNamesRow);
                popup.appendChild(grid);
                popup.appendChild(footer);

                function closePopup() { hideTooltip(); document.body.removeChild(popup); document.removeEventListener('mousedown', onOutside); }
                function onOutside(e) {
                    if (popup.contains(e.target) || e.target === input) return;
                    if (sel) commitSelection();
                    closePopup();
                }

                renderDays();
                if (!onlyDate) renderTime();

                document.body.appendChild(popup);
                var rect = input.getBoundingClientRect();
                popup.style.left = rect.left + 'px';
                popup.style.top = (rect.bottom + 4) + 'px';
                popup.style.zIndex = '9999';
                if (window.innerHeight - rect.bottom < 360 && rect.top > 360) {
                    popup.style.top = (rect.top - popup.offsetHeight - 4) + 'px';
                }
                document.addEventListener('mousedown', onOutside, false);
                return popup;
            }

            function init() {
                document.querySelectorAll('input[data-pdp]').forEach(function(input) {
                    if (input.dataset.pdpInited) return;
                    input.dataset.pdpInited = '1';
                    input.addEventListener('click', function(e) {
                        e.preventDefault();
                        if (input.disabled) return;
                        var ex = document.querySelector('[data-pdp-input-id]');
                        if (ex && ex.parentNode) ex.parentNode.removeChild(ex);
                        createPopup(input);
                    });
                });
            }

            if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
            else init();
            document.addEventListener('livewire:navigated', init);
        })();
    </script>
@endpushonce
