@php
    /**
     * @var int $id
     * @var string|null $editRoute
     * @var string|null $destroyRoute
     * @var string|null $showRoute
     * @var bool $canEdit
     * @var bool $canDelete
     */
@endphp
<div class="d-flex gap-1">
    @isset($showRoute)
        <a href="{{ route($showRoute, $id) }}"
           class="btn btn-sm btn-outline-secondary"
           data-inertia
           title="{{ __('coffee.view') }}">
            <i class="bi bi-eye"></i>
        </a>
    @endisset
    @if (($canEdit ?? true) && ! empty($editRoute))
        <a href="{{ route($editRoute, $id) }}"
           class="btn btn-sm btn-outline-primary"
           data-inertia
           title="{{ __('coffee.edit') }}">
            <i class="bi bi-pencil"></i>
        </a>
    @endif
    @if (($canDelete ?? true) && ! empty($destroyRoute))
        <button type="button"
                class="btn btn-sm btn-outline-danger js-confirm-delete"
                data-url="{{ route($destroyRoute, $id) }}"
                title="{{ __('coffee.delete') }}">
            <i class="bi bi-trash"></i>
        </button>
    @endif
</div>
