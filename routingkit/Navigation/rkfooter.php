<?php

use Rk\RoutingKit\Entities\RkNavigation;

return [

    RkNavigation::makeGroup('configuracion_grupo')
        ->setLabel('Configuración')
        ->setHeroIcon('cog-6-tooth')
        ->setItems([

            RkNavigation::makeSimple('profile.edit')
                ->setParentId('configuracion_grupo')
                ->setUrl('http://localhost:8000/settings/profile')
                ->setLabel('Perfil')
                ->setHeroIcon('user-circle')
                ->setItems([])
                ->setEndBlock('profile.edit'),

            RkNavigation::makeSimple('user-password.edit')
                ->setParentId('configuracion_grupo')
                ->setUrl('http://localhost:8000/settings/password')
                ->setLabel('Contraseña')
                ->setHeroIcon('lock-closed')
                ->setItems([])
                ->setEndBlock('user-password.edit'),

            RkNavigation::makeSimple('two-factor.show')
                ->setParentId('configuracion_grupo')
                ->setUrl('http://localhost:8000/settings/two-factor')
                ->setLabel('Doble Autenticación')
                ->setHeroIcon('finger-print')
                ->setItems([])
                ->setEndBlock('two-factor.show'),

            RkNavigation::makeSimple('appearance.edit')
                ->setParentId('configuracion_grupo')
                ->setUrl('http://localhost:8000/settings/appearance')
                ->setLabel('Apariencia')
                ->setHeroIcon('paint-brush')
                ->setItems([])
                ->setEndBlock('appearance.edit'),
        ])
        ->setEndBlock('configuracion_grupo'),

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

    RkNavigation::makeLink('ayuda')
        ->setUrl('https://wa.me/+50499217206')
        ->setLabel('Ayuda')
        ->setHeroIcon('question-mark-circle')
        ->setItems([])
        ->setEndBlock('ayuda'),
];
