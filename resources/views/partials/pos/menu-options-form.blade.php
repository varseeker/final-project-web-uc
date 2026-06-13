@php
    $config = $config ?? \App\Support\MenuOptions::forMenu($item);
    $menuId = $item->id;
@endphp

<div class="menu-options-form" data-menu-id="{{ $menuId }}">
    @foreach (['variant', 'size', 'ice', 'sugar'] as $fieldKey)
        @php $field = $config[$fieldKey] ?? ['enabled' => false]; @endphp

        @if (!($field['enabled'] ?? false))
            <input type="hidden" name="{{ $fieldKey }}-{{ $menuId }}" value="-">
            @continue
        @endif

        <div class="mb-4 menu-option-group pos-option-group"
             data-option-field="{{ $fieldKey }}"
             @if(isset($field['visible_when']))
                data-visible-when-field="{{ $field['visible_when']['field'] }}"
                data-visible-when-values="{{ implode(',', $field['visible_when']['values'] ?? []) }}"
                @if(!in_array($field['default'] ?? '', $field['visible_when']['values'] ?? [], true) && ($field['visible_when']['field'] ?? '') === 'variant')
                    style="display: none;"
                @endif
             @endif>

            <label class="pos-option-label">{{ $field['label'] }}</label>

            @if(($field['type'] ?? 'radio') === 'select')
                <select class="form-select pos-option-select"
                        name="{{ $fieldKey }}-{{ $menuId }}"
                        data-menu-option-trigger
                        aria-label="{{ $field['label'] }}"
                        required>
                    @foreach ($field['options'] as $option)
                        <option value="{{ $option }}" @selected(($field['default'] ?? '') === $option)>{{ $option }}</option>
                    @endforeach
                </select>
            @else
                <div class="pos-option-pills" role="group" aria-label="{{ $field['label'] }}">
                    @foreach ($field['options'] as $option)
                        <input type="radio"
                               class="btn-check"
                               name="{{ $fieldKey }}-{{ $menuId }}"
                               id="{{ $fieldKey }}-{{ $menuId }}-{{ $loop->index }}"
                               value="{{ $option }}"
                               data-menu-option-trigger
                               @checked(($field['default'] ?? $field['options'][0] ?? '') === $option)
                               @if($loop->first) required @endif>
                        <label class="btn btn-outline-secondary btn-sm pos-option-pill"
                               for="{{ $fieldKey }}-{{ $menuId }}-{{ $loop->index }}">{{ $option }}</label>
                    @endforeach
                </div>
            @endif
        </div>
    @endforeach
</div>
