<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BranchSwitcherController extends Controller
{
    public function switch(Request $request): RedirectResponse
    {
        $request->validate(['branch_id' => 'required|integer']);

        $user = $request->user();
        $branchId = (int) $request->input('branch_id');

        $branch = $user->branches()->where('branches.id', $branchId)->first();
        if (! $branch && $user->isSuperAdmin()) {
            $branch = Branch::find($branchId);
        }
        if (! $branch) {
            abort(404);
        }

        $request->session()->put('active_branch_id', $branch->id);
        $request->session()->put('active_branch_name', $branch->name);

        return back()->with('success', __('coffee.switch_branch').': '.$branch->name);
    }
}
