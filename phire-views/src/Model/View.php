<?php

namespace Phire\Views\Model;

use Phire\Views\Table;
use Phire\Model\AbstractModel;
use Pop\Dom\Child;

class View extends AbstractModel
{

    /**
     * Get all views
     *
     * @param  int    $limit
     * @param  int    $page
     * @param  string $sort
     * @return array
     */
    public function getAll($limit = null, $page = null, $sort = null)
    {
        $order = $this->getSortOrder($sort, $page);

        if (null !== $limit) {
            $page = ((null !== $page) && ((int)$page > 1)) ?
                ($page * $limit) - $limit : null;

            return Table\Views::findAll([
                'offset' => $page,
                'limit'  => $limit,
                'order'  => $order
            ])->rows();
        } else {
            return Table\Views::findAll([
                'order'  => $order
            ])->rows();
        }
    }

    /**
     * Get view by ID
     *
     * @param  int $id
     * @return void
     */
    public function getById($id)
    {
        $view = Table\Views::findById($id);
        if (isset($view->id)) {
            $data = $view->getColumns();
            if (!empty($data['group_fields'])) {
                $data['group_fields']       = explode('|', $data['group_fields']);
                $data['group_fields_names'] = [];
                foreach ($data['group_fields'] as $id) {
                    if (!is_numeric($id)) {
                        $data['group_fields_names'][] = $id;
                    } else {
                        $f = \Phire\Fields\Table\Fields::findById($id);
                        if (isset($f->id)) {
                            $data['group_fields_names'][] = $f->name;
                        }
                    }
                }
            }
            if (!empty($data['single_fields'])) {
                $data['single_fields']       = explode('|', $data['single_fields']);
                $data['single_fields_names'] = [];
                foreach ($data['single_fields'] as $id) {
                    if (!is_numeric($id)) {
                        $data['single_fields_names'][] = $id;
                    } else {
                        $f = \Phire\Fields\Table\Fields::findById($id);
                        if (isset($f->id)) {
                            $data['single_fields_names'][] = $f->name;
                        }
                    }
                }
            }

            if (!empty($data['models'])) {
                $data['models'] = unserialize($data['models']);
            }

            $this->data = array_merge($this->data, $data);
        }
    }

    /**
     * Get view by name
     *
     * @param  string $name
     * @return void
     */
    public function getByName($name)
    {
        $view = Table\Views::findBy(['name' => $name]);
        if (isset($view->id)) {
            $this->getById($view->id);
        }
    }

    /**
     * Render pages for group view
     *
     * @param  string $name
     * @return mixed
     */
    public function renderPages($name)
    {
        if (is_numeric($name)) {
            $this->getById($name);
        } else {
            $this->getByName($name);
        }

        if (isset($this->data['models'][0]) && isset($this->data['models'][0]['model'])) {
            $model = $this->data['models'][0]['model'];
            if ((null !== $this->data['models'][0]['type_field']) && (null !== $this->data['models'][0]['type_value'])) {
                $params = [
                    $this->data['models'][0]['type_field'] => $this->data['models'][0]['type_value']
                ];
                $objects = \Phire\Fields\Model\FieldValue::getModelObjects($model, $params);

                if (count($objects) > $this->data['pagination']) {
                    $limit = $this->data['pagination'];
                    $pages = new \Pop\Paginator\Paginator(count($objects), $limit);
                    $pages->useInput(true);

                    return $pages;
                } else {
                    return null;
                }
            } else {
                return null;
            }
        } else {
            return null;
        }
    }

    /**
     * Render group view
     *
     * @param  string $name
     * @param  string $dateFormat
     * @param  array  $filters
     * @param  array  $conds
     * @return mixed
     */
    public function render($name, $dateFormat = null, array $filters = [], array $conds = [])
    {
        if (is_numeric($name)) {
            $this->getById($name);
        } else {
            $this->getByName($name);
        }

        if (isset($this->data['models'][0]) && isset($this->data['models'][0]['model'])) {
            $model = $this->data['models'][0]['model'];
            if ((null !== $this->data['models'][0]['type_field']) && (null !== $this->data['models'][0]['type_value'])) {
                $params = [
                    $this->data['models'][0]['type_field'] => $this->data['models'][0]['type_value']
                ];
                $objects = \Phire\Fields\Model\FieldValue::getModelObjects($model, $params, 'getAll', $filters);

                if (count($objects) > $this->data['pagination']) {
                    $page  = (!empty($_GET['page'])) ? (int)$_GET['page'] : null;
                    $limit = $this->data['pagination'];
                    $offset = ((null !== $page) && ((int)$page > 1)) ?
                        ($page * $limit) - $limit : 0;
                    $objects = array_slice($objects, $offset, $limit);
                }

                return $this->build($objects, $dateFormat);
            } else {
                return null;
            }
        } else {
            return null;
        }
    }

