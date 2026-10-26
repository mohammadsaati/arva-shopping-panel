@php($shareUrl = $shareUrl ?? url()->current())
@php($shareText = $shareText ?? '')
<div class="blog-page__share" data-blog-share>
    <span>اشتراک‌گذاری</span>
    <div class="blog-page__share-actions">
        <a href="https://t.me/share/url?url={{ urlencode($shareUrl) }}&text={{ urlencode($shareText) }}" target="_blank" rel="noopener noreferrer">تلگرام</a>
        <a href="https://wa.me/?text={{ urlencode(trim($shareText.' '.$shareUrl)) }}" target="_blank" rel="noopener noreferrer">واتساپ</a>
        <button type="button" data-copy-link="{{ $shareUrl }}">کپی لینک</button>
    </div>
</div>
