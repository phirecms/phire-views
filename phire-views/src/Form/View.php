<?php

namespace Phire\Views\Form;

use Pop\Form\Form;
use Pop\Validator;

class View extends Form
{

    /**
     * Constructor
     *
     * Instantiate the form object
     *
     * @param  array  $fields
     * @param  string $action
     * @param  string $method
     * @return View
     */
    public function __construct(array $fields = null, $action = null, $method = 'post')
    {
        parent::__construct($fields, $action, $method);
        $this->setAttribute('id', 'view-form');
        $this->setIndent('    ');
    }

}