    /**
     * Render single view
     *
     * @param  string $name
     * @param  int    $id
     * @param  string $dateFormat
     * @param  array  $filters
     * @return mixed
     */
    public function renderSingle($name, $id, $dateFormat = null, array $filters = [])
    {
        if (is_numeric($name)) {
            $this->getById($name);
        } else {
            $this->getByName($name);
        }

        if (isset($this->data['models'][0]) && isset($this->data['models'][0]['model'])) {
            $model = $this->data['models'][0]['model'];
            if ((null !== $this->data['models'][0]['type_field']) && (null !== $this->data['models'][0]['type_value'])) {
                $object = \Phire\Fields\Model\FieldValue::getModelObject($model, ['id' => $id], 'getById', $filters);

                return $this->buildSingle($object, $dateFormat);
            } else {
                return null;
            }
        } else {
            return null;
        }
    }

    /**
     * Build group view
     *
     * @param  array  $objects
     * @param  string $dateFormat
     * @throws \Phire\Exception
     * @return mixed
     */
    public function build(array $objects, $dateFormat = null)
    {
        if (!isset($this->data['id'])) {
            throw new \Phire\Exception('Error: A view has not been selected.');
        }

        $view = null;

        if (isset($objects[0]) && (is_array($objects[0]) || ($objects[0] instanceof \ArrayObject))) {
            $viewName = str_replace(' ', '-', strtolower($this->data['name']));

            switch ($this->data['group_style']) {
                case 'table':
                    $view = new Child('table');
                    $view->setAttributes([
                        'id'    => $viewName . '-view-' . $this->data['id'],
                        'class' => $viewName . '-view',
                    ]);

                    $linkField = $this->hasLinkField($this->data['group_fields_names']);
                    $dateField = $this->hasDateField($this->data['group_fields_names']);

                    if ($this->data['group_headers']) {
                        $tr = new Child('tr');
                        foreach ($this->data['group_fields_names'] as $field) {
                            if ($field !== $linkField) {
                                $tr->addChild(new Child('th', ucwords(str_replace(['_', '-'], [' ', ' '], $field))));
                            }
                        }
                        $view->addChild($tr);
                    }
                    foreach ($objects as $object) {
                        $tr = new Child('tr');
                        foreach ($this->data['group_fields_names'] as $field) {
                            if (($field == 'title') && (null !== $linkField) && isset($object[$field]) && isset($object[$linkField])) {
                                $td = new Child('td');
                                $a  = new Child('a', $object[$field]);
                                $a->setAttribute('href', $object[$linkField]);
                                $td->addChild($a);
                                $tr->addChild($td);
                            } else if ($field !== $linkField) {
                                if (isset($object[$field])) {
                                    if (($field === $dateField) && (null !== $dateFormat)) {
                                        $value = date($dateFormat, strtotime($object[$field]));
                                    } else {
                                        $value = $object[$field];
                                    }
                                } else {
                                    $value = '&nbsp;';
                                }
                                $tr->addChild(new Child('td', $value));
                            }
                        }
                        $view->addChild($tr);
                    }

                    break;

                case 'ul':
                case 'ol':
                    $view = new Child('div');
                    $view->setAttributes([
                        'id'    => $viewName . '-view-' . $this->data['id'],
                        'class' => $viewName . '-view',
                    ]);

                    $linkField = $this->hasLinkField($this->data['group_fields_names']);
                    $dateField = $this->hasDateField($this->data['group_fields_names']);

                    foreach ($objects as $object) {
                        $list = ($this->data['group_style'] == 'ol') ? new Child('ol') : new Child('ul');
                        foreach ($this->data['group_fields_names'] as $field) {
                            $li = new Child('li', null, null, true);

                            if (($this->data['group_headers']) && ($field !== $linkField)) {
                                $li->addChild(new Child('strong', ucwords(str_replace(['_', '-'], [' ', ' '], $field)) . ':'));
                            }
                            if (($field == 'title') && (null !== $linkField) && isset($object[$field]) && isset($object[$linkField])) {
                                $a = new Child('a', $object[$field]);
                                $a->setAttribute('href', $object[$linkField]);
                                $li->addChild($a);
                                $list->addChild($li);
                            } else if ($field !== $linkField) {
                                if (isset($object[$field])) {
                                    if (($field === $dateField) && (null !== $dateFormat)) {
                                        $value = date($dateFormat, strtotime($object[$field]));
                                    } else {
                                        $value = $object[$field];
                                    }
                                } else {
                                    $value = '&nbsp;';
                                }
                                $li->setNodeValue($value);
                                $list->addChild($li);
                            }

                        }

                        $view->addChild($list);
                    }

                    break;

                case 'div':
                    $view = new Child('div');
                    $view->setAttributes([
                        'id'    => $viewName . '-view-' . $this->data['id'],
                        'class' => $viewName . '-view',
                    ]);

                    $linkField = $this->hasLinkField($this->data['group_fields_names']);
                    $dateField = $this->hasDateField($this->data['group_fields_names']);

                    foreach ($objects as $object) {
                        $section   = new Child('section');
                        if (in_array('title', $this->data['group_fields_names']) && isset($object['title'])) {
                            if ((null !== $linkField) && isset($object[$linkField])) {
                                $h2 = new Child('h2');
                                $a  = new Child('a', $object['title']);
                                $a->setAttribute('href', $object[$linkField]);
                                $h2->addChild($a);
                            } else {
                                $h2 = new Child('h2', $object['title']);
                            }
                            $section->addChild($h2);
                        }
                        if ((null !== $dateField) && (null !== $dateFormat)) {
                            if (in_array($dateField, $this->data['group_fields_names']) && isset($object[$dateField])) {
                                $section->addChild(new Child('h5', date($dateFormat, strtotime($object[$dateField]))));
                            }
                        }
                        foreach ($this->data['group_fields_names'] as $field) {
                            if (($field !== 'title') && (($field !== $dateField) || (null === $dateFormat)) && ($field !== $linkField)) {
                                $value = (isset($object[$field]) ? $object[$field] : '&nbsp;');
                                if ($this->data['group_headers']) {
                                    $value = '<strong>' . ucwords(str_replace(['_', '-'], [' ', ' '], $field)) . '</strong>: ' . $value;
                                }
                                $section->addChild(new Child('p', $value));
                            }
                        }
                        $view->addChild($section);
                    }

                    break;
            }

        }

        return $view;
    }

