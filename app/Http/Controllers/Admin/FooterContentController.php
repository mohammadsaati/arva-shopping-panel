<?php

namespace App\Http\Controllers\Admin;

use App\DTO\FooterEntryData;
use App\Enums\GlobalStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\SaveFooterEntryRequest;
use App\Services\Contracts\FooterContentServiceInterface;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class FooterContentController extends Controller
{
    public function __construct(private readonly FooterContentServiceInterface $footer)
    {
    }

    public function index(string $kind): View
    {
        abort_unless(isset($this->footer->kinds()[$kind]), 404);

        return view('admin.settings.footer.index', ['kind' => $kind, 'kinds' => $this->footer->kinds()]);
    }

    public function create(string $kind): View
    {
        abort_unless(isset($this->footer->kinds()[$kind]), 404);

        return $this->form($kind, null);
    }

    public function store(SaveFooterEntryRequest $request, string $kind): RedirectResponse
    {
        $this->footer->save($this->data($request, $kind));

        return redirect()->route('admin.footer.index', $kind)->with('success', 'آیتم فوتر افزوده شد.');
    }

    public function edit(string $kind, string $entryId): View
    {
        abort_unless(isset($this->footer->kinds()[$kind]), 404);

        return $this->form($kind, $this->footer->entry($kind, (int) $entryId));
    }

    public function update(SaveFooterEntryRequest $request, string $kind, string $entryId): RedirectResponse
    {
        $this->footer->save($this->data($request, $kind), (int) $entryId);

        return redirect()->route('admin.footer.index', $kind)->with('success', 'آیتم فوتر ویرایش شد.');
    }

    public function destroy(string $kind, string $entryId): RedirectResponse
    {
        $this->footer->delete($kind, (int) $entryId);

        return redirect()->route('admin.footer.index', $kind)->with('success', 'آیتم فوتر حذف شد.');
    }

    private function form(string $kind, ?object $entry): View
    {
        return view('admin.settings.footer.form', [
            'kind' => $kind,
            'kindTitle' => $this->footer->kinds()[$kind],
            'entry' => $entry,
            'groupOptions' => $this->footer->groupOptions(),
            'statusOptions' => collect(GlobalStatusEnum::toKeyValueType())->mapWithKeys(fn ($item) => [$item->value => $item->key])->all(),
        ]);
    }

    private function data(SaveFooterEntryRequest $request, string $kind): FooterEntryData
    {
        return new FooterEntryData(
            kind: $kind,
            title: $request->filled('title') ? $request->string('title')->value() : null,
            label: $request->filled('label') ? $request->string('label')->value() : null,
            url: $request->filled('url') ? $request->string('url')->value() : null,
            icon: $request->filled('icon') ? $request->string('icon')->value() : null,
            groupId: $request->filled('footer_link_group_id') ? $request->integer('footer_link_group_id') : null,
            opensInNewTab: $request->boolean('opens_in_new_tab'),
            sortOrder: $request->integer('sort_order'),
            status: $request->enum('status', GlobalStatusEnum::class),
            image: $request->file('image'),
            removeImage: $request->boolean('remove_image'),
            providedFields: $request->keys(),
        );
    }
}
