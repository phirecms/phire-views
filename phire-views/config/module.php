<?php
/**
 * Module Name: phire-views
 * Author: Nick Sagona
 * Description: This is the views module for Phire CMS 2
 * Version: 1.0
 */
return [
    'phire-views' => [
        'prefix'     => 'Phire\Views\\',
        'src'        => __DIR__ . '/../src',
        'routes'     => include 'routes.php',
        'resources'  => include 'resources.php',
        'forms'      => include 'forms.php',
        'nav.phire'  => [
            'views' => [
                'name' => 'Views',
                'href' => '/views',
                'acl' => [
                    'resource'   => 'views',
                    'permission' => 'index'
                ],
                'attributes' => [
                    'class' => 'views-nav-icon'
                ]
            ]
        ],
        'events' => [
            [
                'name'     => 'app.send.pre',
                'action'   => 'Phire\Views\Event\View::init',
                'priority' => 500
            ],
            [
                'name'     => 'app.send.post',
                'action'   => 'Phire\Views\Event\View::parseViews',
                'priority' => 1000
            ]
        ]
    ]
];
