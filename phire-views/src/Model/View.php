<?php

namespace Phire\Views\Model;

use Phire\Views\Table;
use Phire\Model\AbstractModel;

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
        $field = Table\Views::findById($id);
        if (isset($field->id)) {
            $data = $field->getColumns();
            if (!empty($data['group_fields'])) {
                $data['group_fields'] = explode('|', $data['group_fields']);
            }
            if (!empty($data['single_fields'])) {
                $data['single_fields'] = explode('|', $data['single_fields']);
            }
            $this->data = array_merge($this->data, $data);
        }
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
