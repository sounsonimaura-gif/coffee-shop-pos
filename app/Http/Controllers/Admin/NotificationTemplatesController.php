<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseCrudController;
use App\Models\NotificationTemplate;
use Illuminate\Http\Request;

class NotificationTemplatesController extends BaseCrudController
{
    protected string $model = NotificationTemplate::class;

    protected string $viewNamespace = 'NotificationTemplates';

    protected string $permissionPrefix = 'notification_templates';

    protected string $routePrefix = 'admin.notification-templates';

    protected string $titleKey = 'coffee.notification_templates';

    protected function columns(): array
    {
        return [
            ['data' => 'template_key', 'title' => 'coffee.code'],
            ['data' => 'title', 'title' => 'coffee.title'],
            ['data' => 'channel', 'title' => 'coffee.channel'],
            ['data' => 'is_active', 'title' => 'coffee.is_active'],
        ];
    }

    protected function rules(Request $request, $model = null): array
    {
        return [
            'template_key' => ['required', 'string', 'max:150'],
            'title' => ['required', 'string', 'max:255'],
            'body' => ['nullable', 'string'],
            'channel' => ['required', 'in:system,email,telegram,sms,phpflasher,sweetalert2'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    protected function formProps(?object $model = null): array
    {
        return [
            'channels' => [
                ['value' => 'system', 'text' => 'System'],
                ['value' => 'email', 'text' => 'Email'],
                ['value' => 'telegram', 'text' => 'Telegram'],
                ['value' => 'sms', 'text' => 'SMS'],
                ['value' => 'phpflasher', 'text' => 'PHPFlasher'],
                ['value' => 'sweetalert2', 'text' => 'SweetAlert2'],
            ],
        ];
    }
}
