<?php
use Rk\RoutingKit\Entities\RkRoute;

return [
    RkRoute::makeGroup('auth_group')
        ->setUrlMiddleware([
            'auth:sanctum',
            config('jetstream.auth_session'),
            'verified',
        ])
        ->setItems([
            RkRoute::make('dashboard')
                ->setParentId('auth_group')
                ->setUrlMethod('get')
                ->setUrlController('App\Livewire\Dashboard')
                ->setRoles(['admin_general'])
                ->setItems([])
                ->setEndBlock('dashboard'),
        ])
        ->setEndBlock('auth_group'),
];