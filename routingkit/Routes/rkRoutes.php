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

            RkRoute::make('categoria')
                ->setParentId('productos_group')
                ->setAccessPermission('producto.categoria')
                ->setPermissions([
                    'categoria.ver', 
                    'categoria.editar', 
                    'categoria.crear', 
                    'categoria.eliminar', 
                    'productos_group'
                ])
                ->setUrlMethod('get')
                ->setUrlController('App\Livewire\Categoria\Categoria')
                ->setRoles(['super_admin', 'admin'])
                ->setItems([])
                ->setEndBlock('categoria'),

            RkRoute::make('proveedor')
                ->setParentId('productos_group')
                ->setAccessPermission('producto.proveedor')
                ->setPermissions([
                    'proveedor.ver', 
                    'proveedor.editar', 
                    'proveedor.crear', 
                    'proveedor.eliminar', 
                    'productos_group'
                ])
                ->setUrlMethod('get')
                ->setUrlController('App\Livewire\Proveedor\Proveedor')
                ->setRoles(['super_admin', 'admin'])
                ->setItems([])
                ->setEndBlock('proveedor'),

             RkRoute::make('producto')
                ->setParentId('productos_group')
                ->setAccessPermission('producto.producto')
                ->setPermissions([
                    'producto.ver', 
                    'producto.editar', 
                    'producto.crear', 
                    'producto.eliminar', 
                    'productos_group'
                ])
                ->setUrlMethod('get')
                ->setUrlController('App\Livewire\Producto\Producto')
                ->setRoles(['super_admin', 'admin'])
                ->setItems([])
                ->setEndBlock('producto'),
  
  
        ])
        ->setEndBlock('grupo_auth'),
];
