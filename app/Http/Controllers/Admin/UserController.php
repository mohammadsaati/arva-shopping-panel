<?php

namespace App\Http\Controllers\Admin;

use App\DTO\user\UserCreateData;
use App\DTO\user\UserAdminUpdateData;
use App\DTO\user\UserProfileUpdateData;
use App\Enums\GlobalStatusEnum;
use App\Enums\UserTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAdminUserRequest;
use App\Http\Requests\UpdateAdminUserRequest;
use App\Services\Contracts\UserServiceInterface;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class UserController extends Controller
{
    public function __construct(private readonly UserServiceInterface $users)
    {
    }

    public function index(): View
    {
        return view('admin.users.index');
    }

    public function show(string $userId): View
    {
        $user = $this->users->details((int)$userId);

        return view('admin.users.show', ['user' => $user]);
    }

    public function orders(string $userId): View
    {
        return view('admin.users.orders', [
            'user' => $this->users->details((int) $userId),
        ]);
    }

    public function edit(string $userId): View
    {
        return view('admin.users.edit', [
            'user' => $this->users->details((int) $userId),
            'userTypes' => collect(UserTypeEnum::toKeyValueType())
                ->mapWithKeys(fn ($item) => [$item->value => $item->key])
                ->all(),
            'statusOptions' => collect(GlobalStatusEnum::toKeyValueType())
                ->mapWithKeys(fn ($item) => [$item->value => $item->key])
                ->all(),
        ]);
    }

    public function update(UpdateAdminUserRequest $request, string $userId): RedirectResponse
    {
        $user = $this->users->details((int) $userId);
        $user = $this->users->updateAdminDetails($user, new UserAdminUpdateData(
            firstName: $request->string('first_name')->value(),
            lastName: $request->string('last_name')->value(),
            phone: $request->string('phone')->value(),
            type: $request->enum('type', UserTypeEnum::class),
            status: $request->enum('status', GlobalStatusEnum::class),
            profile: new UserProfileUpdateData(
                bankCard: $request->filled('bank_card') ? $request->string('bank_card')->value() : null,
                bankCardProvided: $request->has('bank_card'),
                job: $request->filled('job') ? $request->string('job')->value() : null,
                jobProvided: $request->has('job'),
                cityId: $request->filled('city_id') ? $request->string('city_id')->value() : null,
                cityIdProvided: $request->has('city_id'),
            ),
        ));

        return redirect()
            ->route('admin.users.show', $user->id)
            ->with('success', 'کاربر با موفقیت ویرایش شد.');
    }

    public function create(): View
    {
        return view('admin.users.create', [
            'userTypes' => collect(UserTypeEnum::toKeyValueType())
                ->mapWithKeys(fn ($item) => [$item->value => $item->key])
                ->all(),
        ]);
    }

    public function store(StoreAdminUserRequest $request): RedirectResponse
    {
        $this->users->create(new UserCreateData(
            firstName: $request->string('first_name')->value(),
            lastName: $request->string('last_name')->value(),
            phone: $request->string('phone')->value(),
            password: $request->string('password')->value(),
            type: $request->enum('type', UserTypeEnum::class),
        ));

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'کاربر جدید با موفقیت ایجاد شد.');
    }
}
