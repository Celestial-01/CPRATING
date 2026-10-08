@props([
    'value',
    'label',
    'tone' => 'default',
])

<div class="flex h-16 min-w-0 flex-col items-center justify-center rounded-[16px] border border-[#edf1f6] bg-[#f8fafc] px-2 text-center">
    <dt class="order-2 mt-1 text-[10px] leading-3 text-[#999999]">
        {{ $label }}
    </dt>
    <dd
        @class([
            'order-1 text-base font-bold leading-5',
            'text-[#ffa51f]' => $tone === 'rating',
            'text-[#0189ff]' => $tone === 'views',
            'text-black' => $tone === 'default',
        ])
    >
        {{ $value }}
    </dd>
</div>