    /**
     * Build single view
     *
     * @param  mixed  $object
     * @param  string $dateFormat
     * @throws \Phire\Exception
     * @return mixed
     */
    public function buildSingle($object, $dateFormat = null)
    {
        if (!isset($this->data['id'])) {
            throw new \Phire\Exception('Error: A view has not been selected.');
        }

        $view = null;

        $viewName = str_replace(' ', '-', strtolower($this->data['name']));

        switch ($this->data['single_style']) {
            case 'table':
                $view = new Child('table');
                $view->setAttributes([
                    'id'    => $viewName . '-single-view-' . $this->data['id'],
                    'class' => $viewName . '-single-view',
                ]);

                $linkField = $this->hasLinkField($this->data['single_fields_names']);
                $dateField = $this->hasDateField($this->data['single_fields_names']);

                foreach ($this->data['single_fields_names'] as $field) {
                    if ($field !== $linkField) {
                        $tr = new Child('tr');
                        if ($this->data['single_headers']) {
                            $tr->addChild(new Child('th', ucwords(str_replace(['_', '-'], [' ', ' '], $field)) . ':'));
                        }
                        if (($field == 'title') && (null !== $linkField) && isset($object[$field]) && isset($object[$linkField])) {
                            $td = new Child('td');
                            $a = new Child('a', $object[$field]);
                            $a->setAttribute('href', $object[$linkField]);
                            $td->addChild($a);
                            $tr->addChild($td);
                        } else if ($field !== $linkField) {
                            if (isset($object[$field])) {
                                if (($field === $dateField) && (null !== $dateFormat)) {
                                    $value = date($dateFormat, strtotime($object[$field]));
                                } else {
                                    $value = $object[$field];
                                }
                            } else {
                                $value = '&nbsp;';
                            }
                            $tr->addChild(new Child('td', $value));
                        }
                        $view->addChild($tr);
                    }
                }

                break;

            case 'ul':
            case 'ol':
                $view = new Child('div');
                $view->setAttributes([
                    'id'    => $viewName . '-single-view-' . $this->data['id'],
                    'class' => $viewName . '-single-view',
                ]);

                $linkField = $this->hasLinkField($this->data['single_fields_names']);
                $dateField = $this->hasDateField($this->data['single_fields_names']);

                $list = ($this->data['single_style'] == 'ol') ? new Child('ol') : new Child('ul');
                foreach ($this->data['single_fields_names'] as $field) {
                    $li = new Child('li', null, null, true);
                                 if (($this->data['single_headers']) && ($field !== $linkField)) {
                        $li->addChild(new Child('strong', ucwords(str_replace(['_', '-'], [' ', ' '], $field)) . ':'));
                    }
                    if (($field == 'title') && (null !== $linkField) && isset($object[$field]) && isset($object[$linkField])) {
                        $a = new Child('a', $object[$field]);
                        $a->setAttribute('href', $object[$linkField]);
                        $li->addChild($a);
                        $list->addChild($li);
                    } else if ($field !== $linkField) {
                        if (isset($object[$field])) {
                            if (($field === $dateField) && (null !== $dateFormat)) {
                                $value = date($dateFormat, strtotime($object[$field]));
                            } else {
                                $value = $object[$field];
                            }
                        } else {
                            $value = '&nbsp;';
                        }
                        $li->setNodeValue($value);
                        $list->addChild($li);
                    }
                }

                    $view->addChild($list);

                break;

            case 'div':
                $view = new Child('div');
                $view->setAttributes([
                    'id'    => $viewName . '-single-view-' . $this->data['id'],
                    'class' => $viewName . '-single-view',
                ]);

                $linkField = $this->hasLinkField($this->data['single_fields_names']);
                $dateField = $this->hasDateField($this->data['single_fields_names']);

                $section   = new Child('section');
                if (in_array('title', $this->data['single_fields_names']) && isset($object['title'])) {
                    if ((null !== $linkField) && isset($object[$linkField])) {
                        $h2 = new Child('h2');
                        $a  = new Child('a', $object['title']);
                        $a->setAttribute('href', $object[$linkField]);
                        $h2->addChild($a);
                    } else {
                        $h2 = new Child('h2', $object['title']);
                    }
                    $section->addChild($h2);
                }
                if ((null !== $dateField) && (null !== $dateFormat)) {
                    if (in_array($dateField, $this->data['single_fields_names']) && isset($object[$dateField])) {
                        $section->addChild(new Child('h5', date($dateFormat, strtotime($object[$dateField]))));
                    }
                }
                foreach ($this->data['single_fields_names'] as $field) {
                    if (($field !== 'title') && (($field !== $dateField) || (null === $dateFormat)) && ($field !== $linkField)) {
                        $value = (isset($object[$field]) ? $object[$field] : '&nbsp;');
                        if ($this->data['single_headers']) {
                            $value = '<strong>' . ucwords(str_replace(['_', '-'], [' ', ' '], $field)) . '</strong>: ' . $value;
                        }
                        $section->addChild(new Child('p', $value));
                    }
                }
                $view->addChild($section);

                break;
        }

        return $view;
    }

