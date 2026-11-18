<?php

namespace Grafite\Forms\Fields\Bootstrap;

use Grafite\Forms\Fields\Field;
use Illuminate\Support\Carbon;
use Throwable;

/**
 * A date picker with an optional time row, rendered as a Bootstrap 5.3
 * dropdown under a form-select styled toggle.
 *
 * The whole picker is rendered on the server (forms::fields.calendar-picker),
 * so a Livewire morph finds nothing to change, and the menu uses Popper's
 * fixed strategy so a scrollable modal body can't clip it. The calendar is
 * built in the browser only while the menu is open, by listeners delegated
 * from document, so pickers added later need no setup.
 *
 * It submits "Y-m-d", or "Y-m-d H:i" with the time row enabled:
 *     CalendarPicker::make('due_at', ['time' => true])
 *
 * Preset buttons can set it with:
 *     window.FormsJS_calendarPicker.set('due_at', '2026-11-18 21:00')
 */
class CalendarPicker extends Field
{
    protected const DEFAULTS = [
        'time' => false,
        'minute_step' => 5,
        'default_time' => '09:00',
        'week_start' => 0,
    ];

    /**
     * Bootstrap's dropdown Popper config. The fixed strategy lets the menu
     * escape a scrollable modal body. When it fits neither below nor above
     * the toggle (a phone, say), flip gives up, so preventOverflow's altAxis
     * slides it back on screen, over the toggle if need be.
     */
    protected const POPPER = [
        'strategy' => 'fixed',
        'modifiers' => [
            ['name' => 'offset', 'options' => ['offset' => [0, 4]]],
            ['name' => 'flip', 'options' => ['padding' => 8]],
            ['name' => 'preventOverflow', 'options' => ['altAxis' => true, 'tether' => false, 'padding' => 8]],
        ],
    ];

    protected static function getType()
    {
        return 'hidden';
    }

    protected static function getFactory()
    {
        return 'date';
    }

    protected static function getView()
    {
        return 'forms::fields.calendar-picker';
    }

    protected static function getOptions()
    {
        return static::DEFAULTS;
    }

    /**
     * Keep these out of the input's HTML attributes.
     */
    protected static function fieldOptions()
    {
        return array_keys(static::DEFAULTS);
    }

    /**
     * FieldBuilder formats a model-bound Carbon value with the `format`
     * attribute; keep the time here and let present() trim it and drop it.
     */
    protected static function getAttributes()
    {
        return [
            'format' => 'Y-m-d H:i:s',
        ];
    }

    /**
     * Everything the picker view needs, from what FieldMaker hands it: the
     * rendered hidden input (whose value already reflects old input, the model
     * or an explicit ->value()), the rendered label and the field config.
     */
    public static function present(string $field, ?string $label, array $options): array
    {
        $time = (bool) static::setting($options, 'time');
        $id = $options['attributes']['id'] ?? '';

        preg_match('/\svalue="([^"]*)">$/', $field, $matches);

        $date = static::parse(html_entity_decode($matches[1] ?? '', ENT_QUOTES | ENT_HTML5));
        $value = $date?->format($time ? 'Y-m-d H:i' : 'Y-m-d') ?? '';

        $field = preg_replace('/\svalue="[^"]*">$/', ' value="'.e($value).'">', $field);
        $field = preg_replace('/\sformat="[^"]*"/', '', $field, 1);

        $wrapper = $options['wrapper'] ?? true;
        $before = (string) ($options['before'] ?? '');
        $after = (string) ($options['after'] ?? '');

        return [
            'id' => $id,
            'field' => $field,
            'value' => $value,
            'display' => $date ? static::display($date, $time) : null,
            'placeholder' => $options['attributes']['placeholder'] ?? ($time ? 'Pick a date and time' : 'Pick a date'),
            'label' => $label ? str_replace('for="'.$id.'"', 'for="'.$id.'-toggle"', $label) : '',
            'group_class' => $wrapper === false ? false : (is_string($wrapper) ? $wrapper : config('forms.form.group-class', 'form-group')),
            'input_group_class' => config('forms.form.before-after-input-wrapper', 'input-group'),
            'before' => $before,
            'after' => $after,
            'grouped' => trim($before) !== '' || trim($after) !== '',
            'invalid' => str_contains($options['attributes']['class'] ?? '', 'is-invalid'),
            'time' => $time,
            'minute_step' => max(1, min(30, (int) static::setting($options, 'minute_step'))),
            'default_time' => (string) static::setting($options, 'default_time'),
            'week_start' => ((int) static::setting($options, 'week_start')) % 7,
            'popper_config' => json_encode(static::POPPER),
        ];
    }

