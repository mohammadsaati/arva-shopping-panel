<?php

namespace App\Http\Controllers;

use App\DTO\user\UserPasswordChangeData;
use App\DTO\user\UserProfileUpdateData;
use App\DTO\user\UserUpdateData;
use App\Http\Requests\ChangeUserPasswordRequest;
use App\Http\Requests\UpdateUserProfileRequest;
use App\Services\Contracts\UserServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Contracts\View\View;

class AccountController extends Controller
{
    public function __construct(private readonly UserServiceInterface $users)
    {
    }

    public function show(Request $request): JsonResponse|View
    {
        if (! $request->expectsJson()) {
            return view('account.show', [
                'user' => $this->users->profile($request->user()),
            ]);
        }

        return response()->json($this->users->profile($request->user()));
    }

    public function update(UpdateUserProfileRequest $request): JsonResponse|RedirectResponse
    {
        $user = $this->users->updateProfile(
            $request->user(),
            new UserUpdateData(
                firstName: $request->has('first_name') ? $request->string('first_name')->value() : null,
                lastName: $request->has('last_name') ? $request->string('last_name')->value() : null,
                phone: $request->has('phone') ? $request->string('phone')->value() : null,
                recoveryEmail: $request->has('recovery_email')
                    ? ($request->filled('recovery_email') ? $request->string('recovery_email')->value() : null)
                    : null,
            ),
            new UserProfileUpdateData(
                bankCard: $request->filled('bank_card') ? $request->string('bank_card')->value() : null,
                bankCardProvided: $request->has('bank_card'),
                job: $request->filled('job') ? $request->string('job')->value() : null,
                jobProvided: $request->has('job'),
                cityId: $request->filled('city_id') ? $request->string('city_id')->value() : null,
                cityIdProvided: $request->has('city_id'),
            ),
        );

        if (! $request->expectsJson()) {
            return redirect()->route('account.show')->with('success', 'اطلاعات حساب کاربری ذخیره شد.');
        }

        return response()->json($user);
    }

    public function changePassword(ChangeUserPasswordRequest $request): Response|RedirectResponse
    {
        $this->users->changePassword(
            $request->user(),
            new UserPasswordChangeData(
                currentPassword: $request->string('current_password')->value(),
                newPassword: $request->string('password')->value(),
            ),
        );

        if (! $request->expectsJson()) {
            return redirect()->route('account.show')->with('success', 'رمز عبور با موفقیت تغییر کرد.');
        }

        return response()->noContent();
    }
}
