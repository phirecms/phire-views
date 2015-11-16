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
            $this->data = array_merge($this->data, $field->getColumns());
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
            'name' => $fields['name']
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
            $view->name = $fields['name'];
            $view->save();

            $this->data = array_merge($this->data, $view->getColumns());
        }
    }

    /**
     * Remove a view
     *
     * @param  array $fields
     * @param  array $config
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

}
