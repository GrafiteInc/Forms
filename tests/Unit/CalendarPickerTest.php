<?php

namespace Tests\Unit;

use Grafite\Forms\Fields\Bootstrap\CalendarPicker;
use Illuminate\Support\Carbon;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;
use Tests\TestCase;

class CalendarPickerTest extends TestCase
{
    public function test_field_config()
    {
        $field = CalendarPicker::make('due_at', ['time' => true])->toArray();

        $this->assertEquals('hidden', $field['type']);
        $this->assertEquals('forms::fields.calendar-picker', $field['view']);
        $this->assertEquals('Y-m-d H:i:s', $field['attributes']['format']);
        $this->assertArrayNotHasKey('time', $field['attributes']);
        $this->assertArrayNotHasKey('minute_step', $field['attributes']);
        $this->assertArrayNotHasKey('data-formsjs-onload', array_filter($field['attributes']));
        $this->assertStringContainsString('window.FormsJS_calendarPicker', $field['assets']['js']);
        $this->assertStringContainsString('.calendar-picker-menu', $field['assets']['styles']);
        $this->assertEmpty($field['assets']['scripts']);
        $this->assertEmpty($field['assets']['stylesheets']);
    }

    public function test_it_renders_a_date_picker_on_the_server()
    {
        $html = (string) CalendarPicker::make('worked_on')
            ->id('worked_on_1')
            ->withoutLabel()
            ->value('2026-11-18');

        $this->assertStringContainsString('name="worked_on" type="hidden" value="2026-11-18"', $html);
        $this->assertStringContainsString('id="worked_on_1-toggle"', $html);
        $this->assertStringContainsString('<span class="calendar-picker-label">Nov 18, 2026</span>', $html);
        $this->assertStringContainsString('data-calendar-picker-time="false"', $html);
        $this->assertStringContainsString('data-bs-toggle="dropdown"', $html);
        $this->assertStringContainsString('data-bs-auto-close="outside"', $html);
        $this->assertStringContainsString('data-bs-popper-config="{&quot;strategy&quot;:&quot;fixed&quot;', $html);
        $this->assertStringContainsString('&quot;altAxis&quot;:true,&quot;tether&quot;:false', $html);
        $this->assertStringContainsString('class="dropdown-menu calendar-picker-menu" role="dialog" aria-label="Choose a date" wire:ignore></div>', $html);
        $this->assertStringStartsWith('<div class="'.config('forms.form.group-class', 'form-group').'">', $html);
        $this->assertStringNotContainsString('input-group', $html);
        $this->assertStringNotContainsString('format=', $html);
    }

    public function test_the_time_option_normalises_the_value_and_label()
    {
        $html = (string) CalendarPicker::make('due_at', ['time' => true])
            ->value(Carbon::parse('2026-11-18 14:30:45'));

        $this->assertStringContainsString('value="2026-11-18 14:30"', $html);
        $this->assertStringContainsString('Nov 18, 2026 · 2:30 PM', $html);
        $this->assertStringContainsString('data-calendar-picker-time="true"', $html);
        $this->assertStringContainsString('aria-label="Choose a date and time"', $html);
    }

    public function test_options_can_be_set_fluently()
    {
        $html = (string) CalendarPicker::make('due_at')
            ->option('time', true)
            ->option('minute_step', 15)
            ->option('week_start', 1)
            ->option('default_time', '17:00')
            ->value('2026-11-18 09:05');

        $this->assertStringContainsString('value="2026-11-18 09:05"', $html);
        $this->assertStringContainsString('data-calendar-picker-step="15"', $html);
        $this->assertStringContainsString('data-calendar-picker-week-start="1"', $html);
        $this->assertStringContainsString('data-calendar-picker-default-time="17:00"', $html);
        $this->assertStringContainsString('Nov 18, 2026 · 9:05 AM', $html);
    }

