@props(['name', 'label', 'value' => '', 'type' => 'text'])
<div>
    <label for="{{ $name }}" class="field-label">{{ $label }}</label>
    <input id="{{ $name }}" name="{{ $name }}" type="{{ $type }}"
        value="{{ $type === 'password' ? '' : old($name, $value) }}" {{ $attributes->merge(['class' => 'clay-input']) }}>
    @error($name)
        <p class="mt-2 text-sm text-red-700">{{ $message }}</p>
    @enderror
</div>
