<footer class="panel-footer">
    <div class="container-fixed flex flex-col sm:flex-row items-center justify-between gap-2 py-5">
        <span>© {{ jalali_date(now(), 'Y') }} {{ site_name() }}</span>
        <span>{{ request()->is('owner*') ? 'مدیریت سالن' : 'مدیریت سامانه' }}</span>
    </div>
</footer>
