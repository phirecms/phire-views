<?php

namespace Phire\Views\Form;

use Phire\Views\Table;
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

    /**
     * Set the field values
     *
     * @param  array $values
     * @return View
     */
    public function setFieldValues(array $values = null)
    {
        parent::setFieldValues($values);

        if (($_POST) && (null !== $this->name)) {
            // Check for dupe name
            $view = Table\Views::findBy(['name' => $this->name]);
            if (isset($view->id) && ($this->id != $view->id)) {
                $this->getElement('name')
                     ->addValidator(new Validator\NotEqual($this->name, 'That view name already exists.'));
            }
        }

        return $this;
    }

}