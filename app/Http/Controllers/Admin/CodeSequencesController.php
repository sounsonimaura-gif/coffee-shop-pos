<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseReadOnlyController;
use App\Models\CodeSequence;
use Illuminate\Database\Eloquent\Builder;
use Yajra\DataTables\DataTableAbstract;

class CodeSequencesController extends BaseReadOnlyController
{
    protected string $model = CodeSequence::class;

    protected string $viewNamespace = 'CodeSequences';

    protected string $permissionPrefix = 'code_sequences';

    protected string $routePrefix = 'admin.code-sequences';

    protected string $titleKey = 'coffee.code_sequences';

    protected function columns(): array
    {
        return [
            ['data' => 'module', 'title' => 'coffee.module'],
            ['data' => 'prefix', 'title' => 'coffee.prefix'],
            ['data' => 'next_number', 'title' => 'coffee.next_value'],
            ['data' => 'padding', 'title' => 'coffee.padding'],
            ['data' => 'date_format', 'title' => 'coffee.format'],
        ];
    }

    protected function baseQuery(): Builder
    {
        $q = CodeSequence::query();
        $companyId = request()->user()?->company_id;
        if ($companyId) {
            $q->where('company_id', $companyId);
        }

        return $q;
    }

    protected function transformDataTable(DataTableAbstract $dt): DataTableAbstract
    {
        return $dt;
    }
}
