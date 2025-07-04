@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-semibold text-sm text-white']) }}>
    {{ $value ?? $slot }}
</label>
