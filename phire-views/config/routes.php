<?php

return [
    APP_URI => [
        '/views[/]' => [
            'controller' => 'Phire\Views\Controller\IndexController',
            'action'     => 'index',
            'acl'        => [
                'resource'   => 'views',
                'permission' => 'index'
            ]
        ],
        '/views/add' => [
            'controller' => 'Phire\Views\Controller\IndexController',
            'action'     => 'add',
            'acl'        => [
                'resource'   => 'views',
                'permission' => 'add'
            ]
        ],
        '/views/edit/:id' => [
            'controller' => 'Phire\Views\Controller\IndexController',
            'action'     => 'edit',
            'acl'        => [
                'resource'   => 'views',
                'permission' => 'edit'
            ]
        ],
        '/views/json/:id[/:tid][/:vid]' => [
            'controller' => 'Phire\Views\Controller\IndexController',
            'action'     => 'json',
            'acl'        => [
                'resource'   => 'views',
                'permission' => 'json'
            ]
        ],
        '/views/remove' => [
            'controller' => 'Phire\Views\Controller\IndexController',
            'action'     => 'remove',
            'acl'        => [
                'resource'   => 'views',
                'permission' => 'remove'
            ]
        ]
    ]
];
