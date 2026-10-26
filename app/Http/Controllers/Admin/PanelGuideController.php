<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

class PanelGuideController extends Controller
{
    public function index(): View
    {
        return view('admin.help.index');
    }

    public function icons(): View
    {
        return view('admin.help.icons', [
            'icons' => config('keenicons.filled', []),
        ]);
    }
}
