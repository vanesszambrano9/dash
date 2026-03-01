<?php

use Rk\RoutingKit\Entities\RkRoute;

return [

    RkRoute::makeGroup('grupo_auth')
        ->setUrlMiddleware(['auth'])
        ->setItems([

            RkRoute::make('dashboard')
                ->setParentId('grupo_auth')
                ->setAccessPermission('acceder-dashboard')
                ->setUrl('/dashboard')
                ->setUrlMethod('get')
                ->setUrlController('App\Livewire\AdminGeneral\Dashboard\Dashboard')
                ->setRoles(['admin_general'])
                ->setItems([])
                ->setEndBlock('dashboard'),

            RkRoute::make('userlist_Iac')
                ->setParentId('grupo_auth')
                ->setAccessPermission('acceder-userlist_Iac')
                ->setUrl('/userlist_Iac')
                ->setUrlMethod('get')
                ->setUrlController('App\Livewire\AdminGeneral\UsuariosYPermisos\UserList')
                ->setRoles(['admin_general'])
                ->setItems([])
                ->setEndBlock('userlist_Iac'),

            RkRoute::make('list_roles')
                ->setParentId('grupo_auth')
                ->setAccessPermission('acceder-administrar-roles')
                ->setUrl('/list_roles')
                ->setUrlMethod('get')
                ->setUrlController('App\Livewire\AdminGeneral\UsuariosYPermisos\RolesList')
                ->setRoles(['admin_general'])
                ->setItems([])
                ->setEndBlock('list_roles'),

            RkRoute::make('list_permissions')
                ->setParentId('grupo_auth')
                ->setAccessPermission('acceder-permissions-list')
                ->setUrl('/list_permissions')
                ->setUrlMethod('get')
                ->setUrlController('App\Livewire\AdminGeneral\UsuariosYPermisos\PermissionsList')
                ->setRoles(['admin_general'])
                ->setItems([])
                ->setEndBlock('list_permissions'),

            RkRoute::make('sessionlist_p6D')
                ->setParentId('grupo_auth')
                ->setAccessPermission('acceder-sessionlist_p6D')
                ->setUrl('/sessionlist_p6D')
                ->setUrlMethod('get')
                ->setUrlController('App\Livewire\AdminGeneral\UsuariosYPermisos\UserSessionList')
                ->setRoles(['admin_general'])
                ->setItems([])
                ->setEndBlock('sessionlist_p6D'),

  
        ])
        ->setEndBlock('grupo_auth'),
];
