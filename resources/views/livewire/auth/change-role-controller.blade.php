<div>
    <flux:dropdown position="bottom" align="end">
        <flux:profile :name="auth()->user()->getLabelROle()" initials="Rol" icon="chevrons-up-down" />
        <flux:navmenu>
            @foreach ($roles as $role)
                <flux:navmenu.item wire:click="changeRole({{ $role->id }})"
                    :active="$role->id === auth()->user()->default_role" href="#" icon="user">{{ $role->name }}
                </flux:navmenu.item>
            @endforeach
            <flux:navmenu.item wire:click="changeRole(null)" :active="is_null(auth()->user()->default_role)"
                href="#" icon="user">
                Todos los roles
            </flux:navmenu.item>
        </flux:navmenu>
    </flux:dropdown>
</div>
