@props([
    'node' => null,
])

@if(isset($node) && ! $node->getIsHidden())
    <flux:navbar.item
        :href="$node->getUrl()"
        :icon="$node->getHeroIcon()"
        :current="$node->isActive()"
        badge="{{ $node->getFinalBage() }}"
        badge-color="pink"
        wire:navigate
    >
        {{ $node->getLabel() }}
    </flux:navbar.item>
@endif
