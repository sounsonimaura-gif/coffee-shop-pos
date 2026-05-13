<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseCrudController;
use App\Models\MembershipLevel;
use Illuminate\Http\Request;

class MembershipLevelsController extends BaseCrudController
{
    protected string $model = MembershipLevel::class;

    protected string $viewNamespace = 'MembershipLevels';

    protected string $permissionPrefix = 'membership_levels';

    protected string $routePrefix = 'admin.membership-levels';

    protected string $titleKey = 'coffee.membership_levels';

    protected function columns(): array
    {
        return [
            0 => [
                'data' => 'name',
                'title' => 'coffee.name',
            ],
            1 => [
                'data' => 'min_points',
                'title' => 'coffee.quantity',
            ],
            2 => [
                'data' => 'discount_percent',
                'title' => 'coffee.discount',
            ],
            3 => [
                'data' => 'is_active',
                'title' => 'coffee.is_active',
            ],
        ];
    }

    protected function rules(Request $request, $model = null): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'min_spend' => ['nullable', 'numeric'],
            'min_points' => ['nullable', 'numeric'],
            'discount_percent' => ['nullable', 'numeric'],
            'point_multiplier' => ['nullable', 'numeric'],
            'sort_order' => ['nullable', 'numeric'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    protected function formProps(?object $model = null): array
    {
        return [
        ];
    }
}
