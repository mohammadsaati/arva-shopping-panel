<?php

namespace Tests\Unit\Http;

use App\Http\Requests\TelegramWebhookRequest;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class TelegramWebhookRequestAuthorizeTest extends TestCase
{
    public function test_authorize_allows_local_when_secret_missing(): void
    {
        Config::set('services.telegram.webhook_secret', '');
        $this->app->detectEnvironment(fn () => 'local');

        $request = TelegramWebhookRequest::create('/telegram/webhook', 'POST');
        $this->assertTrue($request->authorize());
    }

    public function test_authorize_rejects_when_secret_missing_outside_local(): void
    {
        Config::set('services.telegram.webhook_secret', '');
        $this->app->detectEnvironment(fn () => 'production');

        $request = TelegramWebhookRequest::create('/telegram/webhook', 'POST');
        $this->assertFalse($request->authorize());
    }
}
