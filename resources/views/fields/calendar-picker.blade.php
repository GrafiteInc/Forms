{{--
    Grafite\Forms\Fields\Bootstrap\CalendarPicker. Rendered whole on the server
    so Livewire morphs leave it alone; the field's JS fills the menu when it
    opens and empties it when it closes.
--}}
@php($picker = \Grafite\Forms\Fields\Bootstrap\CalendarPicker::present($field, $label, $options))
@if ($picker['group_class'] !== false)
<div class="{{ $picker['group_class'] }}">
@endif
    {!! $picker['label'] !!}
    @if ($picker['grouped'])
    <div class="{{ $picker['input_group_class'] }}">
        {!! $picker['before'] !!}
    @endif
        <div
            class="calendar-picker dropdown"
            data-calendar-picker-time="{{ $picker['time'] ? 'true' : 'false' }}"
            data-calendar-picker-step="{{ $picker['minute_step'] }}"
            data-calendar-picker-default-time="{{ $picker['default_time'] }}"
            data-calendar-picker-week-start="{{ $picker['week_start'] }}"
            data-calendar-picker-placeholder="{{ $picker['placeholder'] }}"
        >
            {!! $picker['field'] !!}
            <button
                type="button"
                id="{{ $picker['id'] }}-toggle"
                class="form-select text-start calendar-picker-toggle{{ $picker['invalid'] ? ' is-invalid' : '' }}"
                data-bs-toggle="dropdown"
                data-bs-auto-close="outside"
                data-bs-offset="0,4"
                data-bs-popper-config='{"strategy":"fixed"}'
                aria-haspopup="dialog"
                aria-expanded="false"
            >
                <svg class="calendar-picker-icon" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true"><path d="M14 0H2a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2M1 3.857C1 3.384 1.448 3 2 3h12c.552 0 1 .384 1 .857v10.286c0 .473-.448.857-1 .857H2c-.552 0-1-.384-1-.857z"/><path d="M6.5 7a1 1 0 1 0 0-2 1 1 0 0 0 0 2m3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2m3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2m-9 3a1 1 0 1 0 0-2 1 1 0 0 0 0 2m3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2m3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2m3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2m-9 3a1 1 0 1 0 0-2 1 1 0 0 0 0 2m3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2m3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2"/></svg>
                <span class="calendar-picker-label{{ $picker['display'] ? '' : ' calendar-picker-placeholder' }}">{{ $picker['display'] ?? $picker['placeholder'] }}</span>
            </button>
            <div class="dropdown-menu calendar-picker-menu" role="dialog" aria-label="{{ $picker['time'] ? 'Choose a date and time' : 'Choose a date' }}" wire:ignore></div>
        </div>
    @if ($picker['grouped'])
        {!! $picker['after'] !!}
    </div>
    @endif
    {!! $errors !!}
@if ($picker['group_class'] !== false)
</div>
@endif
