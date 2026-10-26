<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Services\Contracts\AdminTelegramConnectionServiceInterface;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AdminTelegramConnectionController extends Controller
{
    public function __construct(private readonly AdminTelegramConnectionServiceInterface $connections)
    {
    }

    public function index(Request $request): View
    {
        return view('admin.telegram.index', [
            'connection' => $this->connections->overview($this->admin($request)),
        ]);
    }

    public function registerWebhook(): RedirectResponse
    {
        $result = $this->connections->registerWebhook();

        return redirect()->route('admin.telegram.index')
            ->with('telegram_webhook_result', $result->toArray());
    }

    public function createLink(Request $request): RedirectResponse
    {
        $link = $this->connections->createLink($this->admin($request));

        if ($link === null) {
            return redirect()->route('admin.telegram.index')
                ->with('error', 'ابتدا تنظیمات ربات تلگرام را روی سرور کامل کنید.');
        }

        return redirect()->route('admin.telegram.index')->with('telegram_link', $link);
    }

    public function sendTest(Request $request): RedirectResponse
    {
        $sent = $this->connections->sendTest($this->admin($request));

        return redirect()->route('admin.telegram.index')->with(
            $sent ? 'success' : 'error',
            $sent ? 'پیام آزمایشی به تلگرام شما ارسال شد.' : 'ارسال پیام آزمایشی انجام نشد. اتصال و تنظیمات ربات را بررسی کنید.',
        );
    }

    public function disconnect(Request $request): RedirectResponse
    {
        $this->connections->disconnect($this->admin($request));

        return redirect()->route('admin.telegram.index')->with('success', 'اتصال تلگرام شما قطع شد.');
    }

    private function admin(Request $request): Admin
    {
        $admin = $request->user('admin');
        abort_unless($admin instanceof Admin, 403);

        return $admin;
    }
}
