@props([
    'course',
    'cssClass' => 'mc mc--left-top',
])

<div
    class="{{ $cssClass }}"
    aria-hidden="true"
>
    <img
        src="{{ $course['image'] }}"
        alt=""
    >
    <span>
        {{ $course['code'] }}
        <em>★ {{ number_format($course['rating'], 1) }}</em>
    </span>
</div>
