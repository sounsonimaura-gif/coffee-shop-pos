@php
    /**
     * @var int $id
     * @var string $editRoute
     * @var string $destroyRoute
     * @var bool $canEdit
     * @var bool $canDelete
     */
@endphp
<div class="d-flex gap-1">
    @if ($canEdit ?? true)
        <a href="{{ route($editRoute, $id) }}"
           class="btn btn-sm btn-outline-primary"
           data-inertia
           title="{{ __('coffee.edit') }}">
            <i class="bi bi-pencil"></i>
        </a>
    @endif
    @if ($canDelete ?? true)
        <button type="button"
                class="btn btn-sm btn-outline-danger js-confirm-delete"
                data-url="{{ route($destroyRoute, $id) }}"
                title="{{ __('coffee.delete') }}">
            <i class="bi bi-trash"></i>
        </button>
    @endif
</div>