    /**
     * The toggle text; the JS builds the same string after a pick.
     */
    public static function display(Carbon $date, bool $time = false): string
    {
        $text = $date->format('M j, Y');

        return $time ? $text.' · '.$date->format('g:i A') : $text;
    }

    /**
     * A ->option() set on the field wins over the make() options and defaults.
     */
    protected static function setting(array $options, string $key): mixed
    {
        return $options['options'][$key]
            ?? $options['customOptions'][$key]
            ?? static::DEFAULTS[$key];
    }

    protected static function parse(string $value): ?Carbon
    {
        if (trim($value) === '') {
            return null;
        }

        try {
            return Carbon::parse($value);
        } catch (Throwable) {
            return null;
        }
    }

    public static function styles($id, $options)
    {
        return <<<'CSS'
.calendar-picker {
    position: relative;
    width: 100%;
}

.calendar-picker-toggle {
    display: flex;
    align-items: center;
    gap: .5rem;
    width: 100%;
}

.calendar-picker-icon {
    flex-shrink: 0;
    width: 1em;
    height: 1em;
    color: var(--bs-secondary-color);
}

.calendar-picker-label {
    min-width: 0;
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
}

.calendar-picker-placeholder {
    color: var(--bs-secondary-color);
}

.input-group > .calendar-picker {
    flex: 1 1 auto;
    width: 1%;
    min-width: 0;
}

.input-group > .calendar-picker:focus-within {
    z-index: 5;
}

.input-group > .calendar-picker:not(:first-child) > .calendar-picker-toggle {
    border-top-left-radius: 0;
    border-bottom-left-radius: 0;
}

.input-group > .calendar-picker:not(:last-child) > .calendar-picker-toggle {
    border-top-right-radius: 0;
    border-bottom-right-radius: 0;
}

.calendar-picker:has(> .calendar-picker-toggle.is-invalid) ~ .invalid-feedback,
.input-group:has(> .calendar-picker > .calendar-picker-toggle.is-invalid) ~ .invalid-feedback {
    display: block;
}

.calendar-picker-menu {
    min-width: 18rem;
    max-height: calc(100vh - 1rem);
    max-height: calc(100dvh - 1rem);
    overflow-y: auto;
    overscroll-behavior: contain;
    padding: .75rem;
}

.calendar-picker-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: .5rem;
}

.calendar-picker-title {
    font-weight: 600;
    color: var(--bs-emphasis-color);
}

.calendar-picker-nav {
    --bs-btn-padding-x: .5rem;
    --bs-btn-color: var(--bs-secondary-color);
    --bs-btn-hover-color: var(--bs-emphasis-color);
    --bs-btn-hover-bg: var(--bs-tertiary-bg);
    --bs-btn-active-bg: var(--bs-secondary-bg);
}

.calendar-picker-weekdays,
.calendar-picker-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 2px;
    text-align: center;
}

.calendar-picker-weekdays {
    margin-bottom: .25rem;
    font-size: .75rem;
    font-weight: 600;
    color: var(--bs-secondary-color);
}

.calendar-picker-weekdays abbr {
    text-decoration: none;
    cursor: default;
}

.calendar-picker-day {
    display: flex;
    align-items: center;
    justify-content: center;
    aspect-ratio: 1;
    padding: 0;
    border: 0;
    border-radius: var(--bs-border-radius);
    background-color: transparent;
    color: var(--bs-body-color);
    font-size: .875rem;
    font-variant-numeric: tabular-nums;
}

.calendar-picker-day:hover {
    background-color: var(--bs-tertiary-bg);
}

.calendar-picker-day:focus-visible {
    outline: 0;
    box-shadow: 0 0 0 .2rem rgba(var(--bs-primary-rgb), .25);
}

.calendar-picker-day.is-outside {
    color: var(--bs-tertiary-color);
}

.calendar-picker-day.is-today {
    box-shadow: inset 0 0 0 1px var(--bs-primary-border-subtle);
    color: var(--bs-primary-text-emphasis);
    font-weight: 600;
}