    /**
     * Save new field
     *
     * @param  array $fields
     * @return void
     */
    public function save(array $fields)
    {
        $view = new Table\Views([
            'name'           => $fields['name'],
            'group_fields'   => (!empty($fields['group_fields'])) ? implode('|', $fields['group_fields']) : null,
            'group_style'    => (!empty($fields['group_style'])) ? $fields['group_style'] : null,
            'group_headers'  => (isset($_POST['group_headers']) && isset($_POST['group_headers'][0])) ? 1 : 0,
            'single_fields'  => (!empty($fields['single_fields'])) ? implode('|', $fields['single_fields']) : null,
            'single_style'   => (!empty($fields['single_style'])) ? $fields['single_style'] : null,
            'single_headers' => (isset($_POST['single_headers']) && isset($_POST['single_headers'][0])) ? 1 : 0,
            'models'         => serialize($this->getModels())
        ]);
        $view->save();

        $this->data = array_merge($this->data, $view->getColumns());
    }

    /**
     * Update an existing field
     *
     * @param  array $fields
     * @return void
     */
    public function update(array $fields)
    {
        $view = Table\Views::findById($fields['id']);
        if (isset($view->id)) {
            $view->name           = $fields['name'];
            $view->group_fields   = (!empty($fields['group_fields'])) ? implode('|', $fields['group_fields']) : null;
            $view->group_style    = (!empty($fields['group_style'])) ? $fields['group_style'] : null;
            $view->group_headers  = (isset($_POST['group_headers']) && isset($_POST['group_headers'][0])) ? 1 : 0;
            $view->single_fields  = (!empty($fields['single_fields'])) ? implode('|', $fields['single_fields']) : null;
            $view->single_style   = (!empty($fields['single_style'])) ? $fields['single_style'] : null;
            $view->single_headers = (isset($_POST['single_headers']) && isset($_POST['single_headers'][0])) ? 1 : 0;
            $view->models         = serialize($this->getModels());
            $view->save();

            $this->data = array_merge($this->data, $view->getColumns());
        }
    }

