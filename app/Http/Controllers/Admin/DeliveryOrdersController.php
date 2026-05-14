<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseCrudController;
use App\Models\Branch;
use App\Models\DeliveryOrder;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;

class DeliveryOrdersController extends BaseCrudController
{
    protected string $model = DeliveryOrder::class;

    protected string $viewNamespace = 'DeliveryOrders';

    protected string $permissionPrefix = 'delivery_orders';

    protected string $routePrefix = 'admin.delivery-orders';

    protected string $titleKey = 'coffee.delivery_orders';

    protected function columns(): array
    {
        return [
            ['data' => 'delivery_no', 'title' => 'coffee.code'],
            ['data' => 'order_id', 'title' => 'coffee.order'],
            ['data' => 'receiver_name', 'title' => 'coffee.receiver'],
            ['data' => 'delivery_fee', 'title' => 'coffee.delivery_fee'],
            ['data' => 'status', 'title' => 'coffee.status'],
        ];
    }

    protected function rules(Request $request, $model = null): array
    {
        return [
            'branch_id' => ['nullable', 'integer', 'exists:branches,id'],
            'order_id' => ['nullable', 'integer', 'exists:orders,id'],
            'delivery_staff_id' => ['nullable', 'integer', 'exists:users,id'],
            'delivery_no' => ['nullable', 'string', 'max:80'],
            'delivery_address' => ['required', 'string'],
            'receiver_name' => ['nullable', 'string', 'max:255'],
            'receiver_phone' => ['nullable', 'string', 'max:50'],
            'delivery_fee' => ['nullable', 'numeric', 'min:0'],
            'distance_km' => ['nullable', 'numeric', 'min:0'],
            'status' => ['nullable', 'in:pending,accepted,preparing,ready_for_pickup,on_delivery,delivered,failed,cancelled'],
            'note' => ['nullable', 'string'],
        ];
    }

    protected function beforeSave(array $payload, Request $request, ?object $model = null): array
    {
        $payload = parent::beforeSave($payload, $request, $model);
        if (empty($payload['delivery_no'])) {
            $payload['delivery_no'] = 'DL-'.now()->format('YmdHis').'-'.random_int(100, 999);
        }

        return $payload;
    }

    protected function formProps(?object $model = null): array
    {
        $companyId = request()->user()?->company_id;

        return [
            'branches' => Branch::query()->where('company_id', $companyId)->select('id', 'name')->orderBy('name')->get(),
            'orders' => Order::query()->where('company_id', $companyId)->latest('id')->limit(200)->select('id', 'order_no')->get(),
            'staff' => User::query()->where('company_id', $companyId)->select('id', 'name', 'email')->orderBy('name')->get(),
            'statuses' => [
                ['value' => 'pending', 'text' => 'Pending'],
                ['value' => 'accepted', 'text' => 'Accepted'],
                ['value' => 'preparing', 'text' => 'Preparing'],
                ['value' => 'ready_for_pickup', 'text' => 'Ready for Pickup'],
                ['value' => 'on_delivery', 'text' => 'On Delivery'],
                ['value' => 'delivered', 'text' => 'Delivered'],
                ['value' => 'failed', 'text' => 'Failed'],
                ['value' => 'cancelled', 'text' => 'Cancelled'],
            ],
        ];
    }
}
