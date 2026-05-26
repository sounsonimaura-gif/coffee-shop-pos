<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseCrudController;
use App\Models\Branch;
use App\Models\Staff;
use App\Models\StaffSchedule;
use Illuminate\Http\Request;

class StaffSchedulesController extends BaseCrudController
{
    protected string $model = StaffSchedule::class;

    protected string $viewNamespace = 'StaffSchedules';

    protected string $permissionPrefix = 'staff_schedules';

    protected string $routePrefix = 'admin.staff-schedules';

    protected string $titleKey = 'coffee.staff_schedules';

    protected function columns(): array
    {
        return [
            ['data' => 'staff_id', 'title' => 'coffee.staff'],
            ['data' => 'day_of_week', 'title' => 'coffee.day'],
            ['data' => 'start_time', 'title' => 'coffee.start_time'],
            ['data' => 'end_time', 'title' => 'coffee.end_time'],
            ['data' => 'is_day_off', 'title' => 'coffee.day_off'],
        ];
    }

    protected function rules(Request $request, $model = null): array
    {
        return [
            'staff_id' => ['required', 'integer', 'exists:staff,id'],
            'branch_id' => ['nullable', 'integer', 'exists:branches,id'],
            'day_of_week' => ['required', 'integer', 'between:0,6'],
            'start_time' => ['nullable', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i'],
            'is_day_off' => ['nullable', 'boolean'],
        ];
    }

    protected function formProps(?object $model = null): array
    {
        $companyId = request()->user()?->company_id;

        return [
            'staff' => Staff::query()->where('company_id', $companyId)->select('id', 'name', 'staff_code')->orderBy('name')->get(),
            'branches' => Branch::query()->where('company_id', $companyId)->select('id', 'name')->orderBy('name')->get(),
            'days' => [
                ['value' => 0, 'text' => 'Sunday'],
                ['value' => 1, 'text' => 'Monday'],
                ['value' => 2, 'text' => 'Tuesday'],
                ['value' => 3, 'text' => 'Wednesday'],
                ['value' => 4, 'text' => 'Thursday'],
                ['value' => 5, 'text' => 'Friday'],
                ['value' => 6, 'text' => 'Saturday'],
            ],
        ];
    }
}