.calendar-picker-day.is-selected,
.calendar-picker-day.is-selected:hover {
    background-color: var(--bs-primary);
    color: #fff;
    font-weight: 600;
}

.calendar-picker-time {
    display: flex;
    align-items: center;
    gap: .375rem;
    margin-top: .75rem;
    padding-top: .75rem;
    border-top: var(--bs-border-width) solid var(--bs-border-color-translucent);
}

.calendar-picker-time .form-select {
    flex: 1 1 0;
    min-width: 0;
}

.calendar-picker-time-separator {
    font-weight: 600;
    color: var(--bs-secondary-color);
}

.calendar-picker-footer {
    display: flex;
    align-items: center;
    gap: .25rem;
    margin-top: .75rem;
    padding-top: .5rem;
    border-top: var(--bs-border-width) solid var(--bs-border-color-translucent);
}

.calendar-picker-footer .btn-link {
    text-decoration: none;
}

.calendar-picker-clear {
    --bs-btn-color: var(--bs-secondary-color);
    --bs-btn-hover-color: var(--bs-danger);
}
CSS;
    }

    public static function js($id, $options)
    {
        return <<<'JS'
if (! window.FormsJS_calendarPicker) {
    (function () {
        const MONTHS = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
        const WEEKDAYS = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
        const ARROWS = { ArrowLeft: -1, ArrowRight: 1, ArrowUp: -7, ArrowDown: 7 };

        // The month each open picker is showing, and the day keyboard focus is on.
        const views = new WeakMap();

        const pad = function (number) {
            return String(number).padStart(2, "0");
        };

        // Values are wall-clock strings, parsed by hand and never through
        // Date's UTC parsing of "Y-m-d".
        const parse = function (value) {
            let match = /^(\d{4})-(\d{1,2})-(\d{1,2})(?:[ T](\d{1,2}):(\d{2}))?/.exec(value || "");

            if (! match) {
                return null;
            }

            return {
                year: Number(match[1]),
                month: Number(match[2]) - 1,
                day: Number(match[3]),
                hour: match[4] === undefined ? null : Number(match[4]),
                minute: match[5] === undefined ? null : Number(match[5]),
            };
        };

        const fromDate = function (date) {
            return { year: date.getFullYear(), month: date.getMonth(), day: date.getDate(), hour: null, minute: null };
        };

        const today = function () {
            return fromDate(new Date());
        };

        const dateKey = function (date) {
            return date.year + "-" + pad(date.month + 1) + "-" + pad(date.day);
        };

        const sameDay = function (a, b) {
            return !! (a && b && a.year === b.year && a.month === b.month && a.day === b.day);
        };

        const parts = function (root) {
            return {
                input: root.querySelector(":scope > input"),
                toggle: root.querySelector(":scope > .calendar-picker-toggle"),
                label: root.querySelector(":scope > .calendar-picker-toggle .calendar-picker-label"),
                menu: root.querySelector(":scope > .calendar-picker-menu"),
            };
        };

        const settings = function (root) {
            let time = (root.dataset.calendarPickerDefaultTime || "09:00").split(":").map(Number);

            return {
                time: root.dataset.calendarPickerTime === "true",
                step: Number(root.dataset.calendarPickerStep) || 5,
                weekStart: Number(root.dataset.calendarPickerWeekStart) || 0,
                placeholder: root.dataset.calendarPickerPlaceholder || "",
                defaultHour: Number.isNaN(time[0]) ? 9 : time[0],
                defaultMinute: Number.isNaN(time[1]) ? 0 : time[1],
            };
        };

        // Matches CalendarPicker::display() in PHP: "Nov 18, 2026 · 2:30 PM".
        const display = function (date, time) {
            let text = MONTHS[date.month].slice(0, 3) + " " + date.day + ", " + date.year;

            if (time) {
                text += " · " + (date.hour % 12 || 12) + ":" + pad(date.minute) + " " + (date.hour < 12 ? "AM" : "PM");
            }

            return text;
        };

        const isOpen = function (root) {
            return parts(root).menu.classList.contains("show");
        };

        const hide = function (root) {
            let toggle = parts(root).toggle;
            let instance = window.bootstrap.Dropdown.getInstance(toggle);

            if (instance) {
                instance.hide();
            }

            toggle.focus();
        };

        // The hour and minute the time row shows, as 24-hour numbers.
        const selectedTime = function (root) {
            let menu = parts(root).menu;
            let hour = menu.querySelector("[data-calendar-picker-part=\"hour\"]");
            let minute = menu.querySelector("[data-calendar-picker-part=\"minute\"]");
            let meridiem = menu.querySelector("[data-calendar-picker-part=\"meridiem\"]");

            if (! hour || ! minute || ! meridiem) {
                return null;
            }

            return {
                hour: (Number(hour.value) % 12) + (meridiem.value === "PM" ? 12 : 0),
                minute: Number(minute.value),
            };
        };

        // Write a value to the hidden input and the toggle label, and tell
        // anything listening (form autosave, wire:model, validation).
        const commit = function (root, date) {
            let elements = parts(root);
            let config = settings(root);
            let value = "";

            if (date && config.time) {
                let time = selectedTime(root);

                date = Object.assign({}, date, {
                    hour: date.hour !== null ? date.hour : (time ? time.hour : config.defaultHour),
                    minute: date.minute !== null ? date.minute : (time ? time.minute : config.defaultMinute),
                });
            }

            if (date) {
                value = config.time ? dateKey(date) + " " + pad(date.hour) + ":" + pad(date.minute) : dateKey(date);
            }

            elements.label.textContent = date ? display(date, config.time) : config.placeholder;
            elements.label.classList.toggle("calendar-picker-placeholder", ! date);

            if (elements.input.value === value) {
                return;
            }

            elements.input.value = value;
            elements.input.dispatchEvent(new Event("input", { bubbles: true }));
            elements.input.dispatchEvent(new Event("change", { bubbles: true }));
        };

        const options = function (values, selected, label) {
            return values.map(function (value) {
                return "<option value=\"" + value + "\"" + (value === selected ? " selected" : "") + ">" + label(value) + "</option>";
            }).join("");
        };

        const icon = function (path) {
            return "<svg class=\"calendar-picker-icon\" viewBox=\"0 0 16 16\" fill=\"currentColor\" aria-hidden=\"true\"><path d=\"" + path + "\"/></svg>";
        };

        const CLOCK = "M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71zM8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16m7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0";
        const CHEVRON_LEFT = "M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0";
        const CHEVRON_RIGHT = "M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708";

        const timeRow = function (root, selected) {
            let config = settings(root);
            let hour = selected && selected.hour !== null ? selected.hour : config.defaultHour;
            let minute = selected && selected.minute !== null ? selected.minute : config.defaultMinute;
            let minutes = [];

            for (let value = 0; value < 60; value += config.step) {
                minutes.push(value);
            }

            if (! minutes.includes(minute)) {
                minutes.push(minute);
                minutes.sort(function (a, b) {
                    return a - b;
                });
            }

            return "<div class=\"calendar-picker-time\">"
                + icon(CLOCK)
                + "<select class=\"form-select form-select-sm\" data-calendar-picker-part=\"hour\" aria-label=\"Hour\">"
                + options([12, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11], hour % 12 || 12, String)
                + "</select>"
                + "<span class=\"calendar-picker-time-separator\" aria-hidden=\"true\">:</span>"
                + "<select class=\"form-select form-select-sm\" data-calendar-picker-part=\"minute\" aria-label=\"Minute\">"
                + options(minutes, minute, pad)
                + "</select>"
                + "<select class=\"form-select form-select-sm\" data-calendar-picker-part=\"meridiem\" aria-label=\"AM or PM\">"
                + options(["AM", "PM"], hour < 12 ? "AM" : "PM", String)
                + "</select>"
                + "</div>";
        };

        // Build the calendar for the month in view, then focus `focus` (a
        // selector inside the menu), since re-rendering drops the focus.
        const render = function (root, focus) {
            let elements = parts(root);
            let config = settings(root);
            let selected = parse(elements.input.value);
            let now = today();
            let view = views.get(root);

            if (! view) {
                let base = selected || now;

                view = { year: base.year, month: base.month, active: null };
                views.set(root, view);
            }

            let first = new Date(view.year, view.month, 1);
            let offset = (first.getDay() - config.weekStart + 7) % 7;
            let inView = function (date) {
                return !! date && date.year === view.year && date.month === view.month;
            };

            // Roving tabindex: the day keyboard focus left, else the
            // selection, else today, else the 1st of the month.
            let active = [view.active, selected, now].find(inView) || { year: view.year, month: view.month, day: 1 };
            let weekdays = "";
            let grid = "";

            for (let index = 0; index < 7; index++) {
                let name = WEEKDAYS[(config.weekStart + index) % 7];

                weekdays += "<abbr title=\"" + name + "\">" + name.slice(0, 2) + "</abbr>";
            }

            for (let index = 0; index < 42; index++) {
                let date = fromDate(new Date(view.year, view.month, 1 - offset + index));
                let classes = "calendar-picker-day";
                let current = "";

                if (date.month !== view.month) {
                    classes += " is-outside";
                }

                if (sameDay(date, now)) {
                    classes += " is-today";
                    current = " aria-current=\"date\"";
                }

                if (sameDay(date, selected)) {
                    classes += " is-selected";
                }

                let label = WEEKDAYS[new Date(date.year, date.month, date.day).getDay()] + ", " + MONTHS[date.month] + " " + date.day + ", " + date.year;

                grid += "<button type=\"button\" class=\"" + classes + "\" data-calendar-picker-action=\"day\" data-date=\"" + dateKey(date) + "\""
                    + " tabindex=\"" + (sameDay(date, active) ? 0 : -1) + "\" aria-label=\"" + label + "\""
                    + " aria-pressed=\"" + (sameDay(date, selected) ? "true" : "false") + "\"" + current + ">" + date.day + "</button>";
            }

            elements.menu.innerHTML = "<div class=\"calendar-picker-header\">"
                + "<button type=\"button\" class=\"btn btn-sm calendar-picker-nav\" data-calendar-picker-action=\"prev\" aria-label=\"Previous month\">" + icon(CHEVRON_LEFT) + "</button>"
                + "<div class=\"calendar-picker-title\" aria-live=\"polite\">" + MONTHS[view.month] + " " + view.year + "</div>"
                + "<button type=\"button\" class=\"btn btn-sm calendar-picker-nav\" data-calendar-picker-action=\"next\" aria-label=\"Next month\">" + icon(CHEVRON_RIGHT) + "</button>"
                + "</div>"
                + "<div class=\"calendar-picker-weekdays\">" + weekdays + "</div>"
                + "<div class=\"calendar-picker-grid\">" + grid + "</div>"
                + (config.time ? timeRow(root, selected) : "")
                + "<div class=\"calendar-picker-footer\">"
                + "<button type=\"button\" class=\"btn btn-sm btn-link\" data-calendar-picker-action=\"today\">Today</button>"
                + "<button type=\"button\" class=\"btn btn-sm btn-link calendar-picker-clear\" data-calendar-picker-action=\"clear\">Clear</button>"
                + (config.time ? "<button type=\"button\" class=\"btn btn-sm btn-primary ms-auto\" data-calendar-picker-action=\"done\">Done</button>" : "")
                + "</div>";

            if (focus) {
                let target = elements.menu.querySelector(focus);

                if (target) {
                    target.focus();
                }
            }
        };

        const focusActiveDay = function (root) {
            let day = parts(root).menu.querySelector(".calendar-picker-day[tabindex=\"0\"]");

            if (day) {
                day.focus();
            }
        };

        const moveMonth = function (root, delta) {
            let view = views.get(root);
            let date = new Date(view.year, view.month + delta, 1);

            views.set(root, { year: date.getFullYear(), month: date.getMonth(), active: null });
        };

        // Pick a day: in date-only mode that's the whole job, so close; with
        // a time row, stay open on the picked day so the time can be set too.
        const pick = function (root, date) {
            commit(root, date);

            if (! settings(root).time) {
                hide(root);

                return;
            }

            views.set(root, { year: date.year, month: date.month, active: date });
            render(root, "[data-date=\"" + dateKey(date) + "\"]");
        };

        const actions = {
            prev: function (root) {
                moveMonth(root, -1);
                render(root, "[data-calendar-picker-action=\"prev\"]");
            },
            next: function (root) {
                moveMonth(root, 1);
                render(root, "[data-calendar-picker-action=\"next\"]");
            },
            day: function (root, button) {
                pick(root, parse(button.dataset.date));
            },
            today: function (root) {
                pick(root, today());
            },
            clear: function (root) {
                commit(root, null);
                hide(root);
            },
            done: function (root) {
                hide(root);
            },
        };

        const rootOf = function (event) {
            return event.target && event.target.closest ? event.target.closest(".calendar-picker") : null;
        };

        document.addEventListener("show.bs.dropdown", function (event) {
            let root = rootOf(event);

            if (root) {
                views.delete(root);
                render(root);
            }
        });

        document.addEventListener("shown.bs.dropdown", function (event) {
            let root = rootOf(event);

            if (root) {
                focusActiveDay(root);
            }
        });

        document.addEventListener("hidden.bs.dropdown", function (event) {
            let root = rootOf(event);

            if (root) {
                views.delete(root);
                parts(root).menu.replaceChildren();
            }
        });

        // Bootstrap's outside-click handler reads event.composedPath(), which
        // is fixed at dispatch, so re-rendering the menu doesn't close it.
        document.addEventListener("click", function (event) {
            let button = event.target.closest ? event.target.closest(".calendar-picker-menu [data-calendar-picker-action]") : null;

            if (! button || ! actions[button.dataset.calendarPickerAction]) {
                return;
            }

            event.preventDefault();
            actions[button.dataset.calendarPickerAction](button.closest(".calendar-picker"), button);
        });

        // The time row's selects have no name, so they never submit; stop
        // their input/change events so form autosave only hears the input.
        const timeEvent = function (event) {
            let select = event.target.closest ? event.target.closest(".calendar-picker-menu [data-calendar-picker-part]") : null;

            if (! select) {
                return;
            }

            event.stopPropagation();

            if (event.type === "change") {
                let root = select.closest(".calendar-picker");
                let date = parse(parts(root).input.value) || today();
                let time = selectedTime(root);

                commit(root, Object.assign({}, date, { hour: time.hour, minute: time.minute }));
            }
        };

        document.addEventListener("input", timeEvent, true);
        document.addEventListener("change", timeEvent, true);

        // Capture phase on window, so this runs before both a modal's Escape
        // handler on .modal and Bootstrap's dropdown keyboard handler, which
        // is delegated from document in the capture phase too.
        window.addEventListener("keydown", function (event) {
            let root = rootOf(event);

            if (! root || ! isOpen(root)) {
                return;
            }

            let elements = parts(root);

            if (event.key === "Escape") {
                event.preventDefault();
                event.stopPropagation();
                hide(root);

                return;
            }

            if (event.target === elements.toggle && event.key === "ArrowDown") {
                event.preventDefault();
                event.stopPropagation();
                focusActiveDay(root);

                return;
            }

            if (! elements.menu.contains(event.target)) {
                return;
            }

            // Bootstrap's dropdown keyboard handler only exempts input and
            // textarea, so it would swallow arrow keys meant for the selects.
            if (event.target.matches("[data-calendar-picker-part]")) {
                if (event.key === "ArrowUp" || event.key === "ArrowDown") {
                    event.stopPropagation();
                }

                return;
            }

            if (! event.target.matches(".calendar-picker-day")) {
                return;
            }

            let current = parse(event.target.dataset.date);
            let target = null;

            if (ARROWS[event.key] !== undefined) {
                target = fromDate(new Date(current.year, current.month, current.day + ARROWS[event.key]));
            } else if (event.key === "PageUp" || event.key === "PageDown") {
                let date = new Date(current.year, current.month + (event.key === "PageUp" ? -1 : 1), 1);
                let last = new Date(date.getFullYear(), date.getMonth() + 1, 0).getDate();

                target = Object.assign(fromDate(date), { day: Math.min(current.day, last) });
            }

            if (! target) {
                return;
            }

            event.preventDefault();
            event.stopPropagation();
            views.set(root, { year: target.year, month: target.month, active: target });
            render(root, "[data-date=\"" + dateKey(target) + "\"]");
        }, true);

        // For preset buttons: FormsJS_calendarPicker.set("due_at", "2026-11-18 21:00").
        // Takes an input element or its id; an empty value clears the picker.
        window.FormsJS_calendarPicker = {
            set: function (target, value) {
                let input = typeof target === "string" ? document.getElementById(target) : target;
                let root = input ? input.closest(".calendar-picker") : null;

                if (! root) {
                    return;
                }

                commit(root, parse(value));

                if (isOpen(root)) {
                    views.delete(root);
                    render(root);
                }
            },
            clear: function (target) {
                this.set(target, "");
            },
        };
    })();
}
JS;
    }
}
