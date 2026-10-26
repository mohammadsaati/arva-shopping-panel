<?php

namespace App\Http\Controllers\Admin;

use App\DTO\Admin\AdminLoginData;
use App\Http\Controllers\Controller;
use App\Http\Requests\AdminLoginRequest;
use App\Services\Contracts\AdminAuthServiceInterface;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AdminAuthController extends Controller
{
    public function __construct(private readonly AdminAuthServiceInterface $auth)
    {
    }

    public function loginForm(): View
    {
        return view('admin.auth.login');
    }

    public function login(AdminLoginRequest $request): RedirectResponse
    {
        if (! $this->auth->attempt(new AdminLoginData(
            email: $request->string('email')->value(),
            password: $request->string('password')->value(),
        ))) {
            return back()
                ->withErrors(['auth' => 'ایمیل یا رمز عبور واردشده معتبر نیست.'])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->intended(route('admin.dashboard.index'));
    }

    public function logout(Request $request): RedirectResponse
    {
        $this->auth->logout();
        $request->session()->regenerate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
