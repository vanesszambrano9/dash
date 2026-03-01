<?php

use Rk\RoutingKit\Entities\RkNavigation;

return [

    RkNavigation::makeGroup('dashboard_group')
        ->setLabel('Administration General')
        ->setHeroIcon('home')
        ->setItems([

            RkNavigation::makeGroup('dashboard_module')
                ->setParentId('dashboard_group')
                ->setLabel('Dashboard')
                ->setHeroIcon('home')
                ->setItems([

                    RkNavigation::makeSimple('dashboard')
                        ->setParentId('dashboard_module')
                        ->setLabel('Dashboard')
                        ->setHeroIcon('plus')
                        ->setItems([])
                        ->setEndBlock('dashboard'),
                ])
                ->setEndBlock('dashboard_module'),

            RkNavigation::makeGroup('productos_group')
                ->setParentId('dashboard_group')
                ->setLabel('Productos')
                ->setHeroIcon('shopping-bag')
                ->setItems([

                    RkNavigation::makeSimple('producto')
                        ->setParentId('productos_group')
                        ->setLabel('Productos')
                        ->setHeroIcon('cube')
                        ->setItems([])
                        ->setEndBlock('producto'),

                    RkNavigation::makeSimple('categoria')
                        ->setParentId('productos_group')
                        ->setLabel('Categorías')
                        ->setHeroIcon('tag')
                        ->setItems([])
                        ->setEndBlock('categoria'),

                    RkNavigation::makeSimple('proveedor')
                        ->setParentId('productos_group')
                        ->setLabel('Proveedores')
                        ->setHeroIcon('truck')
                        ->setItems([])
                        ->setEndBlock('proveedor'),
                ])
                ->setEndBlock('productos_group'),

          

            RkNavigation::makeGroup('usuarios_permisos_group')
                ->setParentId('dashboard_group')
                ->setLabel('Acceso')
                ->setHeroIcon('users')
                ->setItems([

                    RkNavigation::make('userlist_Iac')
                        ->setParentId('usuarios_permisos_group')
                        ->setLabel('Listado de Usuarios')
                        ->setHeroIcon('user')
                        ->setItems([])
                        ->setEndBlock('userlist_Iac'),

                    RkNavigation::make('list_roles')
                        ->setParentId('usuarios_permisos_group')
                        ->setLabel('Listado de Roles')
                        ->setHeroIcon('key')
                        ->setItems([])
                        ->setEndBlock('list_roles'),

                    RkNavigation::make('list_permissions')
                        ->setParentId('usuarios_permisos_group')
                        ->setLabel('Listado de Permisos')
                        ->setHeroIcon('lock-closed')
                        ->setItems([])
                        ->setEndBlock('list_permissions'),

                    RkNavigation::make('sessionlist_p6D')
                        ->setParentId('usuarios_permisos_group')
                        ->setLabel('Sesiones Activas')
                        ->setBageInt(1)
                        ->setHeroIcon('computer-desktop')
                        ->setItems([])
                        ->setEndBlock('sessionlist_p6D'),
                ])
                ->setEndBlock('usuarios_permisos_group'),
        ])
        ->setEndBlock('dashboard_group'),
];