    public function test_a_date_only_picker_drops_the_time()
    {
        $html = (string) CalendarPicker::make('starts_at')->value(Carbon::parse('2026-11-18 14:30'));

        $this->assertStringContainsString('value="2026-11-18"', $html);
        $this->assertStringContainsString('>Nov 18, 2026</span>', $html);
    }

    public function test_a_model_bound_value_keeps_its_time()
    {
        $html = (string) CalendarPicker::make('due_at', ['time' => true])
            ->instance((object) ['due_at' => Carbon::parse('2026-11-18 21:00')]);

        $this->assertStringContainsString('value="2026-11-18 21:00"', $html);
        $this->assertStringContainsString('Nov 18, 2026 · 9:00 PM', $html);
    }

    public function test_an_empty_value_shows_the_placeholder()
    {
        $html = (string) CalendarPicker::make('ends_at')
            ->placeholder('Never')
            ->value('');

        $this->assertStringContainsString('value=""', $html);
        $this->assertStringContainsString('<span class="calendar-picker-label calendar-picker-placeholder">Never</span>', $html);
        $this->assertStringContainsString('data-calendar-picker-placeholder="Never"', $html);
    }

    public function test_an_unparseable_value_is_treated_as_empty()
    {
        $html = (string) CalendarPicker::make('ends_at')->value('not a date');

        $this->assertStringContainsString('value=""', $html);
        $this->assertStringContainsString('calendar-picker-placeholder">Pick a date</span>', $html);
    }

    public function test_before_and_after_wrap_the_picker_in_an_input_group()
    {
        $html = (string) CalendarPicker::make('starts_at')
            ->withoutLabel()
            ->before('<span class="input-group-text">Starts On</span>')
            ->after('<button type="button" class="btn">Now</button>');

        $this->assertMatchesRegularExpression(
            '/<div class="input-group">\s*<span class="input-group-text">Starts On<\/span>\s*<div\s+class="calendar-picker dropdown"/',
            $html
        );
        $this->assertMatchesRegularExpression('/<\/div>\s*<button type="button" class="btn">Now<\/button>\s*<\/div>/', $html);
    }

    public function test_an_empty_before_does_not_make_an_input_group()
    {
        $html = (string) CalendarPicker::make('deadline')->before('');

        $this->assertStringNotContainsString('input-group', $html);
    }

    public function test_the_label_points_at_the_toggle()
    {
        $html = (string) CalendarPicker::make('deadline')
            ->id('project_deadline')
            ->label('Deadline');

        $this->assertStringContainsString('for="project_deadline-toggle"', $html);
        $this->assertStringContainsString('>Deadline</label>', $html);
    }

    public function test_it_can_render_without_a_wrapper()
    {
        $html = (string) CalendarPicker::make('deadline')->onlyField();

        $this->assertStringNotContainsString('form-group', $html);
        $this->assertStringNotContainsString('<label', $html);
    }

    public function test_errors_mark_the_toggle_invalid()
    {
        session()->start();
        session()->put('errors', (new ViewErrorBag)->put('default', new MessageBag([
            'due_at' => ['The due at field is required.'],
        ])));

        $html = (string) CalendarPicker::make('due_at');

        $this->assertStringContainsString('calendar-picker-toggle is-invalid', $html);
        $this->assertStringContainsString('The due at field is required.', $html);
    }

    public function test_display_matches_the_js_label_format()
    {
        $this->assertSame('Sep 5, 2026', CalendarPicker::display(Carbon::parse('2026-09-05 00:00')));
        $this->assertSame('Sep 5, 2026 · 12:00 AM', CalendarPicker::display(Carbon::parse('2026-09-05 00:00'), true));
        $this->assertSame('Sep 5, 2026 · 12:15 PM', CalendarPicker::display(Carbon::parse('2026-09-05 12:15'), true));
    }

    public function test_the_js_only_binds_once()
    {
        $js = CalendarPicker::js('due_at', []);

        $this->assertStringStartsWith('if (! window.FormsJS_calendarPicker) {', trim($js));
        $this->assertSame($js, CalendarPicker::js('other', ['time' => true]));
    }
}
