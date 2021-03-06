<?php
/**
 * phire-views form configuration
 */
return [
    'Phire\Views\Form\View' => [
        [
            'submit' => [
                'type'       => 'submit',
                'value'      => 'Save',
                'attributes' => [
                    'class'  => 'save-btn wide'
                ]
            ],
            'group_style' => [
                'type'  => 'select',
                'label' => 'Group Style',
                'value' => [
                    'table'   => 'Table',
                    'ul'      => 'Bullet List',
                    'ol'      => 'Numbered List',
                    'div'     => 'Div'
                ]
            ],
            'group_headers' => [
                'type'  => 'checkbox',
                'value' => [
                    '1' => 'Group Headers'
                ],
                'marked' => 1
            ],
            'single_style' => [
                'type'  => 'select',
                'label' => 'Single Style',
                'value' => [
                    'table'   => 'Table',
                    'ul'      => 'Bullet List',
                    'ol'      => 'Numbered List',
                    'div'     => 'Div'
                ]
            ],
            'single_headers' => [
                'type'  => 'checkbox',
                'value' => [
                    '1' => 'Single Headers'
                ],
                'marked' => 1
            ],
            'id' => [
                'type'  => 'hidden',
                'value' => 0
            ]
        ],
        [
            'name' => [
                'type'       => 'text',
                'label'      => 'Name',
                'required'   => true,
                'attributes' => [
                    'size'  => 60,
                    'style' => 'width: 99.5%'
                ]
            ]
        ],
        [
            'model_1' => [
                'type'       => 'select',
                'label'      => 'View Model &amp; Type',
                'required'   => true,
                'value'      => ['----' => '----'],
                'attributes' => [
                    'onchange' => 'phire.getViewModelTypes(this);'
                ],
                'validators' => new \Pop\Validator\NotEqual('----', 'You must select a model.')
            ],
            'model_type_1' => [
                'type'       => 'select',
                'value'      => ['----' => '----'],
                'required'   => true,
                'attributes' => [
                    'onchange' => 'phire.selectViewModelType(this);'
                ],
                'validators' => new \Pop\Validator\NotEqual('----', 'You must select a type.')
            ]
        ],
        [
            'group_fields' => [
                'type'  => 'checkbox',
                'label' => 'Group Fields<br />[ <a class="small-link" href="#" onclick="jax(\'#view-form\').checkAll(\'group_fields\'); return false;">All</a> | <a class="small-link" href="#" onclick="jax(\'#view-form\').uncheckAll(\'group_fields\'); return false;">None</a> | <a class="small-link" href="#" onclick="jax(\'#view-form\').checkInverse(\'group_fields\'); return false;">Invert</a> ]',
                'value' => [
                    'id'    => 'id',
                    'title' => 'title'
                ]
            ]
        ],
        [
            'single_fields' => [
                'type'  => 'checkbox',
                'label' => 'Single Fields<br />[ <a class="small-link" href="#" onclick="jax(\'#view-form\').checkAll(\'single_fields\'); return false;">All</a> | <a class="small-link" href="#" onclick="jax(\'#view-form\').uncheckAll(\'single_fields\'); return false;">None</a> | <a class="small-link" href="#" onclick="jax(\'#view-form\').checkInverse(\'single_fields\'); return false;">Invert</a> ]',
                'value' => [
                    'id'    => 'id',
                    'title' => 'title'
                ]
            ]
        ]
    ]
];
