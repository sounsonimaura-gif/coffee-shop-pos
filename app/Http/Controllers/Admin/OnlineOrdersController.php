<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseCrudController;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\OnlineOrder;
use Illuminate\Http\Request;

class OnlineOrdersController extends BaseCrudController
{
    protected string $model = OnlineOrder::class;

    protected string $viewNamespace = 'OnlineOrders';

    protected string $permissionPrefix = 'online_orders';

    protected string $routePrefix = 'admin.online-orders';

    protected string $titleKey = 'coffee.online_orders';

    protected function columns(): array
    {
        return [
            ['data' => 'online_order_no', 'title' => 'coffee.code'],
            ['data' => 'customer_name', 'title' => 'coffee.customer'],
            ['data' => 'customer_phone', 'title' => 'coffee.phone'],
            ['data' => 'grand_total', 'title' => 'coffee.grand_total'],
            ['data' => 'payment_method', 'title' => 'coffee.payment_method'],
            ['data' => 'status', 'title' => 'coffee.status'],
        ];
    }

    protected function rules(Request $request, $model = null): array
    {
        return [
            'branch_id' => ['nullable', 'integer', 'exists:branches,id'],
            'customer_id' => ['nullable', 'integer', 'exists:customers,id'],
            'online_order_no' => ['nullable', 'string', 'max:80'],
            'customer_name' => ['nullable', 'string', 'max:255'],
            'customer_phone' => ['nullable', 'string', 'max:50'],
            'delivery_address' => ['nullable', 'string'],
            'subtotal' => ['nullable', 'numeric', 'min:0'],
            'delivery_fee' => ['nullable', 'numeric', 'min:0'],
            'grand_total' => ['nullable', 'numeric', 'min:0'],
            'payment_method' => ['nullable', 'in:cash,card,bank,qr,wallet,other'],
            'status' => ['nullable', 'in:pending,accepted,preparing,ready,converted_to_sale,cancelled'],
        ];
    }

    protected function beforeSave(array $payload, Request $request, ?object $model = null): array
    {
        $payload = parent::beforeSave($payload, $request, $model);
        if (empty($payload['online_order_no'])) {
            $payload['online_order_no'] = 'OO-'.now()->format('YmdHis').'-'.random_int(100, 999);
        }
        $payload['grand_total'] = (float) ($payload['subtotal'] ?? 0) + (float) ($payload['delivery_fee'] ?? 0);

        return $payload;
    }

    protected function formProps(?object $model = null): array
    {
        $companyId = request()->user()?->company_id;

        return [
            'branches' => Branch::query()->where('company_id', $companyId)->select('id', 'name')->orderBy('name')->get(),
            'customers' => Customer::query()->where('company_id', $companyId)->select('id', 'name', 'phone')->orderBy('name')->limit(500)->get(),
            'payment_methods' => [
                ['value' => 'cash', 'text' => 'Cash'],
                ['value' => 'card', 'text' => 'Card'],
                ['value' => 'bank', 'text' => 'Bank'],
                ['value' => 'qr', 'text' => 'QR'],
                ['value' => 'wallet', 'text' => 'Wallet'],
                ['value' => 'other', 'text' => 'Other'],
            ],
            'statuses' => [
                ['value' => 'pending', 'text' => 'Pending'],
                ['value' => 'accepted', 'text' => 'Accepted'],
                ['value' => 'preparing', 'text' => 'Preparing'],
                ['value' => 'ready', 'text' => 'Ready'],
                ['value' => 'converted_to_sale', 'text' => 'Converted to Sale'],
                ['value' => 'cancelled', 'text' => 'Cancelled'],
            ],
        ];
    }
}
