@props(['type', 'label', 'placeholder', 'name'])

<flux:input type="{{$type}}" label="{{$label}}" placeholder="{{$placeholder}}" {{ $attributes }}/>