<x-panel-layout title="مشخصات {{ $category->name }}" sub-title="گروه‌ها و فیلدهای مشخصات این دسته‌بندی را تعریف کنید.">
    @if (session('success'))
        <x-alert type="success" class="mb-5">{{ session('success') }}</x-alert>
    @endif

    <div class="mb-5 flex flex-wrap gap-2">
        <x-button href="{{ route('admin.categories.show', $category->id) }}" variant="light" icon="ki-filled ki-arrow-right">بازگشت به دسته‌بندی</x-button>
        @if ($groups->isNotEmpty())
            <x-button href="{{ route('admin.categories.specifications.fields.create', $category->id) }}" icon="ki-filled ki-plus">افزودن مشخصه</x-button>
        @endif
    </div>

    <x-card.form
        :action="route('admin.categories.specifications.groups.store', $category->id)"
        :submit="['title' => 'افزودن گروه', 'type' => 'primary']"
        class="admin-editor-form mb-6"
    >
        <x-admin.form-section title="افزودن گروه مشخصات" description="مشخصه‌های هم‌موضوع را زیر یک عنوان جمع کنید." icon="ki-filled ki-category" :two-columns="true">
            <x-form.input name="name" label="نام گروه" placeholder="مثلاً مشخصات کلی" required />
            <x-form.input name="sort_order" label="ترتیب نمایش" type="number" min="0" value="0" required />
        </x-admin.form-section>
    </x-card.form>

    @forelse ($groups as $group)
        <x-card.form
            :action="route('admin.categories.specifications.groups.update', [$category->id, $group->id])"
            method="PUT"
            title="گروه {{ $group->name }}"
            :submit="['title' => 'ذخیره گروه', 'type' => 'light']"
            class="mb-5"
        >
            <div class="grid gap-4 sm:grid-cols-2">
                <x-form.input name="name" label="نام گروه" :value="$group->name" required />
                <x-form.input name="sort_order" label="ترتیب نمایش" type="number" min="0" :value="$group->sort_order" required />
            </div>
        </x-card.form>

        <x-datatable :title="'مشخصه‌های ' . $group->name" class="mb-6">
            <x-slot:head>
                <x-datatable-heading>نام</x-datatable-heading>
                <x-datatable-heading>نوع</x-datatable-heading>
                <x-datatable-heading>نمایش</x-datatable-heading>
                <x-datatable-heading>ترتیب</x-datatable-heading>
                <x-datatable-heading>عملیات</x-datatable-heading>
            </x-slot:head>
            @forelse ($group->specifications as $specification)
                <tr>
                    <td>{{ $specification->name }} <small class="text-gray-500" dir="ltr">{{ $specification->key }}</small></td>
                    <td>{{ $specification->type->trans() }}</td>
                    <td>{{ $specification->is_highlighted ? 'ویژگی برجسته' : 'مشخصات کامل' }}</td>
                    <td>{{ $specification->sort_order }}</td>
                    <td><a class="text-primary hover:underline" href="{{ route('admin.categories.specifications.fields.edit', [$category->id, $specification->id]) }}">ویرایش</a></td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-gray-500">هنوز مشخصه‌ای تعریف نشده است.</td></tr>
            @endforelse
        </x-datatable>
        @if ($group->specifications->isEmpty())
            <x-card.form
                :action="route('admin.categories.specifications.groups.delete', [$category->id, $group->id])"
                method="DELETE"
                :submit="['title' => 'حذف گروه خالی', 'type' => 'light']"
                class="mb-6"
            ><p class="text-sm text-gray-500">این گروه هنوز مشخصه‌ای ندارد.</p></x-card.form>
        @endif
    @empty
        <x-alert type="info">ابتدا یک گروه بسازید، سپس مشخصه‌های آن را اضافه کنید.</x-alert>
    @endforelse
</x-panel-layout>
