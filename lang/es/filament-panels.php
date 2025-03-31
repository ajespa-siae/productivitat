<?php

return [
    'pages' => [
        'dashboard' => [
            'title' => 'Panel',
        ],
    ],
    'resources' => [
        'pages' => [
            'create' => [
                'title' => 'Crear :label',
                'buttons' => [
                    'create' => [
                        'label' => 'Crear',
                    ],
                ],
            ],
            'edit' => [
                'title' => 'Editar :label',
                'buttons' => [
                    'save' => [
                        'label' => 'Guardar cambios',
                    ],
                ],
            ],
            'list' => [
                'title' => ':label',
            ],
        ],
    ],
    'widgets' => [
        'account' => [
            'label' => 'Cuenta',
        ],
        'filament-info' => [
            'label' => 'Información de Filament',
        ],
    ],
];
