<?php

use Rk\RoutingKit\Entities\RkNavigation;

return [

    RkNavigation::makeLink('ayuda')
        ->setUrl('https://wa.me/+50497077088')
        ->setLabel('Ayuda')
        ->setHeroIcon('question-mark-circle')
        ->setItems([])
        ->setEndBlock('ayuda'),

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
];