    /**
     * Remove a view
     *
     * @param  array $fields
     * @return void
     */
    public function remove(array $fields)
    {
        if (isset($fields['rm_views'])) {

            foreach ($fields['rm_views'] as $id) {
                $view = Table\Views::findById((int)$id);
                if (isset($view->id)) {
                    $view->delete();
                }
            }
        }
    }

    /**
     * Determine if list of views has pages
     *
     * @param  int $limit
     * @return boolean
     */
    public function hasPages($limit)
    {
        return (Table\Views::findAll()->count() > $limit);
    }

    /**
     * Get count of views
     *
     * @return int
     */
    public function getCount()
    {
        return Table\Views::findAll()->count();
    }

    /**
     * Determine if a field is a date field
     *
     * @param  string $field
     * @return boolean
     */
    public function isDateField($field)
    {
        $result = false;

        if (strtolower($field) == 'publish') {
            $result = true;
        } else if (strtolower($field) == 'published') {
            $result = true;
        } else if (strtolower($field) == 'created') {
            $result = true;
        } else if (stripos($field, 'date') !== false) {
            $result = true;
        }

        return $result;
    }

    /**
     * Determine if fields contain a date field
     *
     * @param  array $fields
     * @return mixed
     */
    public function hasDateField($fields)
    {
        $field = null;

        foreach ($fields as $f) {
            if (strtolower($f) == 'publish') {
                $field = 'publish';
            } else if (strtolower($f) == 'published') {
                $field = 'published';
            } else if (strtolower($f) == 'created') {
                $field = 'created';
            } else if (stripos($f, 'date') !== false) {
                $field = strtolower($f);
            }
        }

        return $field;
    }

    /**
     * Determine if fields contain a link field
     *
     * @param  array $fields
     * @return mixed
     */
    public function hasLinkField($fields)
    {
        $field = null;

        foreach ($fields as $f) {
            if (strtolower($f) == 'link') {
                $field = 'link';
            } else if (strtolower($f) == 'url') {
                $field = 'url';
            } else if (strtolower($f) == 'uri') {
                $field = 'uri';
            } else if (stripos($f, 'link') !== false) {
                $field = strtolower($f);
            } else if (stripos($f, 'url') !== false) {
                $field = strtolower($f);
            } else if (stripos($f, 'uri') !== false) {
                $field = strtolower($f);
            }
        }

        return $field;
    }

    /**
     * Get models
     *
     * @return array
     */
    protected function getModels()
    {
        $models = [];

        // Get new ones
        foreach ($_POST as $key => $value) {
            if ((strpos($key, 'model_') !== false) && (strpos($key, 'model_type_') === false) && ($value != '----')) {
                $id        = substr($key, 6);
                $typeField = null;
                $typeValue = null;

                if ($_POST['model_type_' . $id] != '----') {
                    $type = explode('|', $_POST['model_type_' . $id]);
                    $typeField = $type[0];
                    $typeValue = $type[1];
                }

                $models[] = [
                    'model'      => $value,
                    'type_field' => $typeField,
                    'type_value' => $typeValue
                ];
            }
        }

        return $models;
    }

}
