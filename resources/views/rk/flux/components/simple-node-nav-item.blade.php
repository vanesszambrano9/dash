@if(isset($node) && ! $node->getIsHidden())
    <flux:navmenu.item href="{{ $node->getUrl() }}">
        {{ $node->getLabel() }}
    </flux:navmenu.item>
@endif
