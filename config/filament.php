<?php

return [
    'navigation' => [
        'collapsible_groups' => true,
        'collapsed_groups' => ['Administration'],
    ],
    
    'layout' => [
        'sidebar' => [
            'is_collapsible_on_desktop' => true,
            'groups' => [
                'are_collapsible' => true,
                'collapsed_by_default' => ['Administration'],
            ],
        ],
    ],
];
