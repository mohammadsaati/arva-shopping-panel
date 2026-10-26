<?php

namespace App\Http\Controllers;

use App\DTO\user\UserCreateData;
use App\Enums\UserTypeEnum;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\StorefrontLoginRequest;
use App\Http\Requests\StorefrontRegisterRequest;
use App\Services\Contracts\UserAuthServiceInterface;
use App\Services\Contracts\UserPasswordResetServiceInterface;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class UserAuthController extends Controller
{
    public function __construct(
        private readonly UserAuthServiceInterface $auth,
        private readonly UserPasswordResetServiceInterface $passwordReset,
    ) {}

    public function loginForm(): View
    {
        return view('auth.login');
    }

    public function login(StorefrontLoginRequest $request): RedirectResponse
    {
        if (! $this->auth->attempt(
            phone: $request->string('phone')->value(),
            password: $request->string('password')->value(),
        )) {
            return back()
                ->withErrors(['auth' => 'شماره موبایل یا رمز عبور درست نیست.'])
                ->onlyInput('phone');
        }

        $request->session()->regenerate();

        return redirect()->intended(route('home'));
    }

    public function registerForm(): View
    {
        return view('auth.register');
    }

    public function register(StorefrontRegisterRequest $request): RedirectResponse
    {
        $this->auth->register(new UserCreateData(
            firstName: $request->string('first_name')->value(),
            lastName: $request->string('last_name')->value(),
            phone: $request->string('phone')->value(),
            password: $request->string('password')->value(),
            type: UserTypeEnum::NORMAL,
        ));

        $request->session()->regenerate();

        return redirect()->intended(route('home'));
    }

    public function logout(Request $request): RedirectResponse
    {
        $this->auth->logout();
        $request->session()->regenerate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    public function forgotPasswordForm(): View
    {
        return view('auth.forgot-password');
    }

    public function sendPasswordResetLink(ForgotPasswordRequest $request): RedirectResponse
    {
        $this->passwordReset->sendResetLink($request->string('phone')->value());

        return back()->with('success', 'اگر حسابی با این شماره وجود داشته باشد و ایمیل بازیابی ثبت شده باشد، لینک بازیابی ارسال شد.');
    }

    public function resetPasswordForm(Request $request, string $token): View
    {
        return view('auth.reset-password', [
            'token' => $token,
            'phone' => $request->string('phone')->value(),
        ]);
    }

    public function resetPassword(ResetPasswordRequest $request): RedirectResponse
    {
        $this->passwordReset->resetPassword(
            phone: $request->string('phone')->value(),
            token: $request->string('token')->value(),
            password: $request->string('password')->value(),
        );

        return redirect()
            ->route('login')
            ->with('success', 'رمز عبور با موفقیت تغییر کرد. اکنون می‌توانید وارد شوید.');
    }
}
