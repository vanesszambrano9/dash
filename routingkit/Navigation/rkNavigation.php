<?php

use Rk\RoutingKit\Entities\RkNavigation;

return [

    RkNavigation::makeGroup('dashboard_group')
        ->setDescription('Panel principal del sistema')
        ->setLabel('Inicio')
        ->setHeroIcon('home')
        ->setItems([

            RkNavigation::makeSimple('dashboard')
                ->setParentId('dashboard_group')
                ->setUrl('/dashboard')
                ->setDescription('Accede al panel principal')
                ->setLabel('Dashboard')
                ->setHeroIcon('home')
                ->setItems([])
                ->setEndBlock('dashboard'),
                
            RkNavigation::makeSimple('dashboard')
                ->setParentId('dashboard_group')
                ->setUrl('/dashboard')
                ->setDescription('Accede al panel principal')
                ->setLabel('Dashboard')
                ->setHeroIcon('home')
                ->setItems([])
                ->setEndBlock('dashboard'),

        ])
        ->setEndBlock('dashboard_group'),

];