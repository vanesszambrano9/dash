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

            RkRoute::make('menu-item')
                ->setParentId('productos_group')
                ->setAccessPermission('producto.menu-item')
                ->setPermissions([
                    'menu-item.ver', 
                    'menu-item.editar', 
                    'menu-item.crear', 
                    'menu-item.eliminar', 
                    'productos_group'
                ])
                ->setUrlMethod('get')
                ->setUrlController('App\Livewire\Producto\MenuItem')
                ->setRoles(['super_admin', 'admin'])
                ->setItems([])
                ->setEndBlock('menu-item'),
    
        // Ventas activas (cards)
        // En tu archivo de configuración de rutas (ej: config/routes.php)

        RkRoute::make('ventas')  // ← Nombre debe coincidir con navegación
            ->setParentId('ventas_group')
            ->setAccessPermission('venta.ventas.ver')
            ->setPermissions([
                'venta.ventas.ver',
                'venta.ventas.crear',
                'venta.ventas.editar',
                'venta.ventas.eliminar',
                'venta.ventas.cerrar',
            ])
            ->setUrlMethod('get')
            ->setUrl('/ventas')  // ← URL explícita
            ->setUrlController('App\Livewire\Venta\Sale')  // ← Tu componente Livewire
            ->setRoles(['super_admin', 'admin', 'cajero'])
            ->setItems([])
        ->setEndBlock('ventas'),

        RkRoute::make('ventas-historial')
            ->setParentId('ventas_group')
            ->setAccessPermission('venta.historial.ver')
            ->setUrlMethod('get')
            ->setUrl('/ventas/historial')
            ->setUrlController('App\Livewire\Venta\SaleHistory')
            ->setRoles(['super_admin', 'admin', 'cajero'])
            ->setItems([])
        ->setEndBlock('ventas-historial'),

        RkRoute::make('venta-item')  
            ->setParentId('ventas_group')
            ->setAccessPermission('venta.item.gestionar')
            ->setPermissions([
                'venta.item.ver',
                'venta.item.crear',
                'venta.item.editar',
                'venta.item.eliminar',
            ])
            ->setUrlMethod('get')
            ->setUrl('/venta-item/{sale}')  
            ->setUrlController('App\Livewire\Venta\SaleItem')
            ->setRoles(['super_admin', 'admin', 'cajero'])
            ->setItems([])
        ->setEndBlock('venta-item'),
            
        RkRoute::make('inventario')
            ->setParentId('inventario_group')
            ->setAccessPermission('inventario.inventario')
            ->setPermissions([
                'inventario.ver', 
                'inventario.editar', 
                'inventario.crear', 
                'inventario.eliminar', 
                'inventario_group'
            ])
            ->setUrlMethod('get')
            ->setUrlController('App\Livewire\Inventario\StockMovement')
            ->setRoles(['super_admin', 'admin'])
            ->setItems([])
        ->setEndBlock('inventario'),
  
  
        ])
        ->setEndBlock('grupo_auth'),
];
