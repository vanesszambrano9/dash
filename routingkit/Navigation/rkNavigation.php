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
                    
                    RkNavigation::makeSimple('menu-item')
                        ->setParentId('productos_group')
                        ->setLabel('Menú')
                        ->setHeroIcon('queue-list')
                        ->setItems([])
                    ->setEndBlock('menu-item'),

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

            RkNavigation::makeGroup('ventas_group')
                ->setParentId('dashboard_group')
                ->setLabel('Ventas')
                ->setHeroIcon('document-currency-dollar')
                ->setItems([

                    RkNavigation::makeSimple('ventas')
                        ->setParentId('ventas_group')
                        ->setLabel('Ventas Activas')
                        ->setHeroIcon('currency-dollar')
                        ->setUrl('/ventas')  // ← Agregar URL explícita
                        ->setItems([])
                    ->setEndBlock('ventas'),

                    RkNavigation::makeSimple('ventas-historial')
                        ->setParentId('ventas_group')
                        ->setLabel('Historial de Ventas')
                        ->setHeroIcon('clipboard-document-list')
                        ->setUrl('/ventas/historial')
                        ->setItems([])
                    ->setEndBlock('ventas-historial'),

                ])
            ->setEndBlock('ventas_group'),

            RkNavigation::makeGroup('inventario_group')
                ->setParentId('dashboard_group')
                ->setLabel('Inventario')
                ->setHeroIcon('archive-box')
                ->setItems([

                    RkNavigation::makeSimple('inventario')
                        ->setParentId('inventario_group')
                        ->setLabel('Inventario')
                        ->setHeroIcon('archive-box')
                        ->setItems([])
                    ->setEndBlock('inventario'),

                ])
            ->setEndBlock('inventario_group'),

        ])
        ->setEndBlock('dashboard_group'),
];